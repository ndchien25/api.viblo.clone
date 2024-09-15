<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use App\Http\Services\CommentService;
use App\Models\Comment;
use Symfony\Component\HttpFoundation\Response;
class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->only(['post_id', 'type', 'content', 'parent_id']);
        $comment = $this->commentService->createComment($validated);
        if ($comment) {
            return response()->json([
                'message' => 'Comment thành công bài viết thành công!!',
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            "message" => "Lỗi không tạo comment. Vui lòng thử lại"
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $post_id)
    {
        return Comment::wherePostId($post_id)->with('user')->limit(3)->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
