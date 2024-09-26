<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\TagService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="API endpoints for managing tags"
 * )
 */
class TagController extends Controller
{
    function __construct(private TagService $tagServive){}

    /**
     * @OA\Get(
     *     path="/api/v1/tags/search",
     *     summary="Search for tags",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=true,
     *         description="The search term for tags",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search for tags",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $tags = $this->tagServive->searchTag($search);

        return response()->json($tags);
    }
}
