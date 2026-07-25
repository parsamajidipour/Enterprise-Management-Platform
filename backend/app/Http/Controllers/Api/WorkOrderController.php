<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Requests\Api\WorkOrder\StoreWorkOrderRequest;
use App\Http\Requests\Api\WorkOrder\UpdateWorkOrderRequest;
use App\Http\Requests\Api\WorkOrder\UpdateStatusRequest;
use App\Http\Resources\Api\WorkOrderResource;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatusEvent;
use App\Services\Cmms\CmmsPushbackService;
use App\Services\WorkOrders\WorkOrderTimelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $workOrders = WorkOrder::with(['asset', 'creator', 'assignee'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->priority, fn($q, $priority) => $q->where('priority', $priority))
            ->when($request->asset_id, fn($q, $id) => $q->where('asset_id', $id))
            ->when($request->assigned_to, fn($q, $id) => $q->where('assigned_to', $id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success(WorkOrderResource::collection($workOrders)->response()->getData(true), 'Work orders retrieved');
    }

    public function store(StoreWorkOrderRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), ['created_by' => $request->user()->id]);
        $workOrder = WorkOrder::create($data);
        $workOrder->load(['asset', 'creator', 'assignee']);

        return $this->created(new WorkOrderResource($workOrder), 'Work order created');
    }

    public function show(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->load(['asset', 'creator', 'assignee', 'inspectionRecords']);

        return $this->success(new WorkOrderResource($workOrder), 'Work order retrieved');
    }

    public function timeline(WorkOrder $workOrder, WorkOrderTimelineService $timeline): JsonResponse
    {
        $workOrder->load('creator');

        return $this->success($timeline->forWorkOrder($workOrder), 'Work order timeline retrieved');
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): JsonResponse
    {
        $workOrder->update($request->validated());
        $workOrder->load(['asset', 'creator', 'assignee']);

        return $this->success(new WorkOrderResource($workOrder), 'Work order updated');
    }

    public function updateStatus(UpdateStatusRequest $request, WorkOrder $workOrder, CmmsPushbackService $cmmsPushback): JsonResponse
    {
        $fromStatus = $workOrder->status;
        $data = ['status' => $request->validated('status')];

        if ($request->validated('status') === 'completed') {
            $data['completed_at'] = now();
        }

        $workOrder->update($data);

        WorkOrderStatusEvent::create([
            'work_order_id' => $workOrder->id,
            'from_status' => $fromStatus,
            'to_status' => $request->validated('status'),
            'actor_id' => $request->user()?->id,
            'source' => 'admin',
        ]);

        $freshWorkOrder = $workOrder->fresh(['asset', 'creator', 'assignee']);
        $cmmsPushback->pushWorkOrderStatus($freshWorkOrder, $request->validated('status'));

        if ($request->validated('status') === 'completed') {
            $cmmsPushback->pushCompletion($freshWorkOrder, [
                'completed_at' => $freshWorkOrder->completed_at?->toISOString(),
            ]);
        }

        return $this->success(new WorkOrderResource($freshWorkOrder), 'Status updated');
    }

    public function destroy(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->delete();

        return $this->success(null, 'Work order deleted');
    }
}
