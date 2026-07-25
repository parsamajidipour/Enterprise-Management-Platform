<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_record_id',
        'inspection_form_field_id',
        'severity',
        'description',
        'status',
    ];

    public function inspectionRecord()
    {
        return $this->belongsTo(InspectionRecord::class);
    }

    public function field()
    {
        return $this->belongsTo(InspectionFormField::class, 'inspection_form_field_id');
    }
}
