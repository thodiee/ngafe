<?php

// Re-write update_migrations.php
$migrationsScript = <<<'PHP'
<?php
$migrationsDir = __DIR__ . '/database/migrations';
$files = glob($migrationsDir . '/*.php');
foreach ($files as $file) {
    unlink($file);
}

$migrations = [
    '0001_01_01_000000_create_users_table.php' => <<<'FILE'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('cashier'); // admin, cashier, hr, inventory
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
FILE,
    '2026_01_01_000001_create_hr_tables.php' => <<<'FILE'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->date('date');
            $table->timestamps();
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status')->default('present');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('shifts');
    }
};
FILE,
    '2026_01_01_000002_create_inventory_tables.php' => <<<'FILE'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stock')->default(0);
            $table->string('unit');
            $table->integer('minimum_stock')->default(0);
            $table->timestamps();
        });
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
FILE,
    '2026_01_01_000003_create_pos_tables.php' => <<<'FILE'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number');
            $table->string('qr_token')->nullable();
            $table->string('status')->default('empty');
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_reference');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->string('order_type'); // dine_in, takeaway
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string('order_status')->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tables');
    }
};
FILE,
    '2026_01_01_000004_create_ats_tables.php' => <<<'FILE'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->string('status')->default('published');
            $table->timestamps();
        });
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->onDelete('cascade');
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('cv_path');
            $table->string('status')->default('submitted');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_vacancies');
    }
};
FILE
];
foreach ($migrations as $name => $content) {
    file_put_contents($migrationsDir . '/' . $name, $content);
}
echo "Migrations rewritten.\n";
PHP;

file_put_contents(__DIR__ . '/update_migrations.php', $migrationsScript);


$modelsScript = <<<'PHP'
<?php
$modelsDir = __DIR__ . '/app/Models';
$models = [
    'User.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { return ['email_verified_at' => 'datetime', 'password' => 'hashed']; }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}
FILE,
    'Category.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    protected $fillable = ['name'];
    public function products() { return $this->hasMany(Product::class); }
}
FILE,
    'Product.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['name', 'category_id', 'price', 'image', 'is_available'];
    public function category() { return $this->belongsTo(Category::class); }
    public function recipes() { return $this->hasMany(Recipe::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}
FILE,
    'Ingredient.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Ingredient extends Model {
    protected $fillable = ['name', 'stock', 'unit', 'minimum_stock'];
    public function recipes() { return $this->hasMany(Recipe::class); }
}
FILE,
    'Recipe.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Recipe extends Model {
    protected $fillable = ['product_id', 'ingredient_id', 'quantity'];
    public function product() { return $this->belongsTo(Product::class); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }
}
FILE,
    'Table.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Table extends Model {
    protected $fillable = ['table_number', 'qr_token', 'status'];
    public function orders() { return $this->hasMany(Order::class); }
}
FILE,
    'Order.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['order_reference', 'table_id', 'order_type', 'total_amount', 'payment_method', 'payment_status', 'order_status', 'gateway_transaction_id'];
    public function items() { return $this->hasMany(OrderItem::class); }
    public function table() { return $this->belongsTo(Table::class); }
}
FILE,
    'OrderItem.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'notes'];
    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
FILE,
    'Shift.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shift extends Model {
    protected $fillable = ['name', 'start_time', 'end_time'];
    public function schedules() { return $this->hasMany(Schedule::class); }
}
FILE,
    'Schedule.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Schedule extends Model {
    protected $fillable = ['user_id', 'shift_id', 'date'];
    public function user() { return $this->belongsTo(User::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
}
FILE,
    'Attendance.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Attendance extends Model {
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'status'];
    public function user() { return $this->belongsTo(User::class); }
}
FILE,
    'JobVacancy.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobVacancy extends Model {
    protected $fillable = ['title', 'description', 'requirements', 'status'];
    public function applications() { return $this->hasMany(JobApplication::class); }
}
FILE,
    'JobApplication.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobApplication extends Model {
    protected $fillable = ['job_vacancy_id', 'applicant_name', 'email', 'phone_number', 'cv_path', 'status'];
    public function jobVacancy() { return $this->belongsTo(JobVacancy::class); }
}
FILE
];
foreach ($models as $name => $content) {
    file_put_contents($modelsDir . '/' . $name, $content);
}
echo "Models rewritten.\n";
PHP;

file_put_contents(__DIR__ . '/update_models.php', $modelsScript);
