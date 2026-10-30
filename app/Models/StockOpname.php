<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = ['opname_number', 'opname_date', 'location', 'status', 'user_id', 'notes'];
}

