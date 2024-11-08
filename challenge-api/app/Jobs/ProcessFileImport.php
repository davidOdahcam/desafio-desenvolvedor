<?php

namespace App\Jobs;

use App\Enums\UploadStatusEnum;
use App\Imports\FileImport;
use App\Models\Upload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessFileImport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $uploadId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($upload = Upload::find($this->uploadId)) {
            Excel::import(new FileImport($upload), $upload->path);
        }
    }
}
