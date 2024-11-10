<?php

namespace App\Http\Controllers;

use App\Exceptions\FileNotImportedException;
use App\Exceptions\FileUploadException;
use App\Http\Requests\FileContentRequest;
use App\Http\Requests\SearchFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileRecordResource;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Throwable;

class FileController extends Controller
{
    function __construct(private readonly FileService $fileService) {}

    /**
     * Retrieves a paginated list of files and returns them as a resource collection.
     *
     * @return JsonResponse|AnonymousResourceCollection A JSON response with an error message or a resource collection of files.
     */
    public function listFiles() : JsonResponse|AnonymousResourceCollection
    {
        try {
            $files = File::paginate(15);
            return FileResource::collection($files);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the file list.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handles the file upload process and returns the created file resource.
     *
     * @param UploadFileRequest $request The validated request containing the file to upload.
     * @return FileResource|JsonResponse The created file resource or an error message in JSON format.
     * @throws Throwable Propagates any exceptions during file processing.
     */
    public function uploadFile(UploadFileRequest $request) : FileResource|JsonResponse
    {
        try {
            $file = $this->fileService->processFile($request->file('file'));
            return new FileResource($file);
        } catch (FileUploadException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Searches a file based on name or referenceDate.
     *
     * @param SearchFileRequest $request The validated request containing search parameters.
     * @return FileResource|JsonResponse The file resource or an error message in JSON format.
     */
    public function searchFile(SearchFileRequest $request) : FileResource|JsonResponse
    {
        try {
            $name = $request->get('name');
            $uploadedAt = $request->get('uploaded_at');
            $file = $this->fileService->searchFile($name, $uploadedAt);

            if (!$file) {
                return response()->json(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
            }

            return new FileResource($file);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while searching the file.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the contents of a file based on specified parameters and returns the results.
     *
     * @param FileContentRequest $request The validated request containing search parameters.
     * @param File $file The file instance to search within.
     * @return JsonResponse|AnonymousResourceCollection A JSON response with an error message or a collection of file records.
     * @throws FileNotImportedException If the file has not been fully imported yet or import has been failed.
     * @throws Throwable Propagates any exceptions during the search operation.
     */
    public function getFileContent(FileContentRequest $request, File $file) : JsonResponse|AnonymousResourceCollection
    {
        try {
            $tckrSymb = $request->get('TckrSymb');
            $rptDt = $request->get('RptDt');
            $records = $this->fileService->getFileContent($file, $tckrSymb, $rptDt);

            return FileRecordResource::collection($records);
        } catch (FileNotImportedException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
