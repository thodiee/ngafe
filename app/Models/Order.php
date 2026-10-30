<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
<<<<<<< HEAD
    protected $fillable = [
        'order_reference', 'table_id', 'pos_session_id', 'customer_id',
        'order_type', 'total_amount', 'points_earned', 'points_used',
        'discount_amount', 'payment_method', 'payment_status', 'order_status',
        'gateway_transaction_id'
    ];
=======
    protected $guarded = [];
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
    public function items() { return $this->hasMany(OrderItem::class); }
    public function table() { return $this->belongsTo(Table::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}