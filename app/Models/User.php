<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'member_id', 'locale', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    
    use HasFactory, Notifiable;

   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

   
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

   
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
