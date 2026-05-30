<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCreditTransaction extends Model
{
    protected $fillable = [
        'employee_id',
        'source_type',
        'source_id',
        'from_date',
        'to_date',
        'particulars',
        'leave_type',
        'vacation_earned',
        'vacation_absence',
        'sick_earned',
        'sick_absence',
        'is_lwop',
        'action_taken',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
            'vacation_earned' => 'decimal:2',
            'vacation_absence' => 'decimal:2',
            'sick_earned' => 'decimal:2',
            'sick_absence' => 'decimal:2',
            'is_lwop' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
