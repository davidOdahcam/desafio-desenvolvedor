<?php

namespace App\Enums;

enum UploadStatusEnum: string {
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case UPLOADED = 'uploaded';
}
