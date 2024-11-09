<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileResouce;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    function __construct(private readonly FileService $fileService) {}

    public function listUploads()
    {
        $files = File::paginate(15);

        return FileResouce::collection($files);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $upload = $this->fileService->processFile($request->file('file'));
        return response()->json($upload);
    }

    public function searchFile(Request $request)
    {
        return response()->json([
            'message' => 'Devo retornar o conteúdo de um arquivo'
        ]);
    }
}
