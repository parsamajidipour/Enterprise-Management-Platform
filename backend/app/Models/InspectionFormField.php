<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_form_id',
        'label',
        'field_type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function inspectionForm()
    {
        return $this->belongsTo(InspectionForm::class);
    }

    public function answers()
    {
        return $this->hasMany(InspectionAnswer::class);
    }
}
