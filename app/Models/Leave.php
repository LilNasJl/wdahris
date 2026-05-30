<?php

namespace App\Models;

use App\Services\LeaveCreditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_name',
        'employee_id',
        'leave_type',
        'leave_from',
        'leave_to',
        'reason',
        'status',
        'status_updated_at',
        'deducted_points',
        'is_lwop',
        'approval_comment',
        'approved_checked_by_hr',
    ];

    protected function casts(): array
    {
        return [
            'leave_from' => 'date',
            'leave_to' => 'date',
            'status_updated_at' => 'datetime',
            'deducted_points' => 'decimal:2',
            'is_lwop' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Leave $leave): void {
            $creditData = LeaveCreditService::assertCanCreateLeave($leave->attributesToArray());

            $leave->deducted_points = $creditData['deducted_points'];
            $leave->is_lwop = $creditData['is_lwop'];
        });

        static::created(function (Leave $leave): void {
            LeaveCreditService::syncLeaveTransaction($leave);
        });

        static::updated(function (Leave $leave): void {
            LeaveCreditService::syncLeaveTransaction($leave);
        });

        static::deleted(function (Leave $leave): void {
            LeaveCreditTransaction::query()
                ->where('source_type', Leave::class)
                ->where('source_id', $leave->id)
                ->delete();
        });

        static::restored(function (Leave $leave): void {
            LeaveCreditService::syncLeaveTransaction($leave);
        });
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
