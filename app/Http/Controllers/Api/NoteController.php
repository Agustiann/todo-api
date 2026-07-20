<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notes = $request->user()
            ->notes()
            ->with([
                'images',
                'checklists' => fn ($q) => $q->orderBy('is_completed')->orderBy('position'),
            ])
            ->when($request->filled('folder_id'), fn ($q) => $q->where('folder_id', $request->string('folder_id')))
            ->latest('updated_at')
            ->get();

        $totalAllNotes = $request->user()->notes()->count();
        return response()->json([
            'message' => 'Daftar note berhasil diambil.',
            'meta' => [
                'total_all_notes' => $totalAllNotes,
            ],
            'data' => NoteResource::collection($notes),
        ]);
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = Note::create([
            'user_id' => $request->user()->id,
            'folder_id' => $request->validated('folder_id'),
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Note berhasil dibuat.',
            'data' => new NoteResource($note),
        ], 201);
    }

    public function show(Note $note): JsonResponse
    {
        Gate::authorize('view', $note);
        $note->load([
            'images',
            'checklists' => fn ($q) => $q->orderBy('is_completed')->orderBy('position'),
        ]);
        return response()->json([
            'message' => 'Detail note berhasil diambil.',
            'data' => new NoteResource($note),
        ]);
    }

    public function update(UpdateNoteRequest $request, Note $note): JsonResponse
    {
        Gate::authorize('update', $note);

        $note->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Note berhasil diperbarui.',
            'data' => new NoteResource($note),
        ]);
    }

    public function destroy(Request $request, Note $note): JsonResponse
    {
        Gate::authorize('delete', $note);

        $note->update(['deleted_by' => $request->user()->id]);
        $note->delete();

        return response()->json([
            'message' => 'Note berhasil dihapus.',
        ]);
    }
}