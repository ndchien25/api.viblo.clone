<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Media",
 *     description="API endpoints for media management"
 * )
 */
class MediaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/upload",
     *     summary="Upload media to S3",
     *     tags={"Media"},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"file_name"},
     *             @OA\Property(property="file_name", type="string", description="The file extension of the media")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful media upload",
     *         @OA\JsonContent(
     *             @OA\Property(property="file_path", type="string", description="The path of the uploaded file"),
     *             @OA\Property(property="pre_signed", type="string", description="The pre-signed URL for the uploaded file")
     *         )
     *     ),
     *     @OA\Response(response=400,description="Bad request",@OA\JsonContent())
     * )
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

   /**
     * @OA\Get(
     *     path="/api/v1/get-object",
     *     summary="Get a media object URL",
     *     tags={"Media"},
     *     @OA\Parameter(name="file_path",in="query",required=true,description="The path of the media file",@OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of media URL",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", description="The URL of the requested media file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function getObject(Request $request)
    {
        $filePath = $request->input('file_path');

        $url = Storage::disk('s3')->url($filePath);

        return response()->json([
            'url' => $url,
        ], Response::HTTP_OK);
    }
}
