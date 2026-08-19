<?php

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    $columns = [
        ['restaurant_table', 'order_token', "ALTER TABLE restaurant_table ADD COLUMN order_token VARCHAR(64) UNIQUE NULL"],
        ['sale_order', 'source', "ALTER TABLE sale_order ADD COLUMN source ENUM('internal','qr') DEFAULT 'internal'"],
        ['sale_order', 'customer_name', "ALTER TABLE sale_order ADD COLUMN customer_name VARCHAR(100) NULL"],
        ['sale_order', 'customer_phone', "ALTER TABLE sale_order ADD COLUMN customer_phone VARCHAR(30) NULL"],
        ['inventory_receipt', 'status', "ALTER TABLE inventory_receipt ADD COLUMN status ENUM('pending','completed') DEFAULT 'pending'"],
        ['inventory_issue', 'status', "ALTER TABLE inventory_issue ADD COLUMN status ENUM('pending','completed') DEFAULT 'pending' AFTER issue_date"],
    ];

    foreach ($columns as [$table, $column, $sql]) {
        $stmt = $db->query("SHOW COLUMNS FROM {$table} LIKE " . $db->quote($column));
        if (!$stmt->fetch()) {
            $db->exec($sql);
            echo "Added {$table}.{$column}\n";
        } else {
            echo "{$table}.{$column} exists\n";
        }
    }

    $db->exec("ALTER TABLE inventory_receipt_detail MODIFY qty DECIMAL(10,3)");
    $db->exec("ALTER TABLE inventory_issue_detail MODIFY qty DECIMAL(10,3)");
    echo "Updated inventory qty columns\n";

    $stmt = $db->query("SHOW TABLES LIKE 'reservation'");
    if (!$stmt->fetch()) {
        $db->exec("
            CREATE TABLE reservation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_id INT,
                customer_name VARCHAR(100) NOT NULL,
                party_size INT DEFAULT 1,
                start_time DATETIME NOT NULL,
                end_time DATETIME NOT NULL,
                status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (table_id) REFERENCES restaurant_table(id),
                FOREIGN KEY (created_by) REFERENCES users(id)
            )
        ");
        echo "Created reservation table\n";
    } else {
        echo "reservation table exists\n";
    }
} catch (Exception $e) {
    echo 'Migration error: ' . $e->getMessage() . "\n";
    exit(1);
}
