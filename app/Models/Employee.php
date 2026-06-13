<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'uid',
        'firstname',
        'middlename',
        'lastname',
        'name_extension',
        'gender',
        'birthdate',
        'place_of_birth',
        'status',
        'citizenship',
        'blood_type',
        'height_cm',
        'weight_kg',
        'address',
        'mobile',
        'kids',
        'email',
        'residential_address',
        'permanent_address',
        'designation_id',
        'department_id',
        'hired_date',
        'original_appointment_date',
        'assumption_date',
        'employment_start_date',
        'employment_end_date',
        'employment_type',
        'salary_grade',
        'salary_step',
        'immediate_supervisor',
        'employment_classification',
        'plantilla_item_number',
        'office_assignment',
        'employment_remarks',
        'school_name',
        'school_level',
        'year_grad',
        'rate_type',
        'payment_type',
        'daily_rate',
        'monthly_rate',
        'allowance',
        'gsis',
        'philhealth',
        'pagibig',
        'tin',
        'sss',
        'philsys',
        'gsis_no',
        'philhealth_no',
        'pagibig_no',
        'sss_no',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            DB::transaction(function () use ($employee): void {
                $counter = Counter::lockForUpdate()->firstOrCreate([], ['uid' => 0]);

                if (filled($employee->uid)) {
                    $employee->uid = static::normalizeEmployeeUid($employee->uid);

                    if ((int) $employee->uid > (int) $counter->uid) {
                        $counter->forceFill(['uid' => (int) $employee->uid])->save();
                    }

                    return;
                }

                $counter->increment('uid');
                $employee->uid = $counter->uid;
            });
        });
    }

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'hired_date' => 'date',
            'original_appointment_date' => 'date',
            'assumption_date' => 'date',
            'employment_start_date' => 'date',
            'employment_end_date' => 'date',
            'year_grad' => 'date',
            'height_cm' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'salary_grade' => 'integer',
            'salary_step' => 'integer',
            'daily_rate' => 'float',
            'monthly_rate' => 'float',
        ];
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employeeDeductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class, 'employee_id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function serviceCredits(): HasMany
    {
        return $this->hasMany(ServiceCredits::class, 'employee_id');
    }

    public function serviceCreditSignatory(): HasOne
    {
        return $this->hasOne(ServiceCreditSignatory::class, 'employee_id');
    }

    public function leaveCreditEarned(): HasMany
    {
        return $this->hasMany(LeaveCreditEarned::class, 'employee_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    public static function normalizeEmployeeUid(mixed $uid): int
    {
        $value = strtoupper(trim((string) $uid));
        $value = str_replace('A685-', '', $value);
        $value = preg_replace('/\D+/', '', $value) ?: '0';

        return (int) $value;
    }

    public static function employeeIdFromUid(mixed $uid): ?string
    {
        if (blank($uid)) {
            return null;
        }

        return 'A685-'.str_pad((string) static::normalizeEmployeeUid($uid), 5, '0', STR_PAD_LEFT);
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->firstname,
            $this->middlename,
            $this->lastname,
            $this->name_extension,
        ])->filter()->implode(' ');
    }
}
