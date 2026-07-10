<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'folder_id' => [
                'nullable',
                'uuid',
                Rule::exists('folders', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul note wajib diisi.',
            'title.max' => 'Judul note maksimal 255 karakter.',
            'folder_id.uuid' => 'Folder tidak valid.',
            'folder_id.exists' => 'Folder tidak ditemukan.',
        ];
    }
}