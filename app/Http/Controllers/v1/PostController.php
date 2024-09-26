<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Services\PostService;
use App\Http\Services\VoteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="API endpoints for manage post"
 * )
 */
class PostController extends Controller
{
    function __construct(private PostService $postService, private VoteService $voteService) {}

    /** @OA\Get(
     *      path="/api/v1/posts", 
     *      summary="Get list of posts", 
     *      tags={"Posts"}, 
     *      @OA\Parameter(name="page", in="query", description="Page number", required=false, 
     *      @OA\Schema(type="integer", minimum=1)), 
     *      @OA\Parameter(name="perPage", in="query", description="Number of posts per page", required=false, 
     *      @OA\Schema(type="integer", minimum=1, maximum=100)), 
     *      @OA\Response(response=200, description="Success", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostResource"))), 
     *      @OA\Response(response=400, description="Invalid parameters")) 
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'perPage' => 'sometimes|integer|min:1|max:100',
        ]);

        $page = $validated['page'] ?? 1;
        $perPage = $validated['perPage'] ?? 20;

        $posts = $this->postService->getNewest($page, $perPage);
        $posts->setPath(config('app.url') . '/api/v1/posts');

        return PostResource::collection($posts);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bài viết được tạo thành công!!"),
     *             @OA\Property(property="slug", type="string", example="new-post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->only(['title', 'content', 'tags']);
        $post = $this->postService->create($validated);
        if ($post) {
            return response()->json([
                'message' => 'Bài viết được tạo thành công!!',
                'slug' => $post->slug
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            "message" => "Lỗi không tạo được bài viết. Vui lòng thử lại"
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/posts/{slug}",
     *     summary="Get a specific post by slug",
     *     tags={"Posts"},
     *     @OA\Parameter(name="slug",in="path",description="The slug of the post",required=true,@OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/PostResource")
     *     ),
     *     @OA\Response(response=400,description="Bad Request", @OA\JsonContent()),
     *     @OA\Response(response=404,description="Post not found", @OA\JsonContent())
     * )
     */
    public function show(string $slug)
    {
        validator(['slug' => $slug], [
            'slug' => 'required|string|regex:/^[a-z0-9-]+$/|exists:posts,slug',
        ])->validate();

        $result = $this->postService->getBySlug($slug);
        if (!$result) {
            return response()->json(['message' => "Lỗi khi tạo bài viết vui lòng thử lại"], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts/{id}/vote",
     *     summary="Vote on a post (upvote or downvote)",
     *     tags={"Posts"},
     *     @OA\Parameter(name="id",in="path",description="Post ID",required=true,@OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="vote", type="string", enum={"up", "down", "none"}, example="up")
     *         )
     *     ),
     *     @OA\Response(response=200,description="Vote recorded succes", @OA\JsonContent()),
     *     @OA\Response(response=422,description="Invalid input", @OA\JsonContent()),
     *     @OA\Response(response=400,description="Bad Request", @OA\JsonContent()),
     * )
     */
    public function vote(Request $request, string $id)
    {
        $request->merge(['id' => $request->route('id')]);
        $validatedData = $request->validate([
            'id' => 'required|exists:posts,id',
            'vote' => 'required|string|in:up,down,none'
        ]);
        $voteType = $validatedData['vote'];
        $userId = $request->user()->id;

        $result = $this->voteService->vote($id, $userId, $voteType);

        return response()->json($result, !$result['error'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
