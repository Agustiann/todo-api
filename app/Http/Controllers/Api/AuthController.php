<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = $user->generateApiToken();

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->revokeApiToken();

        return response()->json([
            'message' => 'Logout berhasil.',
            'data' => []
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Data user berhasil diambil.',
            'data' => new UserResource($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'name' => $request->validated('name'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('local')->delete($user->photo);
            }

            $data['photo'] = $request->file('photo')->store("photo_profile/{$user->id}", 'local');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => new UserResource($user),
        ]);
    }

    public function photo(Request $request): BinaryFileResponse
    {
        $user = $request->user();

        abort_unless($user->photo, 404, 'Foto profil tidak ditemukan.');

        $path = Storage::disk('local')->path($user->photo);
        abort_unless(file_exists($path), 404, 'File foto tidak ditemukan.');

        return response()->file($path);
    }
}