<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class UploadService {
    public function processFile(UploadedFile $file, string $filename = null) {
        $extension = $file->getClientOriginalExtension();

        switch ($extension) {
            case 'zip':
                $this->processZipFile($file);
                break;
            case 'xlsx':
                $this->processCsvFile($file);

        }
        if ($extension === 'zip') {
            $this->processZipFile($file);
        }
    }

    private function processZipFile(UploadedFile $uploadedFile): void {
        $uniqueId = Str::uuid()->toString();
        $tempFolder = Storage::disk('temp');

        $zip = new ZipArchive;

        if ($zip->open($uploadedFile->getRealPath()) === true) {
            $tempFolder->makeDirectory($uniqueId);

            $zip->extractTo($tempFolder->path($uniqueId));
            $zip->close();

            $files = $tempFolder->files($uniqueId);

            throw_if(count($files) === 0, new \Exception('File not found'));

            $filePath = $tempFolder->path($files[0]);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            $permanentFilePath = $this->moveFileToPermanentStorage("temp/{$files[0]}");

            if ($extension === 'csv') {
                $this->processCsvFile($permanentFilePath);
            } elseif ($extension === 'xlsx') {
                $this->processExcelFile($permanentFilePath);
            } else {
                throw new \Exception('The ZIP file must have a CSV or Excel file');
            }

            $tempFolder->deleteDirectory($uniqueId);
        }

        throw new \Exception('Error');
    }

    private function processCsvFile(string $filePath) {
        // TODO: JOB
    }

    private function processExcelFile(string $filePath) {
        // TODO: JOB
    }

    private function moveFileToPermanentStorage(string $filePath) {
        $filename = basename($filePath);
        $permanentPath = "uploads/{$filename}";
        Storage::move($filePath, $permanentPath);

        return "app/{$permanentPath}";
    }
}
