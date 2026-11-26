<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'color', 'description'])]
class Division extends Model
{
    use HasFactory;

    
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
