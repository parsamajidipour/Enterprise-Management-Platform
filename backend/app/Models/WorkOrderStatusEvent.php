<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderStatusEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'from_status',
        'to_status',
        'actor_id',
        'source',
        'notes',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
