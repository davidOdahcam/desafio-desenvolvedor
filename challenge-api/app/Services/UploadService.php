<?php

namespace App\Services;

use App\Enums\UploadStatusEnum;
use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class UploadService {
    public function processFile(UploadedFile $uploadedFile, string $filename = null) {
        $upload = new Upload([
            'status' => UploadStatusEnum::PENDING
        ]);

        $extension = $uploadedFile->getClientOriginalExtension();

        if ($extension === 'zip') {
            $upload->path = $this->unzipUploadedFile($uploadedFile);
        } else {
            $upload->path = $uploadedFile->storeAs("uploads/{$uploadedFile->getClientOriginalName()}");
        }

        $filename = pathinfo($upload->path, PATHINFO_FILENAME);
        $extension = pathinfo($upload->path, PATHINFO_EXTENSION);

        $upload->name = $filename;
        $upload->extension = $extension;

        $upload->save();
    }

    private function unzipUploadedFile(UploadedFile $uploadedFile): string {
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

            if ($extension !== 'csv' && $extension !== 'xlsx') {
                throw new \Exception('The ZIP file must have a CSV or Excel file');
            }

            $filename = basename($filePath);

            $permanentPath = "uploads/{$filename}";
            Storage::move($filePath, $permanentPath);

            $tempFolder->deleteDirectory($uniqueId);

            return "app/{$permanentPath}";
        }

        throw new \Exception('Error');
    }

    private function processCsvFile(string $filePath) {
        // TODO: JOB
    }

    private function processExcelFile(string $filePath) {
        // TODO: JOB
        DB::getConfig();
    }
}
