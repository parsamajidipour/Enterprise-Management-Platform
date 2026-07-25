<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_record_id',
        'uploader_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function inspectionRecord()
    {
        return $this->belongsTo(InspectionRecord::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
