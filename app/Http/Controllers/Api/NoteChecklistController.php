<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteChecklist\StoreNoteChecklistRequest;
use App\Http\Requests\NoteChecklist\UpdateNoteChecklistRequest;
use App\Http\Resources\NoteChecklistResource;
use App\Models\Note;
use App\Models\NoteChecklist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NoteChecklistController extends Controller
{
    public function index(Note $note): JsonResponse
    {
        Gate::authorize('view', $note);

        $checklists = $note->checklists()
            ->orderBy('is_completed')
            ->orderBy('position')
            ->get();

        return response()->json([
            'message' => 'Daftar checklist berhasil diambil.',
            'data' => NoteChecklistResource::collection($checklists),
        ]);
    }

    public function store(StoreNoteChecklistRequest $request, Note $note): JsonResponse
    {
        Gate::authorize('update', $note);

        $nextPosition = (int) $note->checklists()->max('position') + 1;

        $checklist = $note->checklists()->create([
            'content' => $request->validated('content'),
            'is_completed' => $request->boolean('is_completed', false),
            'position' => $nextPosition,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Checklist berhasil ditambahkan.',
            'data' => new NoteChecklistResource($checklist),
        ], 201);
    }

    public function update(UpdateNoteChecklistRequest $request, Note $note, NoteChecklist $checklist): JsonResponse
    {
        Gate::authorize('update', $checklist);
        abort_unless($checklist->note_id === $note->id, 404, 'Checklist tidak ditemukan pada note ini.');

        $checklist->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Checklist berhasil diperbarui.',
            'data' => new NoteChecklistResource($checklist),
        ]);
    }

    public function destroy(Request $request, Note $note, NoteChecklist $checklist): JsonResponse
    {
        Gate::authorize('delete', $checklist);
        abort_unless($checklist->note_id === $note->id, 404, 'Checklist tidak ditemukan pada note ini.');

        $checklist->update(['deleted_by' => $request->user()->id]);
        $checklist->delete();

        return response()->json([
            'message' => 'Checklist berhasil dihapus.',
        ]);
    }
}