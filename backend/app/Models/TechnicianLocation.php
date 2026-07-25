<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_id',
        'latitude',
        'longitude',
        'accuracy',
        'captured_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
        'captured_at' => 'datetime',
    ];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
