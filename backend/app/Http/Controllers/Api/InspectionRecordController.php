<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Requests\Api\InspectionRecord\StoreInspectionRecordRequest;
use App\Http\Requests\Api\InspectionRecord\UploadEvidenceRequest;
use App\Http\Resources\Api\InspectionRecordResource;
use App\Http\Resources\Api\EvidenceFileResource;
use App\Models\InspectionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionRecordController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $records = InspectionRecord::with(['asset', 'inspectionForm', 'inspector', 'workOrder'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->asset_id, fn($q, $id) => $q->where('asset_id', $id))
            ->when($request->inspector_id, fn($q, $id) => $q->where('inspector_id', $id))
            ->when($request->work_order_id, fn($q, $id) => $q->where('work_order_id', $id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success(InspectionRecordResource::collection($records)->response()->getData(true), 'Inspection records retrieved');
    }

    public function store(StoreInspectionRecordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $answers = $validated['answers'] ?? [];
        unset($validated['answers']);

        $validated['inspector_id'] = $request->user()->id;
        $validated['inspected_at'] = $validated['inspected_at'] ?? now();

        $record = InspectionRecord::create($validated);

        foreach ($answers as $answerData) {
            $record->answers()->create($answerData);
        }

        $record->load(['asset', 'inspectionForm', 'inspector', 'workOrder', 'answers.field']);

        return $this->created(new InspectionRecordResource($record), 'Inspection record created');
    }

    public function show(InspectionRecord $inspectionRecord): JsonResponse
    {
        $inspectionRecord->load([
            'asset',
            'inspectionForm.fields',
            'inspector',
            'workOrder',
            'answers.field',
            'defects',
            'evidenceFiles.uploader',
        ]);

        return $this->success(new InspectionRecordResource($inspectionRecord), 'Inspection record retrieved');
    }

    public function uploadEvidence(UploadEvidenceRequest $request, InspectionRecord $inspectionRecord): JsonResponse
    {
        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store("evidence/{$inspectionRecord->id}", 'public');

            $evidence = $inspectionRecord->evidenceFiles()->create([
                'uploader_id' => $request->user()->id,
                'file_path'   => $path,
                'file_name'   => $file->getClientOriginalName(),
                'file_type'   => $file->getClientMimeType(),
                'file_size'   => $file->getSize(),
            ]);

            $uploaded[] = new EvidenceFileResource($evidence);
        }

        return $this->created($uploaded, 'Evidence uploaded');
    }
}
