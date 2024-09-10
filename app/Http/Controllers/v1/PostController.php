<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Services\PostService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    function __construct(private PostService $postService) {}

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
        $validatedData = validator(['slug' => $slug], [
            'slug' => 'required|string|regex:/^[a-z0-9-]+$/|exists:posts,slug',
        ])->validate();
    
        $post = $this->postService->getPostBySlug($slug);
        return response()->json($post);
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
