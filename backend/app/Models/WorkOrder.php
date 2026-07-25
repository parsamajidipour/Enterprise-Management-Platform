<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    public const CLOSED_STATUSES = [
        'completed',
        'synced_to_cmms',
        'cancelled',
        'closed',
    ];

    protected $fillable = [
        'asset_id',
        'created_by',
        'assigned_to',
        'source',
        'external_id',
        'external_reference',
        'outage_type',
        'reported_at',
        'cmms_status',
        'title',
        'description',
        'status',
        'priority',
        'latitude',
        'longitude',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'reported_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function inspectionRecords()
    {
        return $this->hasMany(InspectionRecord::class);
    }

    public function dispatchAssignments()
    {
        return $this->hasMany(DispatchAssignment::class);
    }

    public function statusEvents()
    {
        return $this->hasMany(WorkOrderStatusEvent::class);
    }
}
