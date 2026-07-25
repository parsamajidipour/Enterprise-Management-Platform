<?php

namespace App\Services\Cmms\Adapters;

use App\Services\Cmms\Contracts\CmmsAdapterInterface;
use App\Services\Cmms\FakeCmmsDataStore;

class FakeCmmsAdapter implements CmmsAdapterInterface
{
    public function __construct(private readonly FakeCmmsDataStore $fakeCmms)
    {
    }

    public function healthCheck(): array
    {
        return $this->fakeCmms->health();
    }

    public function fetchWorkOrders(): array
    {
        return $this->fakeCmms->workOrders();
    }

    public function fetchWorkOrder(string $externalId): ?array
    {
        return $this->fakeCmms->workOrder($externalId);
    }

    public function pushStatus(string $externalId, string $status, array $payload = []): array
    {
        return $this->fakeCmms->pushStatus($externalId, $status, $payload);
    }

    public function pushCompletion(string $externalId, array $payload = []): array
    {
        return $this->fakeCmms->pushCompletion($externalId, $payload);
    }
}
