<?php
$content = file_get_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php');

$useArea = "use App\Models\Area;\n";
$content = str_replace("use App\Models\Table;", "use App\Models\Table;\n" . $useArea, $content);

$seedLogic = <<<'PHP'
        $areaIndoor = Area::create(['name' => 'Indoor']);
        $areaOutdoor = Area::create(['name' => 'Outdoor']);
        $areaVip = Area::create(['name' => 'VIP']);

        $table1 = Table::create(['table_number' => 'T01', 'area_id' => $areaIndoor->id]);
        $table2 = Table::create(['table_number' => 'T02', 'area_id' => $areaOutdoor->id]);
        $table3 = Table::create(['table_number' => 'T03', 'area_id' => $areaVip->id]);
PHP;

// Find the old Table::create and replace it.
$content = preg_replace('/Table::create\(\[\'table_number\' => \'T01\'\]\);/', $seedLogic, $content);

file_put_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php', $content);
echo "DatabaseSeeder updated.\n";
