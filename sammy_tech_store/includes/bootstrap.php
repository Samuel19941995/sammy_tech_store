<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
ini_set('log_errors', '1');
ini_set('display_errors', '0');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();

function e(?string $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function money(float $amount): string {
    $usd = '$' . number_format($amount, 2);
    if (!SHOW_SSP_EQUIVALENT) return $usd;
    return $usd . ' (approx. SSP ' . number_format($amount * SSP_PER_USD, 0) . ')';
}
function csrf_token(): string { if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); } return $_SESSION['csrf_token']; }
function verify_csrf(): void { $token = $_POST['csrf_token'] ?? ''; if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) { http_response_code(419); exit('Your session has expired. Please return to the store and try again.'); } }
function flash(string $key, ?string $message = null): ?string { if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; } $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value; }
function admin_required(): void { if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; } }
function order_number(): string { return 'ST-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2))); }
function valid_phone(string $phone): bool { return (bool) preg_match('/^[0-9+() -]{7,25}$/', $phone); }
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(array_map('intval', cart())); }
function cart_total(mysqli $connection): float { $items = cart_items($connection); return array_sum(array_map(fn($item) => (float) $item['price'] * $item['quantity'], $items)); }
function cart_items(mysqli $connection): array { $cart = cart(); if (!$cart) return []; $ids = array_values(array_filter(array_map('intval', array_keys($cart)))); if (!$ids) return []; $idList = implode(',', $ids); $products = $connection->query("SELECT p.*, c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id IN ($idList) AND p.status='active'")->fetch_all(MYSQLI_ASSOC); foreach ($products as &$product) $product['quantity'] = min((int) $cart[$product['id']], max(1, (int) $product['stock_quantity'])); return $products; }
function payment_status_badge(string $status): string { return in_array($status, ['paid', 'confirmed'], true) ? 'paid' : ($status === 'failed' ? 'cancelled' : 'pending'); }
