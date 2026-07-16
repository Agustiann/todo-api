<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteImage\StoreNoteImageRequest;
use App\Http\Resources\NoteImageResource;
use App\Models\Note;
use App\Models\NoteImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NoteImageController extends Controller
{
    private const MAX_IMAGES_PER_NOTE = 3;

    public function index(Note $note): JsonResponse
    {
        Gate::authorize('view', $note);

        $images = $note->images()->latest()->get();

        return response()->json([
            'message' => 'Daftar gambar berhasil diambil.',
            'data' => NoteImageResource::collection($images),
        ]);
    }

    public function store(StoreNoteImageRequest $request, Note $note): JsonResponse
    {
        Gate::authorize('update', $note);
        if (! $request->hasFile('image')) {
            return response()->json([
                'message' => 'Tidak ada gambar yang diunggah.',
            ], 422);
        }
        if ($note->images()->count() >= self::MAX_IMAGES_PER_NOTE) {
            return response()->json([
                'message' => 'Maksimal ' . self::MAX_IMAGES_PER_NOTE . ' gambar per note.',
            ], 422);
        }
        $file = $request->file('image');
        $path = $file->store("notes/{$note->id}", 'local');
        $image = $note->images()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        return response()->json([
            'message' => 'Gambar berhasil diunggah.',
            'data' => new NoteImageResource($image),
        ], 201);
    }

    public function show(Note $note, NoteImage $image): BinaryFileResponse
    {
        Gate::authorize('view', $image);
        abort_unless($image->note_id === $note->id, 404, 'Gambar tidak ditemukan pada note ini.');
        $path = Storage::disk('local')->path($image->file_path);
        abort_unless(file_exists($path), 404, 'File gambar tidak ditemukan.');
        return response()->file($path, [
            'Content-Type' => $image->mime_type,
        ]);
    }

    public function destroy(Request $request, Note $note, NoteImage $image): JsonResponse
    {
        Gate::authorize('delete', $image);
        abort_unless($image->note_id === $note->id, 404, 'Gambar tidak ditemukan pada note ini.');
        Storage::disk('local')->delete($image->file_path);
        $image->update(['deleted_by' => $request->user()->id]);
        $image->delete();

        return response()->json([
            'message' => 'Gambar berhasil dihapus.',
        ]);
    }
}
