<?php

namespace App\Jobs;

use App\Imports\FileImport;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

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
            Excel::import(new FileImport($file), $file->path);
        }
    }
}
