<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['po_number', 'supplier_id', 'po_date', 'expected_date', 'status', 'total_amount', 'notes'];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}

