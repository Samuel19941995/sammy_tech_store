<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
admin_required();

function backup_identifier(string $value): string
{
    return '`' . str_replace('`', '``', $value) . '`';
}

function backup_sql_value(mysqli $connection, mixed $value): string
{
    return $value === null ? 'NULL' : "'" . $connection->real_escape_string((string) $value) . "'";
}

if (isset($_GET['download']) && $_GET['download'] === 'database') {
    $connection = db();
    $tables = [];
    $result = $connection->query('SHOW TABLES');
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $filename = 'sammy-tech-backup-' . date('Y-m-d-His') . '.sql';
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, private');

    echo "-- Sammy Tech Store database backup\n";
    echo "-- Created " . date('c') . "\n\n";
    echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        $quotedTable = backup_identifier($table);
        $create = $connection->query('SHOW CREATE TABLE ' . $quotedTable)->fetch_assoc();
        echo "DROP TABLE IF EXISTS {$quotedTable};\n";
        echo $create['Create Table'] . ";\n\n";

        $rows = $connection->query('SELECT * FROM ' . $quotedTable);
        while ($row = $rows->fetch_assoc()) {
            $columns = array_map('backup_identifier', array_keys($row));
            $values = array_map(fn ($value) => backup_sql_value($connection, $value), array_values($row));
            echo 'INSERT INTO ' . $quotedTable . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ");\n";
        }
        echo "\n";
    }

    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Backup Centre | Sammy Tech</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--ink:#10231c;--paper:#f2f6f1;--lime:#d8e65c;--line:#dbe6dc;--muted:#65746a}*{box-sizing:border-box}body{margin:0;background:linear-gradient(135deg,#f8fbf8,#edf5ef);color:var(--ink);font-family:Manrope,sans-serif}.page{width:min(900px,calc(100% - 32px));margin:60px auto}.back{color:#30604b;font-size:13px;font-weight:700;text-decoration:none}.card{margin-top:20px;padding:38px;border:1px solid var(--line);border-radius:22px;background:#fff;box-shadow:0 20px 55px rgba(16,35,28,.1)}.eyebrow{margin:0 0 12px;color:var(--muted);font:500 10px 'DM Mono';letter-spacing:.14em}.card h1{margin:0;font-size:42px;letter-spacing:-.065em}.intro{max-width:620px;margin:14px 0 28px;color:var(--muted);font-size:14px;line-height:1.7}.backup{padding:24px;border-radius:16px;background:linear-gradient(135deg,#10251d,#244b3a);color:#fff}.backup h2{margin:0;font-size:22px;letter-spacing:-.045em}.backup p{margin:9px 0 18px;color:#c9ddd1;font-size:13px;line-height:1.6}.download{display:inline-block;padding:13px 17px;border-radius:9px;background:var(--lime);color:#14261e;font-size:12px;font-weight:800;text-decoration:none}.tips{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;margin-top:22px}.tips article{padding:16px;border:1px solid var(--line);border-radius:13px}.tips b{font-size:13px}.tips p{margin:7px 0 0;color:var(--muted);font-size:11px;line-height:1.55}@media(max-width:620px){.page{width:min(100% - 20px,900px);margin:20px auto}.card{padding:25px}.card h1{font-size:34px}.tips{grid-template-columns:1fr}}
</style>
<style>
body:before{content:"";position:fixed;inset:0;pointer-events:none;background:linear-gradient(90deg,rgba(11,155,215,.045) 1px,transparent 1px),linear-gradient(rgba(11,155,215,.045) 1px,transparent 1px);background-size:34px 34px;mask-image:linear-gradient(to bottom,black,transparent 68%)}.page{position:relative}.back{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid #dce9df;border-radius:999px;background:rgba(255,255,255,.7);box-shadow:0 8px 22px rgba(16,35,28,.05)}.card{position:relative;isolation:isolate;border:0;background:linear-gradient(145deg,#fff,#fbfefb);box-shadow:0 30px 70px rgba(16,35,28,.14)}.card:before{content:"SAMMY TECH  •  SECURE OPERATIONS";position:absolute;top:0;right:0;padding:12px 20px;border-radius:0 22px 0 16px;background:var(--lime);color:#1a2d23;font:700 9px 'DM Mono';letter-spacing:.11em}.card:after{content:"";position:absolute;right:-96px;top:115px;width:260px;height:260px;border:36px solid rgba(11,155,215,.08);border-radius:50%;z-index:-1}.eyebrow{color:#1789b2}.card h1{font-size:52px;position:relative}.card h1:after{content:"";display:block;width:72px;height:5px;margin-top:17px;border-radius:99px;background:linear-gradient(90deg,var(--lime),#1db9e6)}.intro{font-size:15px}.backup{position:relative;overflow:hidden;padding:30px 34px;border:1px solid rgba(216,230,92,.28);box-shadow:0 16px 32px rgba(16,35,28,.17)}.backup:after{content:"";position:absolute;right:32px;bottom:-24px;width:150px;height:150px;border-radius:50%;background:#10251d url("../assets/images/sammy-tech-logo.jpeg") center/82px no-repeat;box-shadow:0 0 0 20px rgba(255,255,255,.07),0 0 0 40px rgba(255,255,255,.04);opacity:.92}.backup h2,.backup p,.download{position:relative;z-index:1}.backup h2:before{content:"01";display:inline-grid;place-items:center;width:27px;height:27px;margin-right:10px;border:1px solid rgba(216,230,92,.45);border-radius:50%;color:var(--lime);font:500 10px 'DM Mono';vertical-align:3px}.download{transition:transform .18s ease,box-shadow .18s ease}.download:hover{transform:translateY(-3px);box-shadow:0 10px 22px rgba(0,0,0,.22)}.tips{counter-reset:backup-steps}.tips article{position:relative;padding:22px 18px 18px 58px;background:linear-gradient(145deg,#fff,#f8fcf8);box-shadow:0 10px 22px rgba(16,35,28,.04);transition:transform .18s ease}.tips article:hover{transform:translateY(-4px)}.tips article:before{counter-increment:backup-steps;content:"0" counter(backup-steps);position:absolute;left:17px;top:18px;display:grid;place-items:center;width:29px;height:29px;border-radius:50%;background:#e6f5f7;color:#126d8e;font:500 10px 'DM Mono'}.tips article:nth-child(2):before{background:#f7efdb;color:#8b6619}.tips article:nth-child(3):before{background:#edf5d0;color:#59732c}@media(max-width:620px){.card:before{position:static;display:inline-block;margin-bottom:20px;border-radius:99px}.card h1{font-size:40px}.backup{padding:25px 20px}.backup:after{right:-35px;bottom:-34px;opacity:.34}.tips article{padding-left:55px}}
</style>
</head>
<body>
<main class="page">
<a class="back" href="index.php">&larr; Return to dashboard</a>
<section class="card">
<p class="eyebrow">SYSTEM PROTECTION</p>
<h1>Backup Centre.</h1>
<p class="intro">Download a complete copy of the Sammy Tech database, including products, customers, orders, payments, installation requests, and staff accounts.</p>
<section class="backup"><h2>Complete database backup</h2><p>The backup downloads directly to your computer as an SQL file. It is not kept in a public website folder.</p><a class="download" href="backup.php?download=database">Download database backup</a></section>
<section class="tips"><article><b>When to back up</b><p>Download a backup each week and before making major catalogue changes.</p></article><article><b>Keep it private</b><p>The file contains customer and staff data. Store it in a secure location.</p></article><article><b>How to restore</b><p>Use phpMyAdmin to import the SQL file if the database ever needs to be recovered.</p></article></section>
</section>
</main>
</body>
</html>
