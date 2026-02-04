<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category;
use App\Database\Connection;
use PDO;

class CategoryService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getCategories(): array
    {
        $stmt = $this->pdo->query("SELECT id, name FROM categories ORDER BY id");
        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = new Category((string)$row['id'], $row['name']);
        }
        return $categories;
    }

    public function createCategory(string $name): Category
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $id = (string)$this->pdo->lastInsertId();
        return new Category($id, $name);
    }

    public function updateCategory(string $id, string $name): void
    {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    }

    public function deleteCategory(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function findById(string $id): ?Category
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }
        return new Category((string)$row['id'], $row['name']);
    }
}
