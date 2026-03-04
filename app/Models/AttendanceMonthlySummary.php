<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMonthlySummary extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'subscriber_id',
        'employee_id',
        'site_id',
        'year',
        'month',
        'present_days',
        'absent_days',
        'attendance_percent',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'present_days' => 'integer',
            'absent_days' => 'integer',
            'attendance_percent' => 'decimal:2',
        ];
    }

    public static function calculatePercent(int $presentDays, int $absentDays): float
    {
        $totalDays = $presentDays + $absentDays;

        if ($totalDays <= 0) {
            return 0.00;
        }

        return round(($presentDays / $totalDays) * 100, 2);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
