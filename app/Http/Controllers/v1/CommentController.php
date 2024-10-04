<?php

namespace App\Http\Controllers\v1;

use App\Events\NewCommentCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use App\Http\Services\CommentService;
use App\Models\Comment;
use Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="API endpoints for managing comments"
 * )
 */
class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /**
     * @OA\Post(
     *     path="/api/v1/posts/{postId}/comments",
     *     summary="Create a new comment",
     *     tags={"Comments"},
     *     @OA\Parameter(name="postId",in="path",required=true,description="The ID of the post",@OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCommentRequest")
     *     ),
     *     @OA\Response(response=201, description="Comment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CommentResource")
     *     ),
     *     @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent())
     * )
     */
    public function store(StoreCommentRequest $request, string $postId)
    {
        $validated = $request->only(['post_id', 'type', 'content', 'parent_id']);
        $comment = $this->commentService->create($validated);
        if (!$comment) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }
        $comment->load('user');
        broadcast(new NewCommentCreated($comment, $postId));
        return response()->json([
            'comment' => new CommentResource($comment)
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/posts/{postId}/comments",
     *     summary="Get parent comments for a post",
     *     tags={"Comments"},
     *     @OA\Parameter( name="postId",in="path",required=true,description="The ID of the post",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page",in="query",description="Number of comments per page",@OA\Schema(type="integer", example=10)),
     *     @OA\Parameter( name="page",in="query",description="Page number",@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Parent comments fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/CommentResource")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total_pages", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent())
     * )
     */
    public function showParent(Request $request, string $postId)
    {
        $request->merge(['postId' => $request->route('postId')]);
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'postId' => [
                'required',
                'string',
                'exists:posts,id'
            ],
        ]);

        // Ensure that the postId exists
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
     * @OA\Get(
     *     path="/api/v1/posts/{postId}/comments/{parentId}/replies",
     *     summary="Get child comments for a parent comment",
     *     tags={"Comments"},
     *     @OA\Parameter(name="postId",in="path",required=true,description="The ID of the post comment",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="parentId",in="path",required=true,description="The ID of the parent comment",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page",in="query", description="Number of comments per page", @OA\Schema(type="integer", default=3)),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(response=200, description="Child comments fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/CommentResource")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total_pages", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent())
     * )
     */
    public function showChild(Request $request, string $postId, string $parentId)
    {
        $request->merge(['postId' => $request->route('postId'), 'parentId' => $request->route('parentId')]);
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'postId' => [
                'required',
                'string',
                'exists:posts,id' // Check if postId exists in posts table
            ],
            'parentId' => [
                'required',
                'integer',
                'exists:comments,id'
            ]
        ]);
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
     * @OA\Put(
     *     path="/api/v1/posts/{postId}/comments/{id}",
     *     summary="Update a comment",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the comment to be updated",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="content", type="string", description="Updated content of the comment")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Comment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CommentResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Comment not found", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent()),
     * )
     */
    public function update(Request $request,string $postId, string $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized action.'], Response::HTTP_UNAUTHORIZED);
        }
        $comment->content = $validated['content'];
        $comment->save();
        $comment->load('user');
        return response()->json([
            'comment' => new CommentResource($comment)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
