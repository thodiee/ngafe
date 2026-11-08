<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\SalaryComponent;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;

class HRSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $thisMonth = $now->month;
        $thisYear = $now->year;
        
        $lastMonthDate = $now->copy()->startOfMonth()->subMonth();
        $lastMonth = $lastMonthDate->month;
        $lastYear = $lastMonthDate->year;

        // 1. Shifts
        $shifts = [
            ['name' => 'Shift Pagi', 'start_time' => '07:00:00', 'end_time' => '15:00:00'],
            ['name' => 'Shift Sore', 'start_time' => '15:00:00', 'end_time' => '23:00:00'],
            ['name' => 'Shift Malam', 'start_time' => '23:00:00', 'end_time' => '07:00:00'],
        ];

        $shiftModels = [];
        foreach ($shifts as $s) {
            $shiftModels[] = Shift::create($s);
        }

        // Fetch staff members to schedule
        $staff = User::whereIn('role', ['cashier', 'kitchen', 'waiter', 'inventory', 'finance'])->get();
        if ($staff->isEmpty()) {
            return;
        }

        // 2. Schedules & Attendances (for last 15 days up to today, and next 7 days)
        // Let's create schedule & attendance records
        for ($day = -15; $day <= 7; $day++) {
            $currentDate = $now->copy()->addDays($day);
            $dateStr = $currentDate->toDateString();

            foreach ($staff as $index => $employee) {
                // Determine shift (rotate)
                $shiftIndex = ($index + abs($day)) % count($shiftModels);
                $shift = $shiftModels[$shiftIndex];

                // Create schedule
                Schedule::create([
                    'user_id' => $employee->id,
                    'shift_id' => $shift->id,
                    'date' => $dateStr,
                ]);

                // Create attendances for past days (day <= 0)
                if ($day <= 0) {
                    $attendanceStatus = 'present';
                    $clockIn = null;
                    $clockOut = null;

                    // Simulate weekend/off days or leaves sometimes (10% chance off)
                    if (($index + abs($day)) % 7 === 0) {
                        continue; // No attendance (off day)
                    }

                    // Leave day simulation (5% chance)
                    if (($index + abs($day)) % 15 === 3 && $day < -5) {
                        // This will be covered by Leave table records
                        continue;
                    }

                    // Clock times
                    $shiftStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
                    $shiftEnd = Carbon::parse($dateStr . ' ' . $shift->end_time);

                    // Lateness simulation (15% chance of being late)
                    $isLate = (($index + abs($day)) % 8 === 0);
                    if ($isLate) {
                        $attendanceStatus = 'late';
                        $minutesLate = rand(16, 45); // Grace period is 15 mins
                        $clockIn = $shiftStart->copy()->addMinutes($minutesLate)->format('H:i:s');
                    } else {
                        $clockIn = $shiftStart->copy()->subMinutes(rand(5, 20))->format('H:i:s');
                    }

                    // Early leave or normal leave (95% normal)
                    $clockOut = $shiftEnd->copy()->addMinutes(rand(1, 15))->format('H:i:s');

                    Attendance::create([
                        'user_id' => $employee->id,
                        'date' => $dateStr,
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => $attendanceStatus,
                        'latitude' => '-6.2' . rand(1000, 9999),
                        'longitude' => '106.8' . rand(1000, 9999),
                        'photo_proof' => 'dummy_photo_' . $employee->id . '_' . $day . '.jpg',
                    ]);
                }
            }
        }

        // 3. Leaves
        foreach ($staff as $index => $employee) {
            if ($index === 0) {
                // Cashier Sarah has an approved annual leave
                Leave::create([
                    'user_id' => $employee->id,
                    'type' => 'annual',
                    'start_date' => $now->copy()->subDays(12)->toDateString(),
                    'end_date' => $now->copy()->subDays(10)->toDateString(),
                    'reason' => 'Acara keluarga di kampung halaman.',
                    'status' => 'approved',
                    'hr_notes' => 'Disetujui. Kuota cuti terpotong.',
                ]);
            } elseif ($index === 1) {
                // Cashier Budi has a pending sick leave
                Leave::create([
                    'user_id' => $employee->id,
                    'type' => 'sick',
                    'start_date' => $now->copy()->addDays(2)->toDateString(),
                    'end_date' => $now->copy()->addDays(3)->toDateString(),
                    'reason' => 'Jadwal kontrol dokter gigi.',
                    'status' => 'pending',
                ]);
            } elseif ($index === 2) {
                // Chef Adi has a rejected leave
                Leave::create([
                    'user_id' => $employee->id,
                    'type' => 'unpaid',
                    'start_date' => $now->copy()->subDays(1)->toDateString(),
                    'end_date' => $now->copy()->subDays(1)->toDateString(),
                    'reason' => 'Keperluan mendadak.',
                    'status' => 'rejected',
                    'hr_notes' => 'Tidak dapat disetujui karena tidak ada pengganti shift.',
                ]);
            }
        }

        // 4. Salary Components (Bonuses/Deductions)
        // Let's create some bonuses and deductions for last month and this month
        foreach ($staff as $employee) {
            // Last month bonus
            SalaryComponent::create([
                'user_id' => $employee->id,
                'type' => 'bonus',
                'name' => 'Bonus Performa Bulanan',
                'amount' => 300000,
                'applied_month' => $lastMonth,
                'applied_year' => $lastYear,
            ]);

            // This month draft bonus
            SalaryComponent::create([
                'user_id' => $employee->id,
                'type' => 'bonus',
                'name' => 'Uang Lembur Event',
                'amount' => 150000,
                'applied_month' => $thisMonth,
                'applied_year' => $thisYear,
            ]);

            // Deduction if they have any lateness (simulated based on attendance)
            $lateDaysThisMonth = Attendance::where('user_id', $employee->id)
                ->whereMonth('date', $thisMonth)
                ->whereYear('date', $thisYear)
                ->where('status', 'late')
                ->count();
            
            if ($lateDaysThisMonth > 0 && $employee->lateness_penalty > 0) {
                SalaryComponent::create([
                    'user_id' => $employee->id,
                    'type' => 'deduction',
                    'name' => 'Potongan Keterlambatan',
                    'amount' => $lateDaysThisMonth * $employee->lateness_penalty,
                    'applied_month' => $thisMonth,
                    'applied_year' => $thisYear,
                ]);
            }
        }

        // 5. Payrolls (Last month: PAID, This month: DRAFT)
        foreach ($staff as $employee) {
            // Count attendance for last month (simulated as 24 days present, 2 days late)
            $presentLastMonth = 24;
            $lateLastMonth = rand(1, 3);
            
            $baseSalary = $employee->base_salary;
            $allowance = $presentLastMonth * $employee->daily_allowance;
            $deduction = $lateLastMonth * $employee->lateness_penalty;
            $bonus = 300000; // performance bonus
            $netSalary = $baseSalary + $allowance - $deduction + $bonus;

            if ($baseSalary > 0) {
                // Save Last Month Payroll (Paid)
                Payroll::updateOrCreate([
                    'user_id' => $employee->id,
                    'period_month' => $lastMonth,
                    'period_year' => $lastYear,
                ], [
                    'present_days' => $presentLastMonth,
                    'late_days' => $lateLastMonth,
                    'base_salary' => $baseSalary,
                    'total_allowance' => $allowance,
                    'total_deduction' => $deduction,
                    'total_bonus' => $bonus,
                    'net_salary' => $netSalary,
                    'status' => 'paid',
                ]);

                // Save This Month Payroll (Draft)
                $presentThisMonth = Attendance::where('user_id', $employee->id)
                    ->whereMonth('date', $thisMonth)
                    ->whereYear('date', $thisYear)
                    ->whereIn('status', ['present', 'late'])
                    ->count();

                $lateThisMonth = Attendance::where('user_id', $employee->id)
                    ->whereMonth('date', $thisMonth)
                    ->whereYear('date', $thisYear)
                    ->where('status', 'late')
                    ->count();

                $allowanceThisMonth = $presentThisMonth * $employee->daily_allowance;
                $deductionThisMonth = $lateThisMonth * $employee->lateness_penalty;
                $bonusThisMonth = 150000; // Uang lembur event
                $netSalaryThisMonth = $baseSalary + $allowanceThisMonth - $deductionThisMonth + $bonusThisMonth;

                Payroll::updateOrCreate([
                    'user_id' => $employee->id,
                    'period_month' => $thisMonth,
                    'period_year' => $thisYear,
                ], [
                    'present_days' => $presentThisMonth,
                    'late_days' => $lateThisMonth,
                    'base_salary' => $baseSalary,
                    'total_allowance' => $allowanceThisMonth,
                    'total_deduction' => $deductionThisMonth,
                    'total_bonus' => $bonusThisMonth,
                    'net_salary' => $netSalaryThisMonth,
                    'status' => 'draft',
                ]);
            }
        }
    }
}
