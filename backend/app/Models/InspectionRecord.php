<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inspection_form_id',
        'asset_id',
        'work_order_id',
        'inspector_id',
        'status',
        'notes',
        'inspected_at',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
    ];

    public function inspectionForm()
    {
        return $this->belongsTo(InspectionForm::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function answers()
    {
        return $this->hasMany(InspectionAnswer::class);
    }

    public function defects()
    {
        return $this->hasMany(Defect::class);
    }

    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class);
    }
}
