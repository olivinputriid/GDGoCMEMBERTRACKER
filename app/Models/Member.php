<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'member_code',
    'name',
    'email',
    'phone',
    'division_id',
    'role_title',
    'batch',
    'points',
    'attendance_rate',
    'contributions_count',
    'competitions_count',
    'status',
    'avatar_color',
    'avatar_url',
    'bio',
    'github_url',
    'linkedin_url',
])]
class Member extends Model
{
    use HasFactory;

    
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

   
    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class)->orderByDesc('awarded_at')->orderByDesc('id');
    }

    
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_member')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

   
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : 'GD';
    }

   
    public function getShortNameAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        if (count($parts) <= 2) {
            return $this->name;
        }

        return $parts[0].' '.$parts[1];
    }

    
    public function recalculateAttendanceRate(): void
    {
        $total = $this->attendances()->count();
        if ($total === 0) {
            return;
        }

        $attended = $this->attendances()->whereIn('status', ['Present', 'Late', 'Excused'])->count();
        $this->attendance_rate = (int) round(($attended / $total) * 100);
        $this->save();
    }

    
    public function syncMilestoneAchievements(): void
    {
        $eligible = Achievement::whereNotNull('min_points')
            ->where('min_points', '<=', $this->points)
            ->pluck('id');

        if ($eligible->isEmpty()) {
            return;
        }

        $existing = $this->achievements()->pluck('achievements.id');
        $missing = $eligible->diff($existing);

        foreach ($missing as $achievementId) {
            $this->achievements()->attach($achievementId, ['awarded_at' => now()]);
        }
    }
}
