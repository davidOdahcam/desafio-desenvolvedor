<?php

namespace App\Http\Controllers;

use App\Exceptions\FileUploadException;
use App\Http\Requests\FileContentRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileRecordResource;
use App\Http\Resources\FileResouce;
use App\Models\File;
use App\Services\FileService;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class FileController extends Controller
{
    function __construct(private readonly FileService $fileService) {}

    public function listFiles()
    {
        try {
            $files = File::paginate(15);
            return FileResouce::collection($files);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the file list.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function uploadFile(UploadFileRequest $request)
    {
        try {
            $file = $this->fileService->processFile($request->file('file'));
            return new FileResouce($file);
        } catch (FileUploadException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchFileContent(FileContentRequest $request, File $file)
    {
        try {
            $tckrSymb = $request->get('TckrSymb');
            $rptDt = $request->get('RptDt');
            $records = $this->fileService->searchRecords($file, $tckrSymb, $rptDt);

            return FileRecordResource::collection($records);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while searching the file content.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
