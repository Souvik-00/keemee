<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModulePlaceholderController extends Controller
{
    public function show(string $module): View
    {
        $labels = [
            'auth-rbac' => 'Auth + RBAC',
            'masters' => 'Masters',
            'attendance' => 'Attendance',
            'payroll' => 'Payroll',
            'allowances' => 'Allowances',
            'operations' => 'Operations',
            'expenses' => 'Expenses',
            'reports' => 'Reports',
        ];

        abort_unless(array_key_exists($module, $labels), 404);

        return view('modules.placeholder', [
            'module' => $labels[$module],
        ]);
    }
}
