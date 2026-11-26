<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'slug',
    'category',
    'status',
    'event_date',
    'location',
    'max_attendees',
    'initial_attendees',
    'points_reward',
    'icon',
    'accent_color',
    'banner_url',
    'description',
])]
class Activity extends Model
{
    use HasFactory;

   
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

   
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    
    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    
    public function getJoinedCountAttribute(): int
    {
        $recorded = $this->attendances_count ?? $this->attendances()->whereIn('status', ['Present', 'Late'])->count();

        return min($this->max_attendees, max($this->initial_attendees, $recorded));
    }

   
    public function getProgressPercentAttribute(): int
    {
        if ($this->max_attendees <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->joined_count / $this->max_attendees) * 100));
    }
}
