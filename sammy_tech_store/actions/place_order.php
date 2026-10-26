<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
flash('error', 'Please add items to your cart, then use the secure checkout.');
header('Location: ../index.php#shop'); exit;
