<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
require_once __DIR__ . '/../includes/bootstrap.php';
$connection = db(); $failed = false;
function check_status(bool $ok, string $label): void { global $failed; echo ($ok ? '[OK]   ' : '[FAIL] ') . $label . PHP_EOL; if (!$ok) $failed = true; }
$tables = ['administrators','categories','products','orders','order_items','customers','payments','installation_requests','stock_movements','login_attempts'];
foreach ($tables as $table) { $result = $connection->query("SHOW TABLES LIKE '" . $connection->real_escape_string($table) . "'"); check_status($result->num_rows === 1, 'Database table: ' . $table); }
$columns = ['currency','exchange_rate','proof_path']; foreach ($columns as $column) { $result = $connection->query("SHOW COLUMNS FROM payments LIKE '" . $connection->real_escape_string($column) . "'"); check_status($result->num_rows === 1, 'Payment column: ' . $column); }
$products = (int) $connection->query("SELECT COUNT(*) total FROM products WHERE status='active'")->fetch_assoc()['total']; check_status($products > 0, 'Active products: ' . $products);
check_status(is_file(__DIR__ . '/../assets/images/sammy-tech-logo.jpeg'), 'Sammy Tech logo asset');
check_status(SSP_PER_USD === 7800.00, 'SSP exchange rate: 7,800 per USD');
exit($failed ? 1 : 0);
