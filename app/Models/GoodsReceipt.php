<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = ['gr_number', 'purchase_order_id', 'supplier_id', 'receipt_date', 'notes'];
}

