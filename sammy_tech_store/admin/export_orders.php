<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
admin_required();
require __DIR__ . '/export_orders_ready.php'; exit;
$rows = db()->query("SELECT o.order_number,o.created_at,o.customer_name,o.phone,o.email,o.delivery_method,o.delivery_area,o.delivery_address,o.payment_method,o.payment_status,o.status,o.subtotal,o.delivery_charge,o.total_amount,GROUP_CONCAT(CONCAT(oi.quantity,' x ',oi.product_name) SEPARATOR '; ') items FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id GROUP BY o.id ORDER BY o.created_at DESC");
header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="sammy-tech-orders-' . date('Y-m-d') . '.csv"'); $output = fopen('php://output', 'wb'); fputcsv($output, ['Order number','Date','Customer','Phone','Email','Delivery','Area','Address','Payment method','Payment status','Order status','Subtotal USD','Delivery USD','Total USD','Items']); while($row=$rows->fetch_assoc()) fputcsv($output, $row); fclose($output);
