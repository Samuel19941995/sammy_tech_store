<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This command can only be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';
[$script, $username, $password] = array_pad($argv, 3, '');
if ($username === '' || strlen($password) < 12) {
    exit("Usage: php scripts/reset_admin_password.php <username> <password-at-least-12-characters>\n");
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$statement = db()->prepare('UPDATE administrators SET password_hash = ? WHERE username = ?');
$statement->bind_param('ss', $hash, $username); $statement->execute();
if ($statement->affected_rows !== 1) exit("Administrator username not found.\n");
echo "Password updated.\n";
