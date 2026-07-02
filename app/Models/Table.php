<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Table extends Model {
    protected $fillable = ['table_number', 'area_id', 'qr_token', 'status'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($table) {
            if (empty($table->qr_token)) {
                $table->qr_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function area() { return $this->belongsTo(Area::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}