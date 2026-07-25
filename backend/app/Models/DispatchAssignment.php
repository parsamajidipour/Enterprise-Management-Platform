<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchAssignment extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'created';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        'created',
        'sent_to_technician',
        'accepted',
        'on_the_way',
        'arrived',
        'in_progress',
    ];

    protected $fillable = [
        'work_order_id',
        'technician_id',
        'assigned_by',
        'status',
        'assigned_at',
        'accepted_at',
        'arrived_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }
}
