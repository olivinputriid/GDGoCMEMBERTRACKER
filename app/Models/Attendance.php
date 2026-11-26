<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'activity_id', 'status', 'recorded_at', 'notes'])]
class Attendance extends Model
{
    use HasFactory;

    
    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
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
