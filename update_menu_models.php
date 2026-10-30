<?php
$modelsDir = __DIR__ . '/app/Models';

$models = [
    'ProductVariant.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariant extends Model {
    protected $fillable = ['product_id', 'name', 'is_required'];
    public function product() { return $this->belongsTo(Product::class); }
    public function options() { return $this->hasMany(ProductVariantOption::class); }
}
FILE,
    'ProductVariantOption.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariantOption extends Model {
    protected $fillable = ['product_variant_id', 'name', 'price_adjustment'];
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
FILE,
    'PackageItem.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PackageItem extends Model {
    protected $fillable = ['package_id', 'product_id', 'quantity'];
    public function package() { return $this->belongsTo(Product::class, 'package_id'); }
    public function product() { return $this->belongsTo(Product::class, 'product_id'); }
}
FILE,
    'OrderItemVariant.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItemVariant extends Model {
    protected $fillable = ['order_item_id', 'variant_name', 'option_name', 'price_adjustment'];
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}
FILE
];

foreach ($models as $name => $content) {
    file_put_contents($modelsDir . '/' . $name, $content);
}

// Update Product model
$productModelPath = $modelsDir . '/Product.php';
$productModel = file_get_contents($productModelPath);
// Add is_package to fillable
$productModel = str_replace("'is_available'", "'is_available', 'is_package'", $productModel);
// Add relations
$relations = <<<'PHP'
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function packageItems() { return $this->hasMany(PackageItem::class, 'package_id'); }
PHP;
$productModel = preg_replace('/}\s*$/', "\n" . $relations . "\n}\n", $productModel);
file_put_contents($productModelPath, $productModel);

// Update OrderItem model
$orderItemPath = $modelsDir . '/OrderItem.php';
$orderItem = file_get_contents($orderItemPath);
$orderItemRelations = <<<'PHP'
    public function variants() { return $this->hasMany(OrderItemVariant::class); }
PHP;
$orderItem = preg_replace('/}\s*$/', "\n" . $orderItemRelations . "\n}\n", $orderItem);
file_put_contents($orderItemPath, $orderItem);

echo "Models generated and updated.\n";
