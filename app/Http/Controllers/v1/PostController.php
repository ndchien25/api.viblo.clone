<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Services\PostService;
use App\Http\Services\VoteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    function __construct(private PostService $postService, private VoteService $voteService) {}

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
    public function store(StorePostRequest $request)
    {
        $validated = $request->only(['title', 'content', 'tags']);
        $post = $this->postService->createPost($validated);
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
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        validator(['slug' => $slug], [
            'slug' => 'required|string|regex:/^[a-z0-9-]+$/|exists:posts,slug',
        ])->validate();

        $result = $this->postService->getPostBySlug($slug);
        if (!$result) {
            return response()->json('message' => "Lỗi khi tạo bài viết vui lòng thử lại", Response::HTTP_BAD_REQUEST);
        }

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Vote on a post (upvote or downvote).
     */
    public function vote(Request $request, string $id)
    {
        $validatedData = $request->validate([
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
