<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function listUploads()
    {
        return response()->json([
            'message' => 'Devo retornar uma lista'
        ]);
    }

    public function uploadFile(Request $request)
    {
        return response()->json([
            'message' => 'Devo fazer upload do arquivo'
        ]);
    }

    public function searchFile(Request $request)
    {
        return response()->json([
            'message' => 'Devo retornar o conteúdo de um arquivo'
        ]);
    }
}
