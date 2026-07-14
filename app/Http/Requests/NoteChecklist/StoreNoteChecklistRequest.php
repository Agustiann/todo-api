<?php

namespace App\Http\Requests\NoteChecklist;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Isi checklist wajib diisi.',
            'content.max' => 'Isi checklist maksimal 1000 karakter.',
            'is_completed.boolean' => 'Status centang tidak valid.',
        ];
    }
}