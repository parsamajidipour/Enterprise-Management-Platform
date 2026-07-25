<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CmmsSyncLogResource;
use App\Models\CmmsSyncLog;
use App\Services\Cmms\CmmsImportService;
use App\Services\Cmms\Contracts\CmmsAdapterInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmmsController extends Controller
{
    use ApiResponses;

    public function health(CmmsAdapterInterface $adapter): JsonResponse
    {
        return $this->success($adapter->healthCheck(), 'CMMS health checked');
    }

    public function syncNow(CmmsImportService $importer): JsonResponse
    {
        return $this->success($importer->importOpenWorkOrders(), 'CMMS sync completed');
    }

    public function syncLogs(Request $request): JsonResponse
    {
        $logs = CmmsSyncLog::query()
            ->when($request->direction, fn($query, $direction) => $query->where('direction', $direction))
            ->when($request->action, fn($query, $action) => $query->where('action', $action))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->success(CmmsSyncLogResource::collection($logs)->response()->getData(true), 'CMMS sync logs retrieved');
    }
}
