<?php
// Script to generate migrations programmatically
$commands = [
    'make:migration create_customers_table',
    'make:migration create_settings_table',
    'make:migration add_loyalty_to_orders_table'
];

foreach ($commands as $cmd) {
    echo "Running: $cmd\n";
    echo shell_exec("php artisan $cmd");
}
