<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\CompleteMobileJobRequest;
use App\Http\Requests\Api\Mobile\StoreMobileLocationRequest;
use App\Http\Requests\Api\Mobile\UpdateMobileJobStatusRequest;
use App\Http\Requests\Api\Mobile\UploadMobileEvidenceRequest;
use App\Http\Resources\Api\EvidenceFileResource;
use App\Http\Resources\Api\Mobile\MobileJobResource;
use App\Models\DispatchAssignment;
use App\Services\Mobile\MobileJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileJobController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $assignments = DispatchAssignment::with(['workOrder.asset.category'])
            ->where('technician_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success(
            MobileJobResource::collection($assignments)->response()->getData(true),
            'Mobile jobs retrieved'
        );
    }

    public function show(Request $request, DispatchAssignment $assignment, MobileJobService $jobs): JsonResponse
    {
        $jobs->assertAssignedTo($assignment, $request->user());

        $assignment->load(['workOrder.asset.category', 'technician', 'assignedBy']);

        return $this->success(new MobileJobResource($assignment), 'Mobile job retrieved');
    }

    public function accept(Request $request, DispatchAssignment $assignment, MobileJobService $jobs): JsonResponse
    {
        $assignment = $jobs->accept($assignment, $request->user());

        return $this->success(new MobileJobResource($assignment), 'Job accepted');
    }

    public function status(
        UpdateMobileJobStatusRequest $request,
        DispatchAssignment $assignment,
        MobileJobService $jobs
    ): JsonResponse {
        $assignment = $jobs->updateStatus(
            $assignment,
            $request->user(),
            $request->validated('status'),
            $request->validated('notes')
        );

        return $this->success(new MobileJobResource($assignment), 'Job status updated');
    }

    public function location(
        StoreMobileLocationRequest $request,
        DispatchAssignment $assignment,
        MobileJobService $jobs
    ): JsonResponse {
        $location = $jobs->storeLocation($assignment, $request->user(), $request->validated());

        return $this->created([
            'id' => $location->id,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'accuracy' => $location->accuracy,
            'captured_at' => $location->captured_at?->toISOString(),
        ], 'Technician location saved');
    }

    public function complete(
        CompleteMobileJobRequest $request,
        DispatchAssignment $assignment,
        MobileJobService $jobs
    ): JsonResponse {
        $assignment = $jobs->complete(
            $assignment,
            $request->user(),
            $request->validated('notes'),
            $request->validated('condition_score')
        );

        return $this->success(new MobileJobResource($assignment), 'Job completed');
    }

    public function evidence(
        UploadMobileEvidenceRequest $request,
        DispatchAssignment $assignment,
        MobileJobService $jobs
    ): JsonResponse {
        $evidence = $jobs->uploadEvidence(
            $assignment,
            $request->user(),
            $request->evidenceFiles(),
            $request->validated('notes')
        );

        return $this->created(EvidenceFileResource::collection($evidence), 'Mobile evidence uploaded');
    }
}
