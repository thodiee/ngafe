<?php
$modelsDir = __DIR__ . '/app/Models';
$models = [
    'Area.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Area extends Model {
    protected $fillable = ['name'];
    public function tables() { return $this->hasMany(Table::class); }
}
FILE,
    'Table.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Table extends Model {
    protected $fillable = ['table_number', 'area_id', 'qr_token', 'status'];
    public function area() { return $this->belongsTo(Area::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}
FILE
];
foreach ($models as $name => $content) {
    file_put_contents($modelsDir . '/' . $name, $content);
}
echo "Models Area and Table updated.\n";
