<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    function __construct(private TagService $tagServive){}

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $tags = $this->tagServive->searchTag($search);

        return response()->json($tags);
    }
}
