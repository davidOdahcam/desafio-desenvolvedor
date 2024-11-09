<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Enums\FileStatusEnum;
use MongoDB\Laravel\Relations\HasMany;

class File extends Model
{
    protected $fillable = [
        'name',
        'extension',
        'path',
        'status',
    ];

    protected $casts = [
        'status' => FileStatusEnum::class,
    ];

    public function updateStatus(FileStatusEnum $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function records(): HasMany
    {
        return $this->hasMany(FileRecord::class);
    }
}
