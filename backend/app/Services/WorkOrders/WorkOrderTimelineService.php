<?php

namespace App\Services\WorkOrders;

use App\Models\CmmsSyncLog;
use App\Models\DispatchAssignment;
use App\Models\EvidenceFile;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatusEvent;
use Illuminate\Support\Collection;

class WorkOrderTimelineService
{
    public function forWorkOrder(WorkOrder $workOrder): array
    {
        $items = collect()
            ->push($this->createdItem($workOrder))
            ->merge($this->statusEventItems($workOrder))
            ->merge($this->assignmentItems($workOrder))
            ->merge($this->evidenceItems($workOrder))
            ->merge($this->cmmsLogItems($workOrder))
            ->filter(fn(array $item) => $item['timestamp'] !== null)
            ->sortBy('timestamp')
            ->values();

        return $items->all();
    }

    private function createdItem(WorkOrder $workOrder): array
    {
        $imported = $workOrder->source === 'cmms';

        return [
            'id' => 'work_order:' . $workOrder->id,
            'type' => $imported ? 'cmms_imported' : 'work_order_created',
            'title' => $imported ? 'Imported from CMMS' : 'Work order created',
            'description' => $imported
                ? 'External work order ' . $workOrder->external_id . ' was mapped into dispatch.'
                : 'Work order was created locally.',
            'actor' => $imported ? 'CMMS Adapter' : $workOrder->creator?->name,
            'status' => $workOrder->status,
            'timestamp' => $workOrder->created_at?->toISOString(),
            'metadata' => [
                'external_id' => $workOrder->external_id,
                'source' => $workOrder->source,
                'priority' => $workOrder->priority,
                'cmms_status' => $workOrder->cmms_status,
            ],
        ];
    }

    private function statusEventItems(WorkOrder $workOrder): Collection
    {
        return WorkOrderStatusEvent::with('actor')
            ->where('work_order_id', $workOrder->id)
            ->oldest()
            ->get()
            ->map(fn(WorkOrderStatusEvent $event) => [
                'id' => 'status_event:' . $event->id,
                'type' => 'status_changed',
                'title' => 'Status changed',
                'description' => trim(($event->from_status ?? 'none') . ' -> ' . $event->to_status),
                'actor' => $event->actor?->name ?? $event->source,
                'status' => $event->to_status,
                'timestamp' => $event->created_at?->toISOString(),
                'metadata' => [
                    'from_status' => $event->from_status,
                    'to_status' => $event->to_status,
                    'source' => $event->source,
                    'notes' => $event->notes,
                ],
            ]);
    }

    private function assignmentItems(WorkOrder $workOrder): Collection
    {
        return DispatchAssignment::with(['technician', 'assignedBy'])
            ->where('work_order_id', $workOrder->id)
            ->oldest()
            ->get()
            ->flatMap(function (DispatchAssignment $assignment) {
                $items = collect([
                    [
                        'id' => 'assignment:' . $assignment->id . ':assigned',
                        'type' => 'technician_assigned',
                        'title' => 'Technician assigned',
                        'description' => ($assignment->technician?->name ?? 'Technician') . ' was assigned to the work order.',
                        'actor' => $assignment->assignedBy?->name,
                        'status' => $assignment->status,
                        'timestamp' => $assignment->assigned_at?->toISOString() ?? $assignment->created_at?->toISOString(),
                        'metadata' => [
                            'assignment_id' => $assignment->id,
                            'technician_id' => $assignment->technician_id,
                            'technician_name' => $assignment->technician?->name,
                            'notes' => $assignment->notes,
                        ],
                    ],
                ]);

                if ($assignment->cancelled_at) {
                    $items->push([
                        'id' => 'assignment:' . $assignment->id . ':cancelled',
                        'type' => 'assignment_cancelled',
                        'title' => 'Assignment cancelled',
                        'description' => 'Assignment was cancelled and the work order returned to dispatch.',
                        'actor' => $assignment->assignedBy?->name,
                        'status' => $assignment->status,
                        'timestamp' => $assignment->cancelled_at?->toISOString(),
                        'metadata' => [
                            'assignment_id' => $assignment->id,
                            'technician_id' => $assignment->technician_id,
                            'technician_name' => $assignment->technician?->name,
                            'notes' => $assignment->notes,
                        ],
                    ]);
                }

                return $items;
            });
    }

    private function cmmsLogItems(WorkOrder $workOrder): Collection
    {
        return CmmsSyncLog::query()
            ->where(function ($query) use ($workOrder) {
                $query->where(function ($inner) use ($workOrder) {
                    $inner->where('local_type', WorkOrder::class)
                        ->where('local_id', $workOrder->id);
                });

                if ($workOrder->external_id) {
                    $query->orWhere('external_id', $workOrder->external_id);
                }
            })
            ->oldest()
            ->get()
            ->map(function (CmmsSyncLog $log) {
                $type = match ($log->action) {
                    'import_work_order', 'import_work_orders' => 'cmms_imported',
                    'push_completion' => 'cmms_push_completion',
                    'push_status' => 'cmms_push_status',
                    default => 'cmms_sync',
                };

                return [
                    'id' => 'cmms_log:' . $log->id,
                    'type' => $type,
                    'title' => $this->titleForCmmsLog($log),
                    'description' => $log->error_message ?? $this->descriptionForCmmsLog($log),
                    'actor' => $log->direction === 'inbound' ? 'CMMS Adapter' : 'Platform Backend',
                    'status' => $log->status,
                    'timestamp' => $log->created_at?->toISOString(),
                    'metadata' => [
                        'direction' => $log->direction,
                        'action' => $log->action,
                        'external_id' => $log->external_id,
                        'request_payload' => $log->request_payload,
                        'response_payload' => $log->response_payload,
                    ],
                ];
            });
    }

    private function evidenceItems(WorkOrder $workOrder): Collection
    {
        return EvidenceFile::with(['inspectionRecord', 'uploader'])
            ->whereHas('inspectionRecord', fn($query) => $query->where('work_order_id', $workOrder->id))
            ->oldest()
            ->get()
            ->map(fn(EvidenceFile $evidence) => [
                'id' => 'evidence:' . $evidence->id,
                'type' => 'evidence_uploaded',
                'title' => 'Evidence uploaded',
                'description' => $evidence->file_name . ' was uploaded for this work order.',
                'actor' => $evidence->uploader?->name,
                'status' => 'uploaded',
                'timestamp' => $evidence->created_at?->toISOString(),
                'metadata' => [
                    'evidence_id' => $evidence->id,
                    'inspection_record_id' => $evidence->inspection_record_id,
                    'file_name' => $evidence->file_name,
                    'file_type' => $evidence->file_type,
                    'file_size' => $evidence->file_size,
                ],
            ]);
    }

    private function titleForCmmsLog(CmmsSyncLog $log): string
    {
        return match ($log->action) {
            'import_work_order', 'import_work_orders' => 'CMMS import',
            'push_status' => 'Pushed status to CMMS',
            'push_completion' => 'Pushed completion to CMMS',
            default => 'CMMS sync',
        };
    }

    private function descriptionForCmmsLog(CmmsSyncLog $log): string
    {
        if ($log->action === 'push_status') {
            $status = $log->request_payload['status'] ?? 'unknown';
            return 'Status "' . $status . '" was sent to CMMS.';
        }

        if ($log->action === 'push_completion') {
            return 'Completion payload was sent to CMMS.';
        }

        return 'CMMS sync log recorded for ' . ($log->external_id ?? 'batch sync') . '.';
    }
}
