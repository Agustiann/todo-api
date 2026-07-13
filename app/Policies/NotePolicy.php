<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotePolicy
{
    public function view(User $user, Note $note): Response
    {
        return $user->id === $note->user_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke note ini.');
    }

    public function update(User $user, Note $note): Response
    {
        return $user->id === $note->user_id
            ? Response::allow()
            : Response::deny('Anda tidak dapat mengubah note milik pengguna lain.');
    }

    public function delete(User $user, Note $note): Response
    {
        return $user->id === $note->user_id
            ? Response::allow()
            : Response::deny('Anda tidak dapat menghapus note milik pengguna lain.');
    }
}