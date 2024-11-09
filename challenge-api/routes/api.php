<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(FileController::class)->prefix('files')->group(function () {
        Route::get('/', 'listFiles');
        Route::get('/search', 'searchFile');
        Route::post('/upload', 'uploadFile');
        Route::get('/{file}', 'getFileContent');
    });
});
