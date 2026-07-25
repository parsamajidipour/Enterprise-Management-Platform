<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->paginate($request->integer('per_page', 15));

        return $this->success(UserResource::collection($users)->response()->getData(true), 'Users retrieved');
    }
}
