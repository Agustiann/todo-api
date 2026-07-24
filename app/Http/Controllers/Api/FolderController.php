<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Folder\StoreFolderRequest;
use App\Http\Requests\Folder\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $folders = $request->user()
            ->folders()
            ->withCount('notes')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Daftar folder berhasil diambil.',
            'data' => FolderResource::collection($folders),
        ]);
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        $folder = Folder::create([
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Data Folder berhasil dibuat.',
            'data' => new FolderResource($folder),
        ], 201);
    }

    public function show(Folder $folder): JsonResponse
    {
        $folder->load([
            'notes' => fn($q) => $q->orderBy('created_at', 'dsc'),
        ]);
        return response()->json([
            'message' => 'Detail folder berhasil diambil.',
            'data' => new FolderResource($folder),
        ]);
    }

    public function update(UpdateFolderRequest $request, Folder $folder): JsonResponse
    {
        $folder->update([
            'name' => $request->validated('name'),
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Data Folder berhasil diperbarui.',
            'data' => new FolderResource($folder),
        ]);
    }

    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        $folder->update(['deleted_by' => $request->user()->id]);
        $folder->delete();

        return response()->json([
            'message' => 'Data Folder berhasil dihapus.',
            'data' => []
        ]);
    }
}
