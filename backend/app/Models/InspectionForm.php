<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'asset_category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function fields()
    {
        return $this->hasMany(InspectionFormField::class)->orderBy('order');
    }

    public function inspectionRecords()
    {
        return $this->hasMany(InspectionRecord::class);
    }
}
