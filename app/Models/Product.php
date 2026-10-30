<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['name', 'category_id', 'price', 'image', 'is_available', 'is_package'];
    public function category() { return $this->belongsTo(Category::class); }
    public function recipes() { return $this->hasMany(Recipe::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function packageItems() { return $this->hasMany(PackageItem::class, 'package_id'); }
}
