<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    protected $fillable = ['waste_date', 'location', 'user_id', 'notes'];
}

