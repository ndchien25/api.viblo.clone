<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * Handle media upload to S3.
     */
    public function upload(Request $request)
    {
        $client = Storage::disk('s3')->getClient();
        $extension = $request->input('file_name');

        $fileName = Str::random(30) . '.' . $extension;

        $userId = Auth::id();
        $galleryPath = "$userId/gallery/$fileName";

        // Upload to S3
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $galleryPath,
        ]);

        $presignedRequest = $client->createPresignedRequest($command, '+20 minutes');

        return response()->json([
            'file_path' => $galleryPath,
            'pre_signed' => (string) $presignedRequest->getUri()
        ], Response::HTTP_OK);
    }

    public function getObject(Request $request)
    {
        $filePath = $request->input('file_path');

        $url = Storage::disk('s3')->url($filePath);

        return response()->json([
            'url' => $url,
        ], Response::HTTP_OK);
    }
}
