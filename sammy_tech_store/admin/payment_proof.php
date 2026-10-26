<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
admin_required();
$paymentId = filter_input(INPUT_GET, 'payment_id', FILTER_VALIDATE_INT); if (!$paymentId) { http_response_code(404); exit('Payment proof not found.'); }
$statement = db()->prepare('SELECT proof_path FROM payments WHERE id = ?'); $statement->bind_param('i', $paymentId); $statement->execute(); $payment = $statement->get_result()->fetch_assoc();
$filename = basename((string) ($payment['proof_path'] ?? '')); $path = PRIVATE_STORAGE_PATH . DIRECTORY_SEPARATOR . 'payment_proofs' . DIRECTORY_SEPARATOR . $filename;
if ($filename === '' || !is_file($path)) { http_response_code(404); exit('Payment proof not found.'); }
$finfo = new finfo(FILEINFO_MIME_TYPE); $mime = $finfo->file($path); $allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']; if (!in_array($mime, $allowed, true)) { http_response_code(403); exit('Unsupported file.'); }
header('Content-Type: ' . $mime); header('Content-Length: ' . (string) filesize($path)); header('X-Content-Type-Options: nosniff'); header('Content-Disposition: inline; filename="payment-proof.' . pathinfo($filename, PATHINFO_EXTENSION) . '"'); readfile($path);
