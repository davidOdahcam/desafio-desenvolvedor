<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\FileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessFileImport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $fileId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($file = File::find($this->fileId)) {
            (new FileService)->importFile($file);
        }
    }
}
