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