<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\SalaryComponent;
use App\Models\Payroll;
use Carbon\Carbon;

class HrAdvancedController extends Controller
{
    // --- SHIFTS ---
    public function shifts() {
        $shifts = Shift::all();
        return view('admin.hr.shifts', compact('shifts'));
    }

    public function storeShift(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        Shift::create($validated);
        return back()->with('success', 'Shift berhasil ditambahkan');
    }

    public function updateShift(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        Shift::findOrFail($id)->update($validated);
        return back()->with('success', 'Shift berhasil diperbarui. Catatan: Ini akan mempengaruhi jadwal semua pegawai yang berada pada shift ini.');
    }

    public function destroyShift($id) {
        Shift::findOrFail($id)->delete();
        return back()->with('success', 'Shift dihapus');
    }

    // --- SCHEDULES (Jadwal Shift) ---
    public function schedules(Request $request) {
        $users = User::whereNotIn('role', ['admin', 'owner'])->get();
        $shifts = Shift::all();
        
        $weekStart = Carbon::now()->startOfWeek();
        if ($request->week) {
            $weekStart = Carbon::parse($request->week)->startOfWeek();
        }
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Fetch schedules for the selected week
        $schedules = Schedule::whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])->get();
        
        return view('admin.hr.schedules', compact('users', 'shifts', 'schedules', 'weekStart', 'weekEnd'));
    }

    public function storeSchedule(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate([
=======
        $request->validate([
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date'
        ]);

        // Check if already scheduled
<<<<<<< HEAD
        $existing = Schedule::where('user_id', $validated['user_id'])->where('date', $validated['date'])->first();
        if ($existing) {
            $existing->update(['shift_id' => $validated['shift_id']]);
        } else {
            Schedule::create($validated);
=======
        $existing = Schedule::where('user_id', $request->user_id)->where('date', $request->date)->first();
        if ($existing) {
            $existing->update(['shift_id' => $request->shift_id]);
        } else {
            Schedule::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        }
        return back()->with('success', 'Jadwal berhasil disimpan');
    }

    public function autoGenerateSchedule(Request $request) {
        $request->validate(['week' => 'required|date']);
        $weekStart = Carbon::parse($request->week)->startOfWeek();
        
        $users = User::whereNotIn('role', ['admin', 'owner'])->get();
        $shifts = Shift::all();
        
        if ($shifts->isEmpty()) {
            return back()->with('error', 'Belum ada Master Shift yang dibuat. Silakan buat shift terlebih dahulu.');
        }

        // Loop over 7 days of the week
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $weekStart->copy()->addDays($i)->format('Y-m-d');
            
            // Loop over users
            $userIndex = 0;
            foreach ($users as $user) {
                // Round-robin distribution of shifts, shifted by the day index so it rotates
                $shiftIndex = ($userIndex + $i) % $shifts->count();
                $assignedShift = $shifts[$shiftIndex];
                
                // If weekend (Saturday/Sunday), maybe don't schedule someone? 
                // Or just schedule them anyway. We'll schedule everyone to ensure coverage.
                
                // Check if already scheduled (don't overwrite if manual exists)
                $existing = Schedule::where('user_id', $user->id)->where('date', $currentDate)->first();
                if (!$existing) {
                    Schedule::create([
                        'user_id' => $user->id,
                        'shift_id' => $assignedShift->id,
                        'date' => $currentDate
                    ]);
                }
                
                $userIndex++;
            }
        }
        
        return back()->with('success', 'Jadwal otomatis berhasil digenerate (jadwal yang sudah ada tidak ditimpa).');
    }

    // --- LEAVES (Persetujuan Cuti/Izin) ---
    public function leaves() {
        $leaves = Leave::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.hr.leaves', compact('leaves'));
    }

    public function approveLeave(Request $request, $id) {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'approved', 'hr_notes' => $request->hr_notes]);
        return back()->with('success', 'Cuti disetujui');
    }

    public function rejectLeave(Request $request, $id) {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'rejected', 'hr_notes' => $request->hr_notes]);
        return back()->with('success', 'Cuti ditolak');
    }

    // --- SALARY COMPONENTS (Bonus & Potongan) ---
    public function components(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $components = SalaryComponent::with('user')
            ->where('applied_month', $month)
            ->where('applied_year', $year)
            ->get();
            
        $users = User::whereNotIn('role', ['admin', 'owner'])->get();
        
        return view('admin.hr.components', compact('components', 'users', 'month', 'year'));
    }

    public function storeComponent(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:bonus,deduction',
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'applied_month' => 'required|integer|min:1|max:12',
            'applied_year' => 'required|integer|min:2000'
        ]);
        SalaryComponent::create($validated);
        return back()->with('success', 'Komponen gaji ditambahkan');
    }

    public function destroyComponent($id) {
        SalaryComponent::findOrFail($id)->delete();
        return back()->with('success', 'Komponen gaji dihapus');
    }

    // --- PAYROLL (Penggajian) ---
    public function payroll(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $payrolls = Payroll::with('user')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get();
            
        return view('admin.hr.payroll', compact('payrolls', 'month', 'year'));
    }

    public function generatePayroll(Request $request) {
        $month = $request->month;
        $year = $request->year;
        
        if (!$month || !$year) {
            return back()->with('error', 'Pilih bulan dan tahun');
        }

        $users = User::whereNotIn('role', ['admin', 'owner'])->get();
        
        foreach ($users as $user) {
            // Check if payroll already exists
            $existingPayroll = Payroll::where('user_id', $user->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->first();
                
            if ($existingPayroll && $existingPayroll->status === 'paid') {
                continue; // Skip if already paid
            }

            // Calculate attendances
            $attendances = Attendance::where('user_id', $user->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();
                
            $presentDays = $attendances->count();
            $lateDays = 0; // Simple logic: if clock_in > 08:15 (adjust to your logic)
            // Example late logic: Assuming shift starts at 08:00, late if > 08:15
            // In a real app, you compare with Schedule/Shift start_time.
            // For simplicity, we just check if clock_in > 08:15:00
            foreach ($attendances as $att) {
                if ($att->clock_in && $att->clock_in > '08:15:00') {
                    $lateDays++;
                }
            }
            
            // Total Uang Makan
            $totalAllowance = $presentDays * $user->daily_allowance;
            
            // Total Potongan Keterlambatan
            $latenessDeduction = $lateDays * $user->lateness_penalty;
            
            // Additional Components
            $components = SalaryComponent::where('user_id', $user->id)
                ->where('applied_month', $month)
                ->where('applied_year', $year)
                ->get();
                
            $additionalBonus = $components->where('type', 'bonus')->sum('amount');
            $additionalDeduction = $components->where('type', 'deduction')->sum('amount');
            
            $totalBonus = $additionalBonus;
            $totalDeduction = $latenessDeduction + $additionalDeduction;
            
            // Net Salary
            $netSalary = $user->base_salary + $totalAllowance + $totalBonus - $totalDeduction;
            
            if ($existingPayroll) {
                $existingPayroll->update([
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'base_salary' => $user->base_salary,
                    'total_allowance' => $totalAllowance,
                    'total_deduction' => $totalDeduction,
                    'total_bonus' => $totalBonus,
                    'net_salary' => $netSalary,
                ]);
            } else {
                Payroll::create([
                    'user_id' => $user->id,
                    'period_month' => $month,
                    'period_year' => $year,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'base_salary' => $user->base_salary,
                    'total_allowance' => $totalAllowance,
                    'total_deduction' => $totalDeduction,
                    'total_bonus' => $totalBonus,
                    'net_salary' => $netSalary,
                    'status' => 'draft'
                ]);
            }
        }

        return back()->with('success', 'Penggajian bulan ' . $month . '/' . $year . ' berhasil digenerate.');
    }
}
