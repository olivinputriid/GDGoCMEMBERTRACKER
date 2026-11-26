<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'activity_id', 'title', 'category', 'points', 'awarded_at'])]
class PointLog extends Model
{
    use HasFactory;

    
    protected function casts(): array
    {
        return [
            'awarded_at' => 'date',
        ];
    }

    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
