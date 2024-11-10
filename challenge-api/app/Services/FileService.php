<?php

namespace App\Services;

use App\Enums\FileStatusEnum;
use App\Exceptions\FileNotImportedException;
use App\Exceptions\FileUploadException;
use App\Jobs\ProcessFileImport;
use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Throwable;

class FileService {
    /**
     * Processes the uploaded file, either storing it directly or unzipping it and processing the contents.
     *
     * @param UploadedFile $uploadedFile The file uploaded by the user to be processed.
     * @return File The processed file object with updated attributes like path, name, and extension.
     * @throws FileUploadException If an error occurs during file upload or processing.
     * @throws Throwable Propagates any other exceptions that may occur during processing.
     */
    public function processFile(UploadedFile $uploadedFile): File {
        $file = new File([
            'status' => FileStatusEnum::PENDING
        ]);

        $extension = $uploadedFile->getClientOriginalExtension();

        try {
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
        } catch (\Exception $e) {
            throw new FileUploadException('Error while processing file: ' . $e->getMessage());
        }
    }

    /**
     * Extracts and processes a ZIP archive, ensuring it contains a CSV or Excel file.
     *
     * @param UploadedFile $uploadedFile The ZIP file uploaded for extraction.
     * @return string The path where the extracted file is stored.
     * @throws FileUploadException If extraction fails or the archive contents are invalid.
     * @throws Throwable Propagates any other exceptions that may occur during extraction.
     */
    private function unzipUploadedFile(UploadedFile $uploadedFile): string {
        $uniqueId = Str::uuid()->toString();
        $tempFolder = Storage::disk('temp');

        $zip = new ZipArchive;

        if ($zip->open($uploadedFile->getRealPath()) === true) {
            $tempFolder->makeDirectory($uniqueId);

            $zip->extractTo($tempFolder->path($uniqueId));
            $zip->close();

            $files = $tempFolder->files($uniqueId);

            throw_if(count($files) === 0, new FileUploadException('No file found in the ZIP archive'));

            $filePath = "temp/{$files[0]}";
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            throw_if($extension !== 'csv' && $extension !== 'xlsx', new FileUploadException('The ZIP file must contain a CSV or Excel file'));

            $filename = basename($filePath);
            $permanentPath = "uploads/{$filename}";
            Storage::move($filePath, $permanentPath);
            $tempFolder->deleteDirectory($uniqueId);

            return $permanentPath;
        }

        throw new FileUploadException('Error extracting ZIP file');
    }

    /**
     * Searches for a file based on the provided name and/or reference date.
     *
     * @param string|null $name The name of the file to search for.
     * @param string|null $uploadedAt The date to filter files by updating date.
     * @return File|null The first matching file found, or null if no match is found.
     */
    public function searchFile(string $name = null, string $uploadedAt = null) : ?File
    {
        return File::when($name, function ($query, $name) {
                return $query->where('name', $name);
            })
                ->when($uploadedAt, function ($query, $referenceDate) {
                    return $query->whereDate('created_at', $referenceDate);
                })->first();
    }

    /**
     * Retrieves the content of a file, optionally filtered by ticker symbol and/or report date.
     *
     * @param File $file The file instance whose contents are being retrieved.
     * @param string|null $tickerSymbol Optional ticker symbol to filter records.
     * @param string|null $reportDate Optional report date to filter records.
     * @return LengthAwarePaginator|Collection A paginated collection or a full collection of records.
     * @throws Throwable Throws an exception if the file is still pending or being processed.
     */
    public function getFileContent(File $file, string $tickerSymbol = null, string $reportDate = null) : LengthAwarePaginator|Collection
    {
        throw_if($file->status === FileStatusEnum::PROCESSING, new FileNotImportedException());
        throw_if($file->status !== FileStatusEnum::PENDING, new FileNotImportedException('The file has not started importing yet'));

        $query = $file->records()
            ->when($tickerSymbol, function ($query, $tckrSymb) {
                return $query->where('TckrSymb', $tckrSymb);
            })
            ->when($reportDate, function ($query, $rptDt) {
                return $query->where('RptDt', $rptDt);
            });

        return $tickerSymbol || $reportDate ? $query->get() : $query->paginate(15);
    }
}
