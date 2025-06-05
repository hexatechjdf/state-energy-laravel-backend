<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return successResponse(UserResource::collection($users));
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return successResponse(new UserResource($user), Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        return successResponse(new UserResource($user));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());
        return successResponse(new UserResource($user));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return successResponse('User deleted successfully');
    }
}
