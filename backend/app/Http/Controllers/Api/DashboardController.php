<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Models\Asset;
use App\Models\Defect;
use App\Models\InspectionRecord;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponses;

    public function kpis(): JsonResponse
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $underMaintenance = Asset::where('status', 'under_maintenance')->count();

        $totalWorkOrders = WorkOrder::count();
        $pendingWorkOrders = WorkOrder::where('status', 'pending')->count();
        $inProgressWorkOrders = WorkOrder::where('status', 'in_progress')->count();
        $completedWorkOrders = WorkOrder::where('status', 'completed')->count();

        $totalInspections = InspectionRecord::count();
        $draftInspections = InspectionRecord::where('status', 'draft')->count();
        $submittedInspections = InspectionRecord::where('status', 'submitted')->count();

        $openDefects = Defect::where('status', 'open')->count();
        $criticalDefects = Defect::where('severity', 'critical')->where('status', 'open')->count();

        $recentWorkOrders = WorkOrder::with(['asset', 'assignee'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($wo) => [
                'id'       => $wo->id,
                'title'    => $wo->title,
                'status'   => $wo->status,
                'priority' => $wo->priority,
                'asset'    => $wo->asset?->name,
                'assignee' => $wo->assignee?->name,
            ]);

        return $this->success([
            'assets' => [
                'total'             => $totalAssets,
                'active'            => $activeAssets,
                'under_maintenance' => $underMaintenance,
                'inactive'          => $totalAssets - $activeAssets - $underMaintenance,
            ],
            'work_orders' => [
                'total'       => $totalWorkOrders,
                'pending'     => $pendingWorkOrders,
                'in_progress' => $inProgressWorkOrders,
                'completed'   => $completedWorkOrders,
            ],
            'inspections' => [
                'total'     => $totalInspections,
                'draft'     => $draftInspections,
                'submitted' => $submittedInspections,
            ],
            'defects' => [
                'open'     => $openDefects,
                'critical' => $criticalDefects,
            ],
            'recent_work_orders' => $recentWorkOrders,
        ], 'Dashboard KPIs');
    }
}
