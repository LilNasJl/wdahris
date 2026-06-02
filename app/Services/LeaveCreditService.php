<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\LeaveCreditEarned;
use App\Models\LeaveCreditTransaction;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LeaveCreditService
{
    public const VACATION = 'Vacation Leave';

    public const SICK = 'Sick Leave';

    public const HALFDAY = 'Halfday Leave';

    public const BIRTHDAY = 'Birthday Leave';

    public const BOTH = 'Vacation and Sick Leave';

    public const MONETIZATION = 'Monetization';

    public const YEARLY_MAX = 15.0;

    public const DEFAULT_EARNED_POINTS = 1.250;

    public const MONETIZATION_POINTS = 20.0;

    public static function availableCredits(int $employeeId, ?int $exceptLeaveId = null): array
    {
        $vacationEarned = self::earnedTotal($employeeId, 'vacation');
        $sickEarned = self::earnedTotal($employeeId, 'sick');

        $vacationUsed = (float) Leave::query()
            ->where('employee_id', $employeeId)
            ->whereIn('leave_type', [self::VACATION, self::HALFDAY, self::BIRTHDAY])
            ->where('status', '!=', 'Rejected')
            ->where('is_lwop', false)
            ->when($exceptLeaveId, fn ($query) => $query->whereKeyNot($exceptLeaveId))
            ->sum('deducted_points');

        $sickUsed = (float) Leave::query()
            ->where('employee_id', $employeeId)
            ->where('leave_type', self::SICK)
            ->where('status', '!=', 'Rejected')
            ->where('is_lwop', false)
            ->when($exceptLeaveId, fn ($query) => $query->whereKeyNot($exceptLeaveId))
            ->sum('deducted_points');

        $monetized = (float) LeaveCreditTransaction::query()
            ->where('employee_id', $employeeId)
            ->where('source_type', self::MONETIZATION)
            ->sum('vacation_absence');

        $vacation = round($vacationEarned - $vacationUsed - $monetized, 3);
        $sick = round($sickEarned - $sickUsed, 3);

        return [
            'vacation' => max($vacation, 0),
            'sick' => max($sick, 0),
            'total' => max(round($vacation + $sick, 3), 0),
        ];
    }

    public static function earnedTotal(int $employeeId, string $bucket, ?int $year = null, ?int $exceptId = null): float
    {
        $query = LeaveCreditEarned::query()
            ->where('employee_id', $employeeId)
            ->whereIn('leave_type', [
                self::BOTH,
                $bucket === 'sick' ? self::SICK : self::VACATION,
            ]);

        if ($year) {
            $query->whereYear('from_date', $year);
        }

        if ($exceptId) {
            $query->whereKeyNot($exceptId);
        }

        return (float) $query->sum('earned');
    }

    public static function deductionForLeaveType(string $leaveType, mixed $from, mixed $to): float
    {
        if ($leaveType === self::HALFDAY) {
            return 0.5;
        }

        if (! $from || ! $to) {
            return 0;
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->startOfDay();

        if ($end->lessThan($start)) {
            return 0;
        }

        return (float) ($start->diffInDays($end) + 1);
    }

    public static function creditBucketForLeaveType(string $leaveType): string
    {
        return $leaveType === self::SICK ? 'sick' : 'vacation';
    }

    public static function assertCanCreateLeave(array $data, ?int $exceptLeaveId = null): array
    {
        $employeeId = (int) ($data['employee_id'] ?? 0);
        $leaveType = $data['leave_type'] ?? '';
        $deduction = self::deductionForLeaveType($leaveType, $data['leave_from'] ?? null, $data['leave_to'] ?? null);
        $bucket = self::creditBucketForLeaveType($leaveType);
        $credits = self::availableCredits($employeeId, $exceptLeaveId);
        $available = (float) ($credits[$bucket] ?? 0);
        $creditLabel = $bucket === 'sick' ? self::SICK : self::VACATION;

        if ($deduction <= 0) {
            throw ValidationException::withMessages([
                'leave_to' => 'Leave dates are invalid.',
            ]);
        }

        if ($available <= 0) {
            throw ValidationException::withMessages([
                'leave_type' => "Employee has no available {$creditLabel} credits.",
            ]);
        }

        if ($deduction > $available) {
            throw ValidationException::withMessages([
                'leave_to' => "Requested {$leaveType} requires {$deduction} {$creditLabel} credit(s), but only {$available} is available.",
            ]);
        }

        return [
            'deducted_points' => $deduction,
            'is_lwop' => false,
        ];
    }

    public static function assertYearlyEarnedLimit(
        int $employeeId,
        string $leaveType,
        mixed $fromDate,
        float $earned,
        ?int $exceptId = null,
    ): void {
        $year = Carbon::parse($fromDate)->year;
        $buckets = match ($leaveType) {
            self::SICK => ['sick'],
            self::VACATION => ['vacation'],
            default => ['vacation', 'sick'],
        };

        foreach ($buckets as $bucket) {
            $total = self::earnedTotal($employeeId, $bucket, $year, $exceptId) + $earned;

            if ($total > self::YEARLY_MAX) {
                $label = $bucket === 'sick' ? self::SICK : self::VACATION;

                throw ValidationException::withMessages([
                    'from_date' => "{$label} earned credits for {$year} cannot exceed 15. Current total would be {$total}.",
                ]);
            }
        }
    }

    public static function syncEarnedTransaction(LeaveCreditEarned $earned): void
    {
        LeaveCreditTransaction::updateOrCreate(
            [
                'source_type' => LeaveCreditEarned::class,
                'source_id' => $earned->id,
            ],
            [
                'employee_id' => $earned->employee_id,
                'from_date' => $earned->from_date,
                'to_date' => $earned->to_date,
                'particulars' => $earned->particulars,
                'leave_type' => $earned->leave_type,
                'vacation_earned' => in_array($earned->leave_type, [self::VACATION, self::BOTH], true) ? $earned->earned : null,
                'vacation_absence' => null,
                'sick_earned' => in_array($earned->leave_type, [self::SICK, self::BOTH], true) ? $earned->earned : null,
                'sick_absence' => null,
                'is_lwop' => false,
                'action_taken' => null,
            ],
        );
    }

    public static function syncLeaveTransaction(Leave $leave): void
    {
        $bucket = self::creditBucketForLeaveType($leave->leave_type);
        $isRejected = $leave->status === 'Rejected';
        $deducted = ($leave->is_lwop || $isRejected) ? null : $leave->deducted_points;

        LeaveCreditTransaction::updateOrCreate(
            [
                'source_type' => Leave::class,
                'source_id' => $leave->id,
            ],
            [
                'employee_id' => $leave->employee_id,
                'from_date' => $leave->leave_from,
                'to_date' => $leave->leave_to,
                'particulars' => $leave->leave_type,
                'leave_type' => $leave->leave_type,
                'vacation_earned' => null,
                'vacation_absence' => $bucket === 'vacation' ? $deducted : null,
                'sick_earned' => null,
                'sick_absence' => $bucket === 'sick' ? $deducted : null,
                'is_lwop' => (bool) $leave->is_lwop,
                'action_taken' => trim(collect([
                    $leave->status,
                    $leave->approval_comment,
                ])->filter()->implode(' - ')),
            ],
        );
    }

    public static function ledgerRows(int $employeeId): array
    {
        $vacationBalance = 0.0;
        $sickBalance = 0.0;

        return self::ledgerSourceRows($employeeId)
            ->map(function (array $row) use (&$vacationBalance, &$sickBalance): array {
                $vacationBalance += (float) $row['vacation_earned'];
                $vacationBalance -= (float) $row['vacation_absence_value'];
                $sickBalance += (float) $row['sick_earned'];
                $sickBalance -= (float) $row['sick_absence_value'];

                $row['vacation_balance'] = round($vacationBalance, 3);
                $row['sick_balance'] = round($sickBalance, 3);

                unset($row['vacation_absence_value'], $row['sick_absence_value']);

                return $row;
            })
            ->values()
            ->all();
    }

    public static function ledgerSourceRows(int $employeeId): Collection
    {
        $earnedRows = LeaveCreditEarned::query()
            ->where('employee_id', $employeeId)
            ->orderBy('from_date')
            ->orderBy('id')
            ->get()
            ->map(fn (LeaveCreditEarned $earned): array => self::earnedLedgerRow($earned));

        $monetizationRows = LeaveCreditTransaction::query()
            ->where('employee_id', $employeeId)
            ->where('source_type', self::MONETIZATION)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn (LeaveCreditTransaction $transaction): array => [
                'source_type' => self::MONETIZATION,
                'source_id' => $transaction->id,
                'sort_date' => $transaction->created_at,
                'from_date' => null,
                'to_date' => null,
                'from_label' => '',
                'to_label' => self::MONETIZATION,
                'particulars' => self::MONETIZATION,
                'vacation_earned' => null,
                'vacation_absence' => $transaction->vacation_absence,
                'vacation_absence_value' => (float) $transaction->vacation_absence,
                'sick_earned' => null,
                'sick_absence' => null,
                'sick_absence_value' => 0.0,
                'action_taken' => '',
            ]);

        return $earnedRows
            ->merge($monetizationRows)
            ->sortBy([
                ['sort_date', 'asc'],
                ['source_id', 'asc'],
            ])
            ->values();
    }

    public static function monetizeCredits(int $employeeId, float $points = self::MONETIZATION_POINTS): LeaveCreditTransaction
    {
        $credits = self::availableCredits($employeeId);

        if ($credits['total'] < $points || $credits['vacation'] < $points) {
            throw ValidationException::withMessages([
                'points' => "Employee needs at least {$points} available vacation leave credits before monetization.",
            ]);
        }

        return LeaveCreditTransaction::create([
            'employee_id' => $employeeId,
            'source_type' => self::MONETIZATION,
            'source_id' => random_int(1_000_000_000, 9_999_999_999),
            'from_date' => null,
            'to_date' => null,
            'particulars' => self::MONETIZATION,
            'leave_type' => self::MONETIZATION,
            'vacation_earned' => null,
            'vacation_absence' => $points,
            'sick_earned' => null,
            'sick_absence' => null,
            'is_lwop' => false,
            'action_taken' => null,
        ]);
    }

    protected static function earnedLedgerRow(LeaveCreditEarned $earned): array
    {
        $vacationLeaves = self::leavesWithinEarnedPeriod($earned, 'vacation');
        $sickLeaves = self::leavesWithinEarnedPeriod($earned, 'sick');
        $vacationAbsence = (float) $vacationLeaves->where('is_lwop', false)->sum('deducted_points');
        $sickAbsence = (float) $sickLeaves->where('is_lwop', false)->sum('deducted_points');

        return [
            'source_type' => LeaveCreditEarned::class,
            'source_id' => $earned->id,
            'sort_date' => $earned->from_date,
            'from_date' => $earned->from_date,
            'to_date' => $earned->to_date,
            'from_label' => self::formatDate($earned->from_date),
            'to_label' => self::formatDate($earned->to_date),
            'particulars' => $earned->particulars,
            'vacation_earned' => in_array($earned->leave_type, [self::VACATION, self::BOTH], true) ? $earned->earned : null,
            'vacation_absence' => self::absenceDisplay($vacationAbsence, $vacationLeaves),
            'vacation_absence_value' => $vacationAbsence,
            'sick_earned' => in_array($earned->leave_type, [self::SICK, self::BOTH], true) ? $earned->earned : null,
            'sick_absence' => self::absenceDisplay($sickAbsence, $sickLeaves),
            'sick_absence_value' => $sickAbsence,
            'action_taken' => '',
        ];
    }

    protected static function leavesWithinEarnedPeriod(LeaveCreditEarned $earned, string $bucket): Collection
    {
        $leaveTypes = $bucket === 'sick'
            ? [self::SICK]
            : [self::VACATION, self::HALFDAY, self::BIRTHDAY];

        return Leave::query()
            ->where('employee_id', $earned->employee_id)
            ->whereIn('leave_type', $leaveTypes)
            ->where('status', '!=', 'Rejected')
            ->whereDate('leave_from', '>=', $earned->from_date)
            ->whereDate('leave_to', '<=', $earned->to_date)
            ->orderBy('leave_from')
            ->get();
    }

    protected static function absenceDisplay(float $deducted, Collection $leaves): mixed
    {
        if ($deducted > 0) {
            return $deducted;
        }

        return $leaves->contains('is_lwop', true) ? 'LWOP' : null;
    }

    public static function formatDate(mixed $date): string
    {
        if (! $date) {
            return '';
        }

        if ($date instanceof CarbonInterface) {
            return $date->format('m/d/Y');
        }

        return Carbon::parse($date)->format('m/d/Y');
    }
}
