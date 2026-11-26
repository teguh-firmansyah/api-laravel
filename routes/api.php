<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('articles', ArticleController::class);
});

Route::get('/media/{path}', function (string $path) {

    abort_unless(
        str_starts_with($path, 'articles/'),
        404
    );

    abort_if(
        str_contains($path, '..') ||
            str_contains($path, '\\'),
        404
    );

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('public');

    abort_unless($disk->exists($path), 404);

    return $disk->response($path);
})->where('path', '.*');
