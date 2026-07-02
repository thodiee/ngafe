<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'period_month',
        'period_year',
        'present_days',
        'late_days',
        'base_salary',
        'total_allowance',
        'total_deduction',
        'total_bonus',
        'net_salary',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
