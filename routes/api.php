<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CaptureController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json();
    });

    Route::post('/photographer/email-update',[AlbumController::class, 'updateEmail'])->name('email-update');
    Route::post('/photographer-store', [AlbumController::class, 'store'])->name('store');
    Route::post('/photographer-invite', [AlbumController::class, 'inviteUser'])->name('invite');
    Route::post('/photographer/friend-invite', [AlbumController::class, 'inviteFriend'])->name('friend-invite');
    Route::post('/photographer/album-download', [DownloadController::class, 'downloadAlbum'])->name('download-album');
});

Route::get('/admin', [AdminController::class, 'index'])->name('admin');

Route::get('/check-token/{albumId}/{token}', [AdminController::class, 'checkTokenStatus'])->name('check-token');

Route::post('/admin-update', [AdminController::class, 'update'])->name('admin.update');

Route::post('/photographer-capture', [CaptureController::class, 'capture'])->name('capture');

Route::get('/photographer/album/{albumId}/user/{userId}/{token}', [AlbumController::class, 'show'])->name('show');

Route::get('/photographer/download/album/{albumId}/user/{userId}/file/{fileName}', [DownloadController::class, 'downloadFile']);


