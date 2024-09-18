<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use App\Http\Services\CommentService;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->only(['post_id', 'type', 'content', 'parent_id']);
        $comment = $this->commentService->create($validated);
        if (!$comment) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }
        $comment->load('user');
        return response()->json([
            'comment' => new CommentResource($comment)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the parent comments for a given post.
     */
    public function showParent(Request $request, string $postId)
    {
        $perPage = $request->get('per_page', 3);
        $page = $request->get('page', 1);

        $comments = $this->commentService->showParent($postId, $page, $perPage);

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'current_page' => $comments->currentPage(),
            'total_pages' => $comments->lastPage(),
        ], Response::HTTP_OK);
    }

    /**
     * Display the child comments for a given parent comment.
     */
    public function showChild(Request $request, string $parentId)
    {
        $perPage = $request->get('per_page', 3);
        $page = $request->get('page', 1);

        $comments = $this->commentService->showChild($parentId, $page, $perPage);

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'current_page' => $comments->currentPage(),
            'total_pages' => $comments->lastPage(),
        ], Response::HTTP_OK);
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
