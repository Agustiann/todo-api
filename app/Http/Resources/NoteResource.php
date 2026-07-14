<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'folder_id' => $this->folder_id,
            'images' => NoteImageResource::collection($this->whenLoaded('images')),
            'checklists' => NoteChecklistResource::collection($this->whenLoaded('checklists')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}