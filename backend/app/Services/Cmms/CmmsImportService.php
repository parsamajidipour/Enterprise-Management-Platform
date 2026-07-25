<?php

namespace App\Services\Cmms;

use App\Models\Asset;
use App\Models\CmmsSyncLog;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\Cmms\Contracts\CmmsAdapterInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class CmmsImportService
{
    public function __construct(private readonly CmmsAdapterInterface $adapter)
    {
    }

    public function importOpenWorkOrders(): array
    {
        $items = $this->adapter->fetchWorkOrders();
        $created = 0;
        $updated = 0;
        $importedIds = [];

        foreach ($items as $item) {
            try {
                $result = $this->importOne($item);
                $created += $result['created'] ? 1 : 0;
                $updated += $result['created'] ? 0 : 1;
                $importedIds[] = $result['work_order']->id;
            } catch (Throwable $error) {
                $this->log('inbound', 'import_work_order', $item['external_id'] ?? null, 'failed', $item, null, $error->getMessage());
            }
        }

        $summary = [
            'fetched' => count($items),
            'created' => $created,
            'updated' => $updated,
            'imported_ids' => $importedIds,
        ];

        $this->log('inbound', 'import_work_orders', null, 'success', ['count' => count($items)], $summary);

        return $summary;
    }

    private function importOne(array $item): array
    {
        return DB::transaction(function () use ($item) {
            $asset = Asset::where('code', $item['asset_code'] ?? '')->firstOrFail();
            $creator = User::role('admin')->first() ?? User::firstOrFail();
            $existing = WorkOrder::where('external_id', $item['external_id'])->first();

            $workOrder = WorkOrder::updateOrCreate(
                ['external_id' => $item['external_id']],
                [
                    'asset_id' => $asset->id,
                    'created_by' => $existing?->created_by ?? $creator->id,
                    'assigned_to' => $existing?->assigned_to,
                    'source' => 'cmms',
                    'external_reference' => $item['external_id'],
                    'outage_type' => $item['outage_type'] ?? null,
                    'reported_at' => isset($item['reported_at']) ? Carbon::parse($item['reported_at']) : null,
                    'cmms_status' => $item['status'] ?? null,
                    'title' => $item['title'],
                    'description' => trim(($item['description'] ?? '') . "\n\nLocation: " . ($item['location_name'] ?? 'Unknown')),
                    'status' => $existing?->status && $existing->status !== 'pending_dispatch' ? $existing->status : 'pending_dispatch',
                    'priority' => $item['priority'] ?? 'medium',
                    'latitude' => $item['latitude'] ?? null,
                    'longitude' => $item['longitude'] ?? null,
                    'scheduled_at' => null,
                    'completed_at' => ($item['status'] ?? null) === 'open' && (!$existing || $existing->status === 'pending_dispatch')
                        ? null
                        : $existing?->completed_at,
                ]
            );

            $this->log(
                'inbound',
                'import_work_order',
                $item['external_id'],
                'success',
                $item,
                ['local_id' => $workOrder->id, 'created' => !$existing],
                null,
                WorkOrder::class,
                $workOrder->id
            );

            return [
                'work_order' => $workOrder,
                'created' => !$existing,
            ];
        });
    }

    private function log(
        string $direction,
        string $action,
        ?string $externalId,
        string $status,
        ?array $request,
        ?array $response,
        ?string $error = null,
        ?string $localType = null,
        ?int $localId = null,
    ): void {
        CmmsSyncLog::create([
            'direction' => $direction,
            'action' => $action,
            'external_id' => $externalId,
            'local_type' => $localType,
            'local_id' => $localId,
            'status' => $status,
            'request_payload' => $request,
            'response_payload' => $response,
            'error_message' => $error,
        ]);
    }
}
