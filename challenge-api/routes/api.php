<?php

use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(UploadController::class)->prefix('uploads')->group(function () {
        Route::get('/', 'listUploads');
        Route::post('/', 'uploadFile');
        Route::get('/search', 'searchFile');
    });
});
