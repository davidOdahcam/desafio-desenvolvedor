<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileContentRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileRecordResource;
use App\Http\Resources\FileResouce;
use App\Models\File;
use App\Services\FileService;

class FileController extends Controller
{
    function __construct(private readonly FileService $fileService) {}

    public function listFiles()
    {
        $files = File::paginate(15);
        return FileResouce::collection($files);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $file = $this->fileService->processFile($request->file('file'));
        return new FileResouce($file);
    }

    public function searchFileContent(FileContentRequest $request, File $file)
    {
        $tckrSymb = $request->get('TckrSymb');
        $rptDt = $request->get('RptDt');
        $records = $this->fileService->searchRecords($file, $tckrSymb, $rptDt);
        
        return FileRecordResource::collection($records);
    }
}
