<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'gender',
        'birthdate',
        'status',
        'address',
        'mobile',
        'kids',
        'email',
        'designation_id',
        'department_id',
        'hired_date',
        'employment_type',
        'school_name',
        'school_level',
        'year_grad',
        'rate_type',
        'payment_type',
        'daily_rate',
        'monthly_rate',
        'allowance',
        'gsis_no',
        'philhealth_no',
        'pagibig_no',
        'tin',
        'sss_no',
    ];

    protected static function booted(): void
    {
        static::creating(function ($employee): void {
            DB::transaction(function () use ($employee): void {
                $counter = Counter::lockForUpdate()->first();

                if (! $counter) {
                    $counter = Counter::create(['uid' => 0]);
                }

                $counter->increment('uid');

                $employee->uid = str_pad($counter->uid, 5, '0', STR_PAD_LEFT);
            });
        });
    }

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'hired_date' => 'date',
            'year_grad' => 'date',
            'daily_rate' => 'float',
            'monthly_rate' => 'float',
        ];
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'employee_id');
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

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->firstname.' '.
            ($this->middlename ? $this->middlename.' ' : '').
            $this->lastname
        );
    }
}
