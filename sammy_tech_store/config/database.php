<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'sammy_tech_store';
const DB_USER = 'root';
const DB_PASS = '';

function db(): mysqli
{
    static $connection = null;
    if ($connection instanceof mysqli) {
        return $connection;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    } catch (mysqli_sql_exception $exception) {
        if ((int) $exception->getCode() !== 1049) {
            throw $exception;
        }

        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS);
        $connection->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $connection->select_db(DB_NAME);
        initialize_store_schema($connection);
    }

    $connection->set_charset('utf8mb4');
    migrate_store_schema($connection);
    return $connection;
}

function initialize_store_schema(mysqli $connection): void
{
    $schema = file_get_contents(__DIR__ . '/schema_v2.sql');
    if ($schema === false) {
        throw new RuntimeException('The Sammy Tech database schema could not be read.');
    }

    $connection->multi_query($schema);
    do {
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());
}

function migrate_store_schema(mysqli $connection): void
{
    static $migrated = false;
    if ($migrated) return;
    $migrated = true;
    $queries = [
        "CREATE TABLE IF NOT EXISTS customers (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(150) NOT NULL, phone VARCHAR(50) NOT NULL UNIQUE, email VARCHAR(150) NULL, alternative_phone VARCHAR(50) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE IF NOT EXISTS addresses (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, customer_id INT UNSIGNED NOT NULL, area VARCHAR(100) NOT NULL, address_line VARCHAR(255) NOT NULL, landmark VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (customer_id) REFERENCES customers(id))",
        "CREATE TABLE IF NOT EXISTS payments (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL, payment_method ENUM('cash','mobile_money','bank_transfer') NOT NULL, amount DECIMAL(12,2) NOT NULL, payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending', reference_number VARCHAR(100) NULL, proof_path VARCHAR(255) NULL, confirmed_by INT UNSIGNED NULL, confirmed_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY (confirmed_by) REFERENCES administrators(id))",
        "CREATE TABLE IF NOT EXISTS installation_requests (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, customer_id INT UNSIGNED NOT NULL, service_type VARCHAR(100) NOT NULL, location VARCHAR(255) NOT NULL, preferred_date DATE NULL, installation_charge DECIMAL(12,2) NOT NULL DEFAULT 0, request_status ENUM('new','scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'new', notes TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (customer_id) REFERENCES customers(id))",
        "CREATE TABLE IF NOT EXISTS stock_movements (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, order_id INT UNSIGNED NULL, movement_type ENUM('in','out','adjustment','restored') NOT NULL, quantity INT NOT NULL, notes VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (product_id) REFERENCES products(id), FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL)",
        "CREATE TABLE IF NOT EXISTS login_attempts (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, username VARCHAR(60) NOT NULL, ip_address VARCHAR(45) NOT NULL, attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_login_attempts (username, ip_address, attempted_at))",
        "ALTER TABLE payments ADD COLUMN currency ENUM('USD','SSP') NOT NULL DEFAULT 'USD' AFTER amount",
        "ALTER TABLE payments ADD COLUMN exchange_rate DECIMAL(12,4) NULL AFTER currency",
        "ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NULL AFTER short_description",
        "ALTER TABLE products ADD COLUMN reorder_level INT NOT NULL DEFAULT 5 AFTER stock_quantity",
        "ALTER TABLE orders ADD COLUMN customer_id INT UNSIGNED NULL AFTER id",
        "ALTER TABLE orders ADD COLUMN subtotal DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER status",
        "ALTER TABLE orders ADD COLUMN delivery_charge DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER subtotal",
        "ALTER TABLE orders ADD COLUMN payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' AFTER payment_method",
        "ALTER TABLE orders ADD COLUMN delivery_area VARCHAR(100) NULL AFTER delivery_method",
        "ALTER TABLE orders ADD COLUMN delivery_address VARCHAR(255) NULL AFTER delivery_area",
        "ALTER TABLE orders ADD COLUMN alternative_phone VARCHAR(50) NULL AFTER phone",
        "ALTER TABLE orders ADD COLUMN stock_deducted_at DATETIME NULL AFTER total_amount",
        "ALTER TABLE orders ADD CONSTRAINT fk_order_customer FOREIGN KEY (customer_id) REFERENCES customers(id)",
    ];
    foreach ($queries as $query) { try { $connection->query($query); } catch (mysqli_sql_exception $exception) { if (!in_array((int) $exception->getCode(), [1060, 1061, 1062, 1064, 1091, 1215, 1826], true)) throw $exception; } }
}
