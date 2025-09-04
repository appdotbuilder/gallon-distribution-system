<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\GallonRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GallonSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users
        $hrAdmin = User::create([
            'name' => 'HR Administrator',
            'email' => 'hr@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin_hr',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $administrator = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin_administrator',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $warehouseAdmin = User::create([
            'name' => 'Warehouse Administrator',
            'email' => 'warehouse@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin_gudang',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample employees
        $employees = [
            ['employee_id' => 'EMP001', 'name' => 'John Smith', 'grade' => 'G7'],
            ['employee_id' => 'EMP002', 'name' => 'Jane Doe', 'grade' => 'G8'],
            ['employee_id' => 'EMP003', 'name' => 'Bob Johnson', 'grade' => 'G9'],
            ['employee_id' => 'EMP004', 'name' => 'Alice Brown', 'grade' => 'G10'],
            ['employee_id' => 'EMP005', 'name' => 'Charlie Wilson', 'grade' => 'G11'],
            ['employee_id' => 'EMP006', 'name' => 'Diana Davis', 'grade' => 'G12'],
            ['employee_id' => 'EMP007', 'name' => 'Eve Martinez', 'grade' => 'G13'],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::create([
                'employee_id' => $employeeData['employee_id'],
                'name' => $employeeData['name'],
                'grade' => $employeeData['grade'],
                'monthly_quota' => Employee::$gradeQuotas[$employeeData['grade']],
                'is_active' => true,
            ]);

            // Create some sample requests for each employee
            GallonRequest::factory()->count(3)->completed()->create([
                'employee_id' => $employee->id,
            ]);

            GallonRequest::factory()->count(1)->ready()->create([
                'employee_id' => $employee->id,
            ]);

            GallonRequest::factory()->count(2)->pending()->create([
                'employee_id' => $employee->id,
            ]);
        }

        // Create additional random employees
        Employee::factory()->count(20)->create()->each(function ($employee) {
            // Create random requests for each employee
            GallonRequest::factory()->count(random_int(2, 8))->create([
                'employee_id' => $employee->id,
            ]);
        });
    }
}