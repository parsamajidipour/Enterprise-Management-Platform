<?php

namespace App\Http\Requests\Api\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,pending_dispatch,assigned,accepted,on_the_way,arrived,in_progress,completed,cancelled,failed_sync,synced_to_cmms'],
        ];
    }
}
