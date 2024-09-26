<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/admin/users",
     *     summary="Get a list of users",
     *     tags={"Users"},
     *     @OA\Parameter(name="page",in="query",required=false,description="The page number to retrieve",@OA\Schema(type="integer", minimum=1)),
     *     @OA\Parameter(name="limit",in="query",required=false,description="The number of users per page",@OA\Schema(type="integer", minimum=1, maximum=100)),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $page = $validatedData['page'] ?? 1;
        $perPage = $validatedData['limit'] ?? 10;

        $users = User::paginate($perPage, ['*'], 'page', $page);
        $users->setPath(config('app.url') . '/api/v1/admin/users');

        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

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
