<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role', 'base_salary', 'daily_allowance', 'lateness_penalty', 'face_reference', 'face_verified_at'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { return ['email_verified_at' => 'datetime', 'password' => 'hashed']; }
    
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function leaves() { return $this->hasMany(Leave::class); }
    public function salaryComponents() { return $this->hasMany(SalaryComponent::class); }
    public function payrolls() { return $this->hasMany(Payroll::class); }
}