<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dispatch\AssignWorkOrderRequest;
use App\Http\Requests\Api\Dispatch\CancelDispatchAssignmentRequest;
use App\Http\Resources\Api\DispatchAssignmentResource;
use App\Http\Resources\Api\TechnicianResource;
use App\Models\DispatchAssignment;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\DispatchAssignmentService;
use App\Services\DispatchRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    use ApiResponses;

    public function assignments(Request $request): JsonResponse
    {
        $assignments = DispatchAssignment::with(['workOrder.asset', 'technician', 'assignedBy'])
            ->when($request->status, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success(
            DispatchAssignmentResource::collection($assignments)->response()->getData(true),
            'Dispatch assignments retrieved'
        );
    }

    public function recommendations(WorkOrder $workOrder, DispatchRecommendationService $recommendations): JsonResponse
    {
        $data = $recommendations->recommendFor($workOrder)->map(fn(array $item) => [
            'technician' => new TechnicianResource($item['technician']),
            'availability' => $item['availability'],
            'distance_km' => $item['distance_km'],
            'active_jobs_count' => $item['active_jobs_count'],
            'score' => $item['score'],
            'reason' => $item['reason'],
        ]);

        return $this->success($data, 'Dispatch recommendations retrieved');
    }

    public function assign(
        AssignWorkOrderRequest $request,
        WorkOrder $workOrder,
        DispatchAssignmentService $assignments
    ): JsonResponse {
        $technician = User::with('technicianProfile')->findOrFail($request->validated('technician_id'));

        $assignment = $assignments->assign(
            $workOrder,
            $technician,
            $request->user(),
            $request->validated('notes')
        );

        return $this->created(new DispatchAssignmentResource($assignment), 'Work order assigned');
    }

    public function cancel(
        CancelDispatchAssignmentRequest $request,
        DispatchAssignment $assignment,
        DispatchAssignmentService $assignments
    ): JsonResponse {
        $assignment = $assignments->cancel($assignment, $request->user(), $request->validated('notes'));

        return $this->success(new DispatchAssignmentResource($assignment), 'Dispatch assignment cancelled');
    }
}
