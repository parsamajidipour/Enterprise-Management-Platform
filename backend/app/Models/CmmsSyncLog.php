<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmmsSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction',
        'action',
        'external_id',
        'local_type',
        'local_id',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
