<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    protected $guarded = [];

    public function ingredient() {
        return $this->belongsTo(Ingredient::class);
    }
}

