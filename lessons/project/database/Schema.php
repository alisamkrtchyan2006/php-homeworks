<?php

declare(strict_types=1);

namespace App\Database;

class Schema
{
    public static function createTables(\PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id VARCHAR(36) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                category_id INT NOT NULL,
                price INT NOT NULL,
                quantity INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )
        ");
    }

    public static function renderTablesAsHtml(\PDO $pdo): void
    {
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        $products = $pdo->query("SELECT * FROM products")->fetchAll(\PDO::FETCH_ASSOC);
        ?>
        <!DOCTYPE html>
        <html>
        <body>
        <h2>Categories</h2>
        <table border="1">
            <tr><?php foreach (array_keys($categories[0] ?? []) as $col): ?><th><?= htmlspecialchars($col) ?></th><?php endforeach; ?></tr>
            <?php foreach ($categories as $row): ?>
            <tr><?php foreach ($row as $v): ?><td><?= htmlspecialchars((string)$v) ?></td><?php endforeach; ?></tr>
            <?php endforeach; ?>
        </table>
        <h2>Products</h2>
        <table border="1">
            <tr><?php foreach (array_keys($products[0] ?? []) as $col): ?><th><?= htmlspecialchars($col) ?></th><?php endforeach; ?></tr>
            <?php foreach ($products as $row): ?>
            <tr><?php foreach ($row as $v): ?><td><?= htmlspecialchars((string)$v) ?></td><?php endforeach; ?></tr>
            <?php endforeach; ?>
        </table>
        </body>
        </html>
        <?php
    }
}
