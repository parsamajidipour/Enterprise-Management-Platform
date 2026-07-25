<?php

namespace App\Http\Resources\Api;

use App\Models\DispatchAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnicianStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $user */
        $user = $this->resource;

        $active    = $user->latestActiveAssignment;
        $completed = $user->latestCompletedAssignment;
        $location  = $user->latestTechnicianLocation;
        $profile   = $user->technicianProfile;

        [$availability, $humanLabel] = $this->resolveStatus($user, $active);

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'employee_code'     => $profile?->employee_code,
            'phone'             => $profile?->phone,
            'skills'            => $profile?->skills ?? [],
            'availability'      => $availability,
            'human_status_label' => $humanLabel,

            'current_assignment' => $active ? [
                'id'           => $active->id,
                'status'       => $active->status,
                'assigned_at'  => $active->assigned_at?->toISOString(),
                'accepted_at'  => $active->accepted_at?->toISOString(),
                'arrived_at'   => $active->arrived_at?->toISOString(),
                'started_at'   => $active->started_at?->toISOString(),
                'completed_at' => $active->completed_at?->toISOString(),
            ] : null,

            'current_job' => $active?->workOrder ? [
                'id'          => $active->workOrder->id,
                'title'       => $active->workOrder->title,
                'priority'    => $active->workOrder->priority,
                'status'      => $active->workOrder->status,
                'external_id' => $active->workOrder->external_id,
                'outage_type' => $active->workOrder->outage_type,
            ] : null,

            'last_completed_job' => $completed?->workOrder ? [
                'id'           => $completed->workOrder->id,
                'title'        => $completed->workOrder->title,
                'completed_at' => $completed->completed_at?->toISOString(),
            ] : null,

            'last_location' => $location ? [
                'latitude'    => $location->latitude,
                'longitude'   => $location->longitude,
                'captured_at' => $location->captured_at?->toISOString(),
            ] : null,

            'last_activity_at' => $this->resolveLastActivityAt($active, $location),
        ];
    }

    private function resolveStatus(\App\Models\User $user, ?DispatchAssignment $active): array
    {
        if (!$user->is_active || !($user->technicianProfile?->is_active)) {
            return ['unavailable', 'Unavailable'];
        }

        if (!$active) {
            return ['available', 'Available now'];
        }

        return match ($active->status) {
            'created', 'sent_to_technician' => ['assigned', 'Assigned'],
            'accepted'                       => ['accepted', 'Accepted'],
            'on_the_way'                     => ['on_the_way', 'On the way'],
            'arrived'                        => ['arrived', 'Arrived on site'],
            'in_progress'                    => ['in_progress', 'Working'],
            default                          => ['assigned', 'Assigned'],
        };
    }

    private function resolveLastActivityAt(?DispatchAssignment $active, mixed $location): ?string
    {
        $candidates = array_filter([
            $active?->started_at,
            $active?->arrived_at,
            $active?->accepted_at,
            $active?->assigned_at,
            $location?->captured_at,
        ]);

        if (empty($candidates)) {
            return null;
        }

        return collect($candidates)->max()->toISOString();
    }
}
