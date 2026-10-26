<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = null;
echo '<style>.admin-brand span{display:none!important}.admin-brand:before{content:"";width:42px;height:42px;border-radius:50%;background:#07182e url("../assets/images/sammy-tech-logo.jpeg") center/cover no-repeat;box-shadow:0 0 0 2px #0ea5e9;flex:none}</style>';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $username = trim((string) ($_POST['username'] ?? '')); $password = (string) ($_POST['password'] ?? '');
    $connection = db(); $ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 0, 45);
    $connection->query("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $statement = $connection->prepare('SELECT COUNT(*) attempts FROM login_attempts WHERE username = ? AND ip_address = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)'); $statement->bind_param('ss', $username, $ip); $statement->execute(); $attempts = (int) $statement->get_result()->fetch_assoc()['attempts'];
    if ($attempts >= 5) { $error = 'Too many sign-in attempts. Please try again in 15 minutes.'; }
    else { $statement = $connection->prepare('SELECT id, password_hash FROM administrators WHERE username = ?'); $statement->bind_param('s', $username); $statement->execute(); $admin = $statement->get_result()->fetch_assoc();
        if ($admin && password_verify($password, $admin['password_hash'])) { $statement = $connection->prepare('DELETE FROM login_attempts WHERE username = ? AND ip_address = ?'); $statement->bind_param('ss', $username, $ip); $statement->execute(); session_regenerate_id(true); $_SESSION['admin_id'] = $admin['id']; header('Location: index.php'); exit; }
        $statement = $connection->prepare('INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)'); $statement->bind_param('ss', $username, $ip); $statement->execute(); $error = 'Those credentials were not recognized.';
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sammy Tech | Staff sign in</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="../assets/css/admin.css"></head><body class="login-page"><main class="login-box"><a class="admin-brand" href="../index.php"><span>ST</span> SAMMY TECH</a><p class="eyebrow">Staff workspace</p><h1>Welcome back.</h1><p class="login-copy">Use the staff credentials created during installation.</p><?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button type="submit">Sign in <b>→</b></button></form><a class="back" href="../index.php">← Return to storefront</a></main></body></html>
