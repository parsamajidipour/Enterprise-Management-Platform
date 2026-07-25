<?php

namespace App\Services\Cmms;

class FakeCmmsDataStore
{
    public function health(): array
    {
        return [
            'system' => 'fake-cmms',
            'status' => 'online',
            'mode' => 'demo-only',
            'authenticated' => false,
            'checked_at' => now()->toISOString(),
        ];
    }

    public function workOrders(): array
    {
        $generatedId = 'CMMS-DEMO-' . now()->format('YmdHis');

        return [
            [
                'external_id' => 'CMMS-OUT-1001',
                'title' => 'Feeder outage investigation',
                'description' => 'Investigate reported outage on feeder FDR-11 and restore supply.',
                'priority' => 'critical',
                'outage_type' => 'feeder_outage',
                'location_name' => 'Muscat Grid Station',
                'latitude' => 23.5902,
                'longitude' => 58.4070,
                'asset_code' => 'ELEC-MDP-001',
                'reported_at' => now()->subHours(3)->toISOString(),
                'status' => 'open',
            ],
            [
                'external_id' => 'CMMS-OUT-1002',
                'title' => 'Pump house electrical fault',
                'description' => 'Cooling pump electrical supply trip requires field electrician validation.',
                'priority' => 'high',
                'outage_type' => 'equipment_trip',
                'location_name' => 'Pump House B',
                'latitude' => 23.6150,
                'longitude' => 58.5910,
                'asset_code' => 'MECH-PMP-201',
                'reported_at' => now()->subHours(2)->toISOString(),
                'status' => 'open',
            ],
            [
                'external_id' => 'CMMS-OUT-1003',
                'title' => 'Generator room alarm follow-up',
                'description' => 'Emergency generator alarm received from CMMS outage console.',
                'priority' => 'medium',
                'outage_type' => 'alarm_follow_up',
                'location_name' => 'Generator Room, Basement',
                'latitude' => 23.5081,
                'longitude' => 58.4986,
                'asset_code' => 'MECH-GEN-101',
                'reported_at' => now()->subMinutes(90)->toISOString(),
                'status' => 'open',
            ],
            [
                'external_id' => $generatedId,
                'title' => 'Demo field dispatch request',
                'description' => 'Generated demo CMMS job for dispatcher assignment testing.',
                'priority' => 'high',
                'outage_type' => 'demo_dispatch',
                'location_name' => 'Demo Site',
                'latitude' => 23.5902,
                'longitude' => 58.4070,
                'asset_code' => 'ELEC-MDP-001',
                'reported_at' => now()->toISOString(),
                'status' => 'open',
            ],
        ];
    }

    public function workOrder(string $externalId): ?array
    {
        foreach ($this->workOrders() as $workOrder) {
            if ($workOrder['external_id'] === $externalId) {
                return $workOrder;
            }
        }

        return null;
    }

    public function pushStatus(string $externalId, string $status, array $payload = []): array
    {
        return [
            'external_id' => $externalId,
            'accepted' => true,
            'status' => $status,
            'received_payload' => $payload,
            'updated_at' => now()->toISOString(),
        ];
    }

    public function pushCompletion(string $externalId, array $payload = []): array
    {
        return $this->pushStatus($externalId, 'completed', $payload);
    }
}
