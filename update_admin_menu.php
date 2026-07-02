<?php
$content = file_get_contents(__DIR__ . '/app/Http/Controllers/AdminController.php');

$useStatements = <<<'PHP'
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\PackageItem;
PHP;

$content = str_replace("use App\Models\Ingredient;", "use App\Models\Ingredient;\n" . $useStatements, $content);

$newMethods = <<<'PHP'
    // --- VARIANTS ---
    public function variants() {
        $products = Product::where('is_package', false)->get();
        $variants = ProductVariant::with(['product', 'options'])->get();
        return view('admin.variants', compact('products', 'variants'));
    }

    public function storeVariant(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'is_required' => 'boolean'
        ]);
        ProductVariant::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'is_required' => $request->has('is_required')
        ]);
        return back()->with('success', 'Grup varian berhasil ditambahkan');
    }

    public function destroyVariant(ProductVariant $variant) {
        $variant->delete();
        return back()->with('success', 'Grup varian dihapus');
    }

    public function storeVariantOption(Request $request) {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'name' => 'required|string',
            'price_adjustment' => 'numeric'
        ]);
        ProductVariantOption::create([
            'product_variant_id' => $request->product_variant_id,
            'name' => $request->name,
            'price_adjustment' => $request->price_adjustment ?? 0
        ]);
        return back()->with('success', 'Opsi varian ditambahkan');
    }

    public function destroyVariantOption(ProductVariantOption $option) {
        $option->delete();
        return back()->with('success', 'Opsi varian dihapus');
    }

    // --- PACKAGES ---
    public function packages() {
        $packages = Product::where('is_package', true)->with('packageItems.product')->get();
        $categories = Category::all();
        $products = Product::where('is_package', false)->get();
        return view('admin.packages', compact('packages', 'categories', 'products'));
    }

    public function storePackage(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
        ]);
        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'is_package' => true
        ]);
        return back()->with('success', 'Paket menu dibuat');
    }

    public function storePackageItem(Request $request) {
        $request->validate([
            'package_id' => 'required|exists:products,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        PackageItem::create($request->all());
        return back()->with('success', 'Produk ditambahkan ke paket');
    }

    public function destroyPackageItem(PackageItem $item) {
        $item->delete();
        return back()->with('success', 'Produk dihapus dari paket');
    }
PHP;

$content = preg_replace('/}\s*$/', "\n" . $newMethods . "\n}\n", $content);

file_put_contents(__DIR__ . '/app/Http/Controllers/AdminController.php', $content);
echo "AdminController updated with Variants and Packages.\n";
