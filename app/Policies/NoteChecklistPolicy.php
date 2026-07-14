<?php

namespace App\Policies;

use App\Models\NoteChecklist;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NoteChecklistPolicy
{
    public function view(User $user, NoteChecklist $checklist): Response
    {
        return $checklist->note->user_id === $user->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat mengakses checklist milik note pengguna lain.');
    }

    public function update(User $user, NoteChecklist $checklist): Response
    {
        return $checklist->note->user_id === $user->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat mengubah checklist milik note pengguna lain.');
    }

    public function delete(User $user, NoteChecklist $checklist): Response
    {
        return $checklist->note->user_id === $user->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat menghapus checklist milik note pengguna lain.');
    }
}