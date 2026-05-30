<?php

namespace App\Models;

use App\Services\LeaveCreditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveCreditEarned extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'from_date',
        'to_date',
        'particulars',
        'earned',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
            'earned' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LeaveCreditEarned $earned): void {
            $earned->leave_type = $earned->leave_type ?: LeaveCreditService::BOTH;
            $earned->earned = $earned->earned ?: LeaveCreditService::DEFAULT_EARNED_POINTS;

            LeaveCreditService::assertYearlyEarnedLimit(
                (int) $earned->employee_id,
                $earned->leave_type,
                $earned->from_date,
                (float) $earned->earned,
                $earned->exists ? $earned->id : null,
            );
        });

        static::saved(function (LeaveCreditEarned $earned): void {
            LeaveCreditService::syncEarnedTransaction($earned);
        });

        static::deleted(function (LeaveCreditEarned $earned): void {
            LeaveCreditTransaction::query()
                ->where('source_type', LeaveCreditEarned::class)
                ->where('source_id', $earned->id)
                ->delete();
        });

        static::restored(function (LeaveCreditEarned $earned): void {
            LeaveCreditService::syncEarnedTransaction($earned);
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
