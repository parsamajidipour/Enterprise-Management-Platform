<?php

namespace App\Http\Requests\Api\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id'    => ['sometimes', 'integer', 'exists:assets,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['sometimes', 'in:pending,pending_dispatch,assigned,accepted,on_the_way,arrived,in_progress,completed,cancelled,failed_sync,synced_to_cmms'],
            'priority'    => ['sometimes', 'in:low,medium,high,critical'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'scheduled_at'=> ['nullable', 'date'],
        ];
    }
}
