<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Services\Cmms\FakeCmmsDataStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FakeCmmsController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly FakeCmmsDataStore $fakeCmms)
    {
    }

    public function health(): JsonResponse
    {
        return $this->success($this->fakeCmms->health(), 'Fake CMMS is online');
    }

    public function workOrders(): JsonResponse
    {
        return $this->success($this->fakeCmms->workOrders(), 'Fake CMMS work orders retrieved');
    }

    public function workOrder(string $externalId): JsonResponse
    {
        $workOrder = $this->fakeCmms->workOrder($externalId);

        if (!$workOrder) {
            return $this->notFound('Fake CMMS work order not found');
        }

        return $this->success($workOrder, 'Fake CMMS work order retrieved');
    }

    public function updateStatus(Request $request, string $externalId): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
            'payload' => ['nullable', 'array'],
        ]);

        return $this->success(
            $this->fakeCmms->pushStatus($externalId, $data['status'], $data['payload'] ?? []),
            'Fake CMMS status accepted'
        );
    }
}
