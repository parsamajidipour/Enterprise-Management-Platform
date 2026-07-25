<?php

namespace App\Services;

use App\Models\DispatchAssignment;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;

class DispatchRecommendationService
{
    public function recommendFor(WorkOrder $workOrder): Collection
    {
        $technicians = User::role('technician')
            ->where('is_active', true)
            ->whereHas('technicianProfile', fn($query) => $query->where('is_active', true))
            ->with(['technicianProfile', 'latestTechnicianLocation'])
            ->withCount([
                'dispatchAssignments as active_jobs_count' => fn($query) => $query->whereIn('status', DispatchAssignment::ACTIVE_STATUSES),
            ])
            ->get();

        $hasCoordinates = $workOrder->latitude !== null && $workOrder->longitude !== null;

        return $technicians
            ->map(fn(User $technician) => $this->scoreTechnician($technician, $workOrder, $hasCoordinates))
            ->sortBy([
                ['score', 'desc'],
                ['active_jobs_count', 'asc'],
                ['distance_km', 'asc'],
            ])
            ->values();
    }

    private function scoreTechnician(User $technician, WorkOrder $workOrder, bool $hasCoordinates): array
    {
        $activeJobsCount = (int) $technician->active_jobs_count;
        $available = $activeJobsCount === 0;
        $distanceKm = null;

        if ($hasCoordinates && $technician->latestTechnicianLocation) {
            $distanceKm = $this->haversineDistanceKm(
                (float) $workOrder->latitude,
                (float) $workOrder->longitude,
                (float) $technician->latestTechnicianLocation->latitude,
                (float) $technician->latestTechnicianLocation->longitude,
            );
        }

        $priorityDistanceWeight = $workOrder->priority === 'critical' ? 18 : 10;
        $score = 0;
        $score += $available ? 1000 : 250;
        $score -= $activeJobsCount * 75;

        if ($distanceKm !== null) {
            $score -= $distanceKm * $priorityDistanceWeight;
        }

        return [
            'technician' => $technician,
            'availability' => $available ? 'available' : 'busy',
            'distance_km' => $distanceKm !== null ? round($distanceKm, 2) : null,
            'active_jobs_count' => $activeJobsCount,
            'score' => round($score, 2),
            'reason' => $this->reason($available, $activeJobsCount, $distanceKm),
        ];
    }

    private function haversineDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadiusKm = 6371;

        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($lngDelta / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function reason(bool $available, int $activeJobsCount, ?float $distanceKm): string
    {
        $availability = $available ? 'available technician' : 'busy technician';
        $distance = $distanceKm !== null ? ', distance calculated from latest GPS' : ', no work order coordinates available';

        return $availability . ', active jobs: ' . $activeJobsCount . $distance;
    }
}
