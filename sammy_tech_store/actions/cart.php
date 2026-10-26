<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../index.php'); exit; }
verify_csrf();
$action = $_POST['action'] ?? 'add'; $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT); $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
if (!$productId) { flash('error', 'Please select a valid product.'); header('Location: ../index.php'); exit; }
$_SESSION['cart'] ??= [];
if ($action === 'remove') unset($_SESSION['cart'][$productId]); elseif ($action === 'update') $_SESSION['cart'][$productId] = $quantity; else $_SESSION['cart'][$productId] = min(99, (int) ($_SESSION['cart'][$productId] ?? 0) + $quantity);
flash('success', $action === 'add' ? 'Item added to your cart.' : 'Cart updated.'); header('Location: ../cart.php'); exit;
