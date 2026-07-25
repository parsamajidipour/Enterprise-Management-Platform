<?php

namespace App\Services\Cmms;

use App\Models\CmmsSyncLog;
use App\Models\WorkOrder;
use App\Services\Cmms\Contracts\CmmsAdapterInterface;
use Throwable;

class CmmsPushbackService
{
    public function __construct(private readonly CmmsAdapterInterface $adapter)
    {
    }

    public function pushWorkOrderStatus(WorkOrder $workOrder, string $status, array $payload = []): ?array
    {
        if (!$workOrder->external_id) {
            return null;
        }

        $request = array_merge([
            'local_work_order_id' => $workOrder->id,
            'status' => $status,
            'source' => 'emp-platform',
        ], $payload);

        try {
            $response = $this->adapter->pushStatus($workOrder->external_id, $status, $request);
            $this->log('outbound', 'push_status', $workOrder, 'success', $request, $response);

            return $response;
        } catch (Throwable $error) {
            $this->log('outbound', 'push_status', $workOrder, 'failed', $request, null, $error->getMessage());
            return null;
        }
    }

    public function pushCompletion(WorkOrder $workOrder, array $payload = []): ?array
    {
        if (!$workOrder->external_id) {
            return null;
        }

        try {
            $response = $this->adapter->pushCompletion($workOrder->external_id, $payload);
            $this->log('outbound', 'push_completion', $workOrder, 'success', $payload, $response);

            return $response;
        } catch (Throwable $error) {
            $this->log('outbound', 'push_completion', $workOrder, 'failed', $payload, null, $error->getMessage());
            return null;
        }
    }

    private function log(
        string $direction,
        string $action,
        WorkOrder $workOrder,
        string $status,
        ?array $request,
        ?array $response,
        ?string $error = null,
    ): void {
        CmmsSyncLog::create([
            'direction' => $direction,
            'action' => $action,
            'external_id' => $workOrder->external_id,
            'local_type' => WorkOrder::class,
            'local_id' => $workOrder->id,
            'status' => $status,
            'request_payload' => $request,
            'response_payload' => $response,
            'error_message' => $error,
        ]);
    }
}
