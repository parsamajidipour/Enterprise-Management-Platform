<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function workOrdersCreated()
    {
        return $this->hasMany(WorkOrder::class, 'created_by');
    }

    public function workOrdersAssigned()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_to');
    }

    public function inspectionRecords()
    {
        return $this->hasMany(InspectionRecord::class, 'inspector_id');
    }

    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class);
    }

    public function technicianLocations()
    {
        return $this->hasMany(TechnicianLocation::class, 'technician_id');
    }

    public function latestTechnicianLocation()
    {
        return $this->hasOne(TechnicianLocation::class, 'technician_id')->latestOfMany('captured_at');
    }

    public function dispatchAssignments()
    {
        return $this->hasMany(DispatchAssignment::class, 'technician_id');
    }

    public function activeDispatchAssignments()
    {
        return $this->dispatchAssignments()->whereIn('status', DispatchAssignment::ACTIVE_STATUSES);
    }

    public function latestActiveAssignment()
    {
        return $this->hasOne(DispatchAssignment::class, 'technician_id')
            ->whereIn('status', DispatchAssignment::ACTIVE_STATUSES)
            ->latestOfMany();
    }

    public function latestCompletedAssignment()
    {
        return $this->hasOne(DispatchAssignment::class, 'technician_id')
            ->where('status', 'completed')
            ->latestOfMany('completed_at');
    }
}
