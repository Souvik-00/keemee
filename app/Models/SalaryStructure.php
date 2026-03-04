<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'subscriber_id',
        'employee_id',
        'basic_salary',
        'pf_percent',
        'esi_percent',
        'other_deduction_fixed',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'pf_percent' => 'decimal:2',
            'esi_percent' => 'decimal:2',
            'other_deduction_fixed' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
