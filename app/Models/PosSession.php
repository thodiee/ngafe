<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model {
    protected $fillable = ['user_id', 'opening_time', 'closing_time', 'opening_cash', 'closing_cash_system', 'closing_cash_actual', 'difference', 'status', 'notes'];
    
    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function orders() {
        return $this->hasMany(Order::class);
    }
}