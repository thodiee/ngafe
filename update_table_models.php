<?php
$modelsDir = __DIR__ . '/app/Models';
$models = [
    'Table.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Table extends Model {
    protected $fillable = ['table_number', 'area', 'qr_token', 'status'];
    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}
FILE,
    'Reservation.php' => <<<'FILE'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model {
    protected $fillable = ['table_id', 'customer_name', 'customer_phone', 'reservation_time', 'status'];
    protected $casts = [ 'reservation_time' => 'datetime' ];
    public function table() { return $this->belongsTo(Table::class); }
}
FILE
];
foreach ($models as $name => $content) {
    file_put_contents($modelsDir . '/' . $name, $content);
}
echo "Models updated.\n";
