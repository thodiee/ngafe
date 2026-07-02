<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItemVariant extends Model {
    protected $fillable = ['order_item_id', 'variant_name', 'option_name', 'price_adjustment'];
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}