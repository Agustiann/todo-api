<?php

namespace App\Http\Requests\NoteImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.file' => 'File tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}