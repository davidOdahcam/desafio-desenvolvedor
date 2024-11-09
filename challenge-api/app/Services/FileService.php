<?php

namespace App\Services;

use App\Enums\FileStatusEnum;
use App\Imports\FileImport;
use App\Jobs\ProcessFileImport;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class FileService {
    public function processFile(UploadedFile $uploadedFile): File {
        $file = new File([
            'status' => FileStatusEnum::PENDING
        ]);

        $extension = $uploadedFile->getClientOriginalExtension();

        if ($extension === 'zip') {
            $file->path = $this->unzipUploadedFile($uploadedFile);
        } else {
            $file->path = $uploadedFile->storeAs("uploads/{$uploadedFile->getClientOriginalName()}");
        }

        $file->name = pathinfo($file->path, PATHINFO_FILENAME);
        $file->extension = pathinfo($file->path, PATHINFO_EXTENSION);
        $file->save();

        dispatch(new ProcessFileImport($file->id));

        return $file;
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

            $filePath = "temp/{$files[0]}";
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($extension !== 'csv' && $extension !== 'xlsx') {
                throw new \Exception('The ZIP file must have a CSV or Excel file');
            }

            $filename = basename($filePath);
            $permanentPath = "uploads/{$filename}";
            Storage::move($filePath, $permanentPath);
            $tempFolder->deleteDirectory($uniqueId);

            return $permanentPath;
        }

        throw new \Exception('Error');
    }

    public function importFile(File $file)
    {
        try {
            $file->updateStatus(FileStatusEnum::PROCESSING);
            Excel::import(new FileImport($file), $file->path);
            $file->updateStatus(FileStatusEnum::COMPLETED);

            Storage::delete($file->path);
        } catch (\Exception $e) {
            logger()->debug($e);
            $file->updateStatus(FileStatusEnum::FAILED);
        }
    }
}
