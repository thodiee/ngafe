<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {
    protected $fillable = ['name', 'icon', 'is_active', 'has_surcharge', 'surcharge_rate'];
}
