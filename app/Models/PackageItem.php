<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PackageItem extends Model {
    protected $fillable = ['package_id', 'product_id', 'quantity'];
    public function package() { return $this->belongsTo(Product::class, 'package_id'); }
    public function product() { return $this->belongsTo(Product::class, 'product_id'); }
}