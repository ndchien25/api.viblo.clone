<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="API endpoints for user notifications"
 * )
 */
class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/notifications",
     *     summary="Get list of notifications",
     *     tags={"Notifications"},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="List of notifications", 
     *         @OA\JsonContent(ref="#/components/schemas/NotificationResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
            'current_page' => $notifications->currentPage(),
            'total_pages' => $notifications->lastPage(),
            'total' => $notifications->total(),
        ], Response::HTTP_OK);
    }

     /**
     * @OA\Post(
     *     path="/api/v1/notifications/mark-as-read",
     *     summary="Mark all notifications as read",
     *     tags={"Notifications"},
     *     @OA\Response(response=200, description="All notifications marked as read."),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotificationsnotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read.',
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
