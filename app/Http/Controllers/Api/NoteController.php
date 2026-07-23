<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notes = $request->user()
            ->notes()
            ->with(['images', 'checklists'])
            ->when($request->filled('folder_id'), fn ($q) => $q->where('folder_id', $request->string('folder_id')))
            ->latest('updated_at')
            ->get();

        $totalAllNotes = $notes->count();
        return response()->json([
            'message' => 'Daftar note berhasil diambil.',
            'data' => [
                'total_all_notes' => $totalAllNotes, 
                'notes' => NoteResource::collection($notes)
                ],
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
        $note->load(['images', 'checklists']);
        return response()->json([
            'message' => 'Detail note berhasil diambil.',
            'data' => new NoteResource($note),
        ]);
    }

    public function update(UpdateNoteRequest $request, Note $note): JsonResponse
    {
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
        $note->update(['deleted_by' => $request->user()->id]);
        $note->delete();

        return response()->json([
            'message' => 'Note berhasil dihapus.',
            'data' => []
        ]);
    }
}