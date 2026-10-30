<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
<<<<<<< HEAD
    protected $fillable = ['name', 'phone', 'address', 'dob', 'points'];
=======
    protected $guarded = [];
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70

    public function orders() {
        return $this->hasMany(Order::class);
    }
}
