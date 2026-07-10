<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'ilike', '%'.$request->string('search').'%')
                    ->orWhere('email', 'ilike', '%'.$request->string('search').'%');
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'users' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
}