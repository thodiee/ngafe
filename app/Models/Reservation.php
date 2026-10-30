<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model {
    protected $fillable = ['table_id', 'customer_name', 'customer_phone', 'reservation_time', 'status'];
    protected $casts = [ 'reservation_time' => 'datetime' ];
    public function table() { return $this->belongsTo(Table::class); }
}