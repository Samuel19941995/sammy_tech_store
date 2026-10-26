<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
admin_required();

$connection = db();
$adminId = (int) $_SESSION['admin_id'];
$statement = $connection->prepare('SELECT username, password_hash FROM administrators WHERE id = ?');
$statement->bind_param('i', $adminId);
$statement->execute();
$admin = $statement->get_result()->fetch_assoc();
if (!$admin) { session_destroy(); header('Location: login.php'); exit; }

$message = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $username)) {
        $error = 'Use 3-60 letters, numbers, dots, hyphens, or underscores for the username.';
    } elseif (!password_verify($currentPassword, $admin['password_hash'])) {
        $error = 'Your current password is not correct.';
    } elseif ($newPassword !== '' && strlen($newPassword) < 10) {
        $error = 'Your new password must have at least 10 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'The new password confirmation does not match.';
    } else {
        try {
            if ($newPassword !== '') {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $connection->prepare('UPDATE administrators SET username = ?, password_hash = ? WHERE id = ?');
                $update->bind_param('ssi', $username, $hash, $adminId);
            } else {
                $update = $connection->prepare('UPDATE administrators SET username = ? WHERE id = ?');
                $update->bind_param('si', $username, $adminId);
            }
            $update->execute();
            $admin['username'] = $username;
            $message = 'Account settings saved successfully.';
        } catch (mysqli_sql_exception $exception) {
            $error = (int) $exception->getCode() === 1062 ? 'That username is already being used.' : 'Account settings could not be saved.';
        }
    }
}
echo <<<'STYLE'
<style>
body:before{content:"";position:fixed;inset:0;pointer-events:none;background:linear-gradient(90deg,rgba(11,155,215,.045) 1px,transparent 1px),linear-gradient(rgba(11,155,215,.045) 1px,transparent 1px);background-size:34px 34px;mask-image:linear-gradient(to bottom,black,transparent 67%)}.page{position:relative}.back{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid #dce9df;border-radius:999px;background:rgba(255,255,255,.78);box-shadow:0 8px 22px rgba(16,35,28,.05)}.card{position:relative;overflow:hidden;border:0;border-top:7px solid var(--lime);background:linear-gradient(145deg,#fff,#fbfefb);box-shadow:0 30px 70px rgba(16,35,28,.15)}.card:before{content:"SAMMY TECH  •  ACCOUNT SECURITY";position:absolute;top:0;right:0;padding:12px 20px;border-radius:0 0 0 16px;background:var(--lime);color:#1a2d23;font:700 9px 'DM Mono';letter-spacing:.11em}.card:after{content:"";position:absolute;right:46px;top:66px;width:112px;height:112px;border-radius:50%;background:#0c251e url(../assets/images/sammy-tech-logo.jpeg) center/76px no-repeat;box-shadow:0 0 0 14px #e7f5f6,0 0 0 28px rgba(20,165,204,.11)}.eyebrow{color:#168bb4}.card h1{font-size:52px;position:relative}.card h1:after{content:"";display:block;width:70px;height:5px;margin-top:17px;border-radius:99px;background:linear-gradient(90deg,var(--lime),#1db9e6)}.intro{max-width:690px;font-size:15px}.form{max-width:900px}.section{position:relative;border:1px solid #d9e7dc;background:linear-gradient(145deg,#fcfefc,#f5faf5);box-shadow:inset 0 1px 0 #fff}.section h2{font-size:21px}.section:first-of-type:before{content:"01";position:absolute;right:19px;top:17px;color:#a1b8ab;font:500 24px 'DM Mono'}.section:nth-of-type(2){border-left:5px solid #1b9ec8}.section:nth-of-type(2):before{content:"02";position:absolute;right:19px;top:17px;color:#a1b8ab;font:500 24px 'DM Mono'}.form label{color:#304a3c}.form input{border-color:#c4d7c8;transition:border .18s ease,box-shadow .18s ease}.form input:hover{border-color:#7ba98e}.save{padding:14px 22px;border-radius:10px;box-shadow:0 10px 20px rgba(16,35,28,.18);transition:transform .18s ease,box-shadow .18s ease}.save:hover{transform:translateY(-3px);box-shadow:0 15px 27px rgba(16,35,28,.25)}.hint{padding:12px 15px;border-radius:9px;background:#edf8fb;border-left:4px solid #19a3cb;max-width:760px}@media(max-width:760px){.card:before{position:static;display:inline-block;margin-bottom:18px;border-radius:99px}.card:after{right:-28px;top:160px;opacity:.33}.card h1{font-size:42px}}@media(max-width:600px){.card h1{font-size:36px}.hint{font-size:10px}}
</style>
STYLE;
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Account Settings | Sammy Tech</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"><style>
:root{--ink:#10231c;--paper:#f2f6f1;--line:#dce7de;--muted:#65756b;--lime:#d8e65c}*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 92% 5%,#dff3ef 0,transparent 28%),var(--paper);color:var(--ink);font-family:Manrope,sans-serif}.page{width:min(860px,calc(100% - 32px));margin:58px auto}.back{display:inline-block;color:#286149;font-size:13px;font-weight:800;text-decoration:none}.card{margin-top:20px;padding:38px;border:1px solid var(--line);border-radius:22px;background:#fff;box-shadow:0 24px 60px rgba(16,35,28,.11)}.eyebrow{margin:0 0 11px;color:#128bb5;font:500 10px 'DM Mono';letter-spacing:.14em}.card h1{margin:0;font-size:44px;letter-spacing:-.07em}.intro{margin:12px 0 28px;color:var(--muted);font-size:14px;line-height:1.65}.notice{margin-bottom:20px;padding:13px 15px;border-radius:10px;font-size:12px;font-weight:700}.notice.success{background:#e5f3ca;color:#405e24}.notice.error{background:#f9e0da;color:#914a3d}.form{display:grid;gap:18px}.section{padding:21px;border:1px solid var(--line);border-radius:15px;background:#fbfdfb}.section h2{margin:0 0 6px;font-size:18px;letter-spacing:-.04em}.section p{margin:0 0 18px;color:var(--muted);font-size:12px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:14px}.fields.one{grid-template-columns:1fr}.form label{display:grid;gap:7px;color:#45564c;font-size:11px;font-weight:800}.form input{height:43px;padding:0 12px;border:1px solid #cbd9cd;border-radius:8px;background:#fff;color:var(--ink);font:14px Manrope}.form input:focus{outline:3px solid rgba(11,155,215,.17);border-color:#169aca}.save{justify-self:start;padding:13px 18px;border:0;border-radius:9px;background:var(--ink);color:#fff;font:800 12px Manrope;cursor:pointer}.save:hover{background:#244738}.hint{margin:0;color:var(--muted);font-size:11px;line-height:1.55}@media(max-width:600px){.page{width:min(100% - 20px,860px);margin:24px auto}.card{padding:25px}.card h1{font-size:36px}.fields{grid-template-columns:1fr}.save{width:100%}}
</style></head><body><main class="page"><a class="back" href="index.php">&larr; Return to dashboard</a><section class="card"><p class="eyebrow">STAFF ACCOUNT</p><h1>Account settings.</h1><p class="intro">Update the login information for the administrator currently signed in. Your current password is required to protect the account.</p><?php if($message): ?><div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><form class="form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><section class="section"><h2>Username</h2><p>This is the name used to sign in to the staff area.</p><div class="fields one"><label>USERNAME<input name="username" value="<?= e($admin['username']) ?>" required autocomplete="username"></label></div></section><section class="section"><h2>Change password</h2><p>Leave the new-password fields blank if you only want to change the username.</p><div class="fields one"><label>CURRENT PASSWORD<input type="password" name="current_password" required autocomplete="current-password"></label></div><div class="fields" style="margin-top:14px"><label>NEW PASSWORD<input type="password" name="new_password" autocomplete="new-password"></label><label>CONFIRM NEW PASSWORD<input type="password" name="confirm_password" autocomplete="new-password"></label></div></section><button class="save">Save account settings</button><p class="hint">Use a unique password of at least 10 characters. After changing it, use the new password next time you sign in.</p></form></section></main></body></html>
