<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This command can only be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';
[$script, $username, $password] = array_pad($argv, 3, '');
if ($username === '' || strlen($password) < 12) {
    exit("Usage: php scripts/create_admin.php <username> <password-at-least-12-characters>\n");
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$connection = db();
$statement = $connection->prepare('SELECT id FROM administrators WHERE username = ?');
$statement->bind_param('s', $username); $statement->execute();
if ($statement->get_result()->fetch_assoc()) {
    exit("That username already exists. Use scripts/reset_admin_password.php to change its password, or choose another username.\n");
}
$statement = $connection->prepare('INSERT INTO administrators (username, password_hash) VALUES (?, ?)');
$statement->bind_param('ss', $username, $hash); $statement->execute();
echo "Administrator created.\n";
