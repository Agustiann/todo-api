<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function generateApiToken(): string
    {
        $plainTextToken = Str::random(64);

        $this->forceFill([
            'api_token' => $plainTextToken,
        ])->save();

        return $plainTextToken;
    }

    public function revokeApiToken(): void
    {
        $this->forceFill(['api_token' => null])->save();
    }
}