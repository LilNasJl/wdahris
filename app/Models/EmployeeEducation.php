<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'school_name',
        'school_level',
        'year_grad',
    ];

    protected function casts(): array
    {
        return [
            'year_grad' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
