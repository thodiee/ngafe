<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'type', // bonus, deduction
        'name',
        'amount',
        'applied_month',
        'applied_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
