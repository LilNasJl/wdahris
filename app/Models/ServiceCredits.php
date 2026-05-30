<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCredits extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
        'designation',
        'status',
        'salary',
        'station_place',
        'branch',
        'leave_without_pay',
        'separation_date',
        'separation_case',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
            'separation_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (ServiceCredits $serviceCredit): void {
            self::syncEmployeeCurrentAppointment((int) $serviceCredit->employee_id);
        });

        static::deleted(function (ServiceCredits $serviceCredit): void {
            self::syncEmployeeCurrentAppointment((int) $serviceCredit->employee_id);
        });

        static::restored(function (ServiceCredits $serviceCredit): void {
            self::syncEmployeeCurrentAppointment((int) $serviceCredit->employee_id);
        });
    }

    public static function syncEmployeeCurrentAppointment(int $employeeId): bool
    {
        $employee = Employee::query()->find($employeeId);

        if (! $employee) {
            return false;
        }

        $current = self::query()
            ->where('employee_id', $employeeId)
            ->latest('to_date')
            ->latest('from_date')
            ->latest()
            ->first();

        if (! $current) {
            return false;
        }

        $updates = [
            'employment_type' => $current->status,
        ];

        $designationId = Designation::query()
            ->where('title', $current->designation)
            ->value('id');

        if ($designationId) {
            $updates['designation_id'] = $designationId;
        }

        if (filled($current->salary)) {
            $monthlySalary = (float) $current->salary;

            $updates['monthly_rate'] = $monthlySalary;
            $updates['daily_rate'] = round($monthlySalary / 22, 2);
        }

        return $employee->forceFill($updates)->save();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
