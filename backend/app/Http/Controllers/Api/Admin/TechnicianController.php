<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TechnicianAvailabilityResource;
use App\Http\Resources\Api\TechnicianResource;
use App\Http\Resources\Api\TechnicianStatusResource;
use App\Models\DispatchAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $technicians = User::role('technician')
            ->with(['technicianProfile', 'latestTechnicianLocation'])
            ->withCount([
                'dispatchAssignments as active_jobs_count' => fn($query) => $query->whereIn('status', DispatchAssignment::ACTIVE_STATUSES),
            ])
            ->when($request->boolean('active_only'), fn($query) => $query->where('is_active', true))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->success(TechnicianResource::collection($technicians)->response()->getData(true), 'Technicians retrieved');
    }

    public function availability(Request $request): JsonResponse
    {
        $technicians = User::role('technician')
            ->where('is_active', true)
            ->whereHas('technicianProfile', fn($query) => $query->where('is_active', true))
            ->with(['technicianProfile', 'latestTechnicianLocation'])
            ->withCount([
                'dispatchAssignments as active_jobs_count' => fn($query) => $query->whereIn('status', DispatchAssignment::ACTIVE_STATUSES),
            ])
            ->orderBy('name')
            ->get();

        return $this->success(TechnicianAvailabilityResource::collection($technicians), 'Technician availability retrieved');
    }

    public function status(): JsonResponse
    {
        $technicians = User::role('technician')
            ->with([
                'technicianProfile',
                'latestTechnicianLocation',
                'latestActiveAssignment.workOrder',
                'latestCompletedAssignment.workOrder',
            ])
            ->orderBy('name')
            ->get();

        return $this->success(TechnicianStatusResource::collection($technicians), 'Technician status retrieved');
    }
}
