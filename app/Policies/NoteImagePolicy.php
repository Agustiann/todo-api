<?php

namespace App\Policies;

use App\Models\NoteImage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NoteImagePolicy
{
    public function view(User $user, NoteImage $image): Response
    {
        return $image->note->user_id === $user->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat mengakses gambar milik note pengguna lain.');
    }

    public function delete(User $user, NoteImage $image): Response
    {
        return $image->note->user_id === $user->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat menghapus gambar milik note pengguna lain.');
    }
}