<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Services\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    function __construct(private UploadService $uploadService) {}

    public function listUploads()
    {
        return response()->json([
            'message' => 'Devo retornar uma lista'
        ]);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $file_name = $request->get('name');
        $file = $request->file('file');
        $upload = $this->uploadService->processFile($file, $file_name);
        return response()->json($upload);
    }

    public function searchFile(Request $request)
    {
        return response()->json([
            'message' => 'Devo retornar o conteúdo de um arquivo'
        ]);
    }
}
