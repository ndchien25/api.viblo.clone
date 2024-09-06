<?php

namespace App\Http\Services;

use App\Models\Tag;

class TagService extends BaseService
{
    public function searchTag($query = "")
    {
        $tags = Tag::select('id', 'name')
            ->where('name', 'like', "%{$query}%")->get();
        return $tags ?? $tags;
    }
}
