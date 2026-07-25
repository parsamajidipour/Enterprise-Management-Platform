<?php

namespace App\Services\Cmms\Contracts;

interface CmmsAdapterInterface
{
    public function healthCheck(): array;

    public function fetchWorkOrders(): array;

    public function fetchWorkOrder(string $externalId): ?array;

    public function pushStatus(string $externalId, string $status, array $payload = []): array;

    public function pushCompletion(string $externalId, array $payload = []): array;
}
