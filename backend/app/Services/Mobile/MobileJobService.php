<?php

namespace App\Services\Mobile;

use App\Models\DispatchAssignment;
use App\Models\EvidenceFile;
use App\Models\InspectionForm;
use App\Models\InspectionRecord;
use App\Models\TechnicianLocation;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatusEvent;
use App\Services\Cmms\CmmsPushbackService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileJobService
{
    private const TRANSITIONS = [
        'created' => ['accepted'],
        'sent_to_technician' => ['accepted'],
        'accepted' => ['on_the_way'],
        'on_the_way' => ['arrived'],
        'arrived' => ['in_progress'],
        'in_progress' => ['completed'],
    ];

    public function __construct(private readonly CmmsPushbackService $cmmsPushback)
    {
    }

    public function assertAssignedTo(DispatchAssignment $assignment, User $technician): void
    {
        if ($assignment->technician_id !== $technician->id) {
            throw new AuthorizationException('You can only access your own mobile jobs.');
        }
    }

    public function accept(DispatchAssignment $assignment, User $technician): DispatchAssignment
    {
        $this->assertAssignedTo($assignment, $technician);

        return $this->transition($assignment, $technician, 'accepted');
    }

    public function updateStatus(DispatchAssignment $assignment, User $technician, string $status, ?string $notes = null): DispatchAssignment
    {
        $this->assertAssignedTo($assignment, $technician);

        return $this->transition($assignment, $technician, $status, $notes);
    }

    public function complete(DispatchAssignment $assignment, User $technician, ?string $notes = null, ?float $conditionScore = null): DispatchAssignment
    {
        $this->assertAssignedTo($assignment, $technician);

        return $this->transition($assignment, $technician, 'completed', $notes, [
            'condition_score' => $conditionScore,
        ]);
    }

    public function storeLocation(DispatchAssignment $assignment, User $technician, array $data): TechnicianLocation
    {
        $this->assertAssignedTo($assignment, $technician);

        return TechnicianLocation::create([
            'technician_id' => $technician->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'] ?? null,
            'captured_at' => isset($data['captured_at']) ? Carbon::parse($data['captured_at']) : now(),
        ]);
    }

    /**
     * @param array<int, UploadedFile> $files
     * @return array<int, EvidenceFile>
     */
    public function uploadEvidence(DispatchAssignment $assignment, User $technician, array $files, ?string $notes = null): array
    {
        $this->assertAssignedTo($assignment, $technician);

        $inspectionRecord = $this->inspectionRecordFor($assignment, $technician, $notes);
        $uploaded = [];

        foreach ($files as $file) {
            $path = $file->store("evidence/{$inspectionRecord->id}", 'public');

            $uploaded[] = $inspectionRecord->evidenceFiles()->create([
                'uploader_id' => $technician->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return $uploaded;
    }

    private function transition(
        DispatchAssignment $assignment,
        User $technician,
        string $toStatus,
        ?string $notes = null,
        array $payload = [],
    ): DispatchAssignment {
        $assignment = DB::transaction(function () use ($assignment, $technician, $toStatus, $notes, $payload) {
            $assignment = DispatchAssignment::with('workOrder')
                ->whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertAssignedTo($assignment, $technician);
            $this->ensureTransitionAllowed($assignment->status, $toStatus);

            $workOrder = WorkOrder::whereKey($assignment->work_order_id)->lockForUpdate()->firstOrFail();
            $fromWorkOrderStatus = $workOrder->status;
            $timestampField = $this->timestampFieldFor($toStatus);

            $assignmentData = [
                'status' => $toStatus,
            ];

            if ($notes !== null) {
                $assignmentData['notes'] = $notes;
            }

            if ($timestampField) {
                $assignmentData[$timestampField] = now();
            }

            $assignment->update($assignmentData);

            $workOrderData = [
                'status' => $toStatus,
            ];

            if ($toStatus === 'completed') {
                $workOrderData['completed_at'] = now();
            }

            $workOrder->update($workOrderData);

            $eventNotes = $notes;
            if (($payload['condition_score'] ?? null) !== null) {
                $eventNotes = trim(($eventNotes ? $eventNotes.' ' : '').'Condition score: '.$payload['condition_score']);
            }

            $this->recordStatusEvent($workOrder->fresh(), $fromWorkOrderStatus, $toStatus, $technician, $eventNotes);

            return $assignment->fresh(['workOrder.asset', 'technician', 'assignedBy']);
        });

        if ($toStatus === 'completed') {
            $this->cmmsPushback->pushCompletion($assignment->workOrder, array_filter([
                'assignment_id' => $assignment->id,
                'technician_id' => $technician->id,
                'completed_at' => $assignment->completed_at?->toISOString(),
                'notes' => $notes,
                'condition_score' => $payload['condition_score'] ?? null,
            ], fn($value) => $value !== null));
        } else {
            $this->cmmsPushback->pushWorkOrderStatus($assignment->workOrder, $toStatus, array_filter([
                'assignment_id' => $assignment->id,
                'technician_id' => $technician->id,
                'notes' => $notes,
            ], fn($value) => $value !== null));
        }

        return $assignment;
    }

    private function ensureTransitionAllowed(string $fromStatus, string $toStatus): void
    {
        if (!in_array($toStatus, self::TRANSITIONS[$fromStatus] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot transition assignment from {$fromStatus} to {$toStatus}."],
            ]);
        }
    }

    private function timestampFieldFor(string $status): ?string
    {
        return match ($status) {
            'accepted' => 'accepted_at',
            'arrived' => 'arrived_at',
            'in_progress' => 'started_at',
            'completed' => 'completed_at',
            default => null,
        };
    }

    private function recordStatusEvent(WorkOrder $workOrder, ?string $fromStatus, string $toStatus, User $actor, ?string $notes): void
    {
        WorkOrderStatusEvent::create([
            'work_order_id' => $workOrder->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'actor_id' => $actor->id,
            'source' => 'mobile',
            'notes' => $notes,
        ]);
    }

    private function inspectionRecordFor(DispatchAssignment $assignment, User $technician, ?string $notes): InspectionRecord
    {
        $workOrder = $assignment->workOrder()->with('asset')->firstOrFail();
        $record = InspectionRecord::where('work_order_id', $workOrder->id)
            ->where('inspector_id', $technician->id)
            ->latest()
            ->first();

        if ($record) {
            return $record;
        }

        $form = InspectionForm::where('is_active', true)
            ->where('asset_category_id', $workOrder->asset?->asset_category_id)
            ->first()
            ?? InspectionForm::where('is_active', true)->firstOrFail();

        return InspectionRecord::create([
            'inspection_form_id' => $form->id,
            'asset_id' => $workOrder->asset_id,
            'work_order_id' => $workOrder->id,
            'inspector_id' => $technician->id,
            'status' => 'submitted',
            'notes' => $notes ?? 'Mobile evidence upload',
            'inspected_at' => now(),
        ]);
    }
}
