<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'icon', 'description', 'bg_color', 'text_color', 'min_points'])]
class Achievement extends Model
{
    use HasFactory;

    
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'achievement_member')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}
