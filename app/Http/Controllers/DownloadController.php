<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Capture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadController extends Controller
{
    public function downloadAlbum (Request $request) {
        $request -> validate([
            'album_id' => 'required|exists:albums,id',
            'user_id' => 'required|exists:users,id',
            'token' => 'required'
        ]);

        $album = Album::findOrFail($request->album_id);
        $user = User::findOrFail($request->user_id);
        $captures = Capture::where('album_id', $request->album_id)->get();

        if ($album->status !== 'longterm') {
            return response()->json([
                'error' => 'You do not have permission to download this album'
            ], 403);
        }

        if ($captures->isEmpty()) {
            return response()->json([
                'error' => 'No photos in this album'
            ], 404);
        }

        Log::info('Captures:', $captures->toArray());

        // Create a zip file in the storage path
        $fileName = "album_{$album->id}_photos.zip";
        $zipPath = Storage::disk('public')->path($fileName);

        $zip = new ZipArchive;
        if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Loop through captures and add each file to the zip
            foreach ($captures as $capture) {
                $filePath = Storage::disk('public')->path($capture->image);

                if (Storage::disk('public')->exists($capture->image)) {
                    $zip->addFile($filePath, basename($filePath));
                } else {
                    Log::warning('File not found:', $filePath);
                }
            }

            $zip->close();
            
        } else {
            Log::error('Failed to create zip file', $zipPath);
        }

        return response()->json([
            'message' => 'Album zipped successfully!',
            'file_name' => $fileName,
            'file_size' => Storage::disk('public')->size($fileName)
        ]);
    }

    public function downloadFile ($albumId, $userId, $fileName) {
        Log::info('Download File Called', [
            'userId' => $userId,
            'albumId' => $albumId,
            'fileName' => $fileName,
            'filePath' => storage_path('app/public/' . $fileName)
        ]);

        $filePath = storage_path('app/public/' . $fileName);
        $user = User::findOrFail($userId);

        if (File::exists($filePath)) {
            Log::info('File found', ['filePath' => $filePath]);

            $fileSize = File::size($filePath);

            $logMessage = sprintf(
                "User ID: %d downloaded file %s, Size: %d bytes, Date: %s",
                $user->id,
                $fileName,
                $fileSize,
                now()->toDateTimeString()
            );

            $user->log .= $logMessage . PHP_EOL;
            $user->save();

            return response()->download($filePath)->deleteFileAfterSend(false);
        }else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
    
}
