<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_category_id',
        'name',
        'code',
        'description',
        'location',
        'status',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function inspectionRecords()
    {
        return $this->hasMany(InspectionRecord::class);
    }
}
