<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Enums\UploadStatusEnum;

class Upload extends Model
{
    protected $fillable = [
        'name',
        'extension',
        'path',
        'status',
        'uploaded_at'
    ];

    protected $casts = [
        'status' => UploadStatusEnum::class,
        'uploaded_at' => 'datetime'
    ];
}
