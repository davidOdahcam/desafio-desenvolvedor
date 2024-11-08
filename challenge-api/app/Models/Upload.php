<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Enums\UploadStatusEnum;
use MongoDB\Laravel\Relations\HasMany;

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

    public function updateStatus(UploadStatusEnum $status)
    {
        $this->status = $status;
        $this->save();
    }

    public function records(): HasMany
    {
        return $this->hasMany(FileRecord::class);
    }
}
