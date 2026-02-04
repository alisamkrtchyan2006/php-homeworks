<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product;
use App\DTO\Category;
use App\Database\Connection;
use App\Iface\ValidationException;
use PDO;

class ProductService
{
    private PDO $pdo;
    private CategoryService $categoryService;

    public function __construct(?PDO $pdo = null, ?CategoryService $categoryService = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
        $this->categoryService = $categoryService ?? new CategoryService($this->pdo);
    }

    public function getProducts(): array
    {
        $stmt = $this->pdo->query("
            SELECT p.id, p.name, p.category_id, p.price, p.quantity
            FROM products p
            ORDER BY p.id
        ");
        $products = [];
        while ($row = $stmt->fetch()) {
            $cat = $this->categoryService->findById((string)$row['category_id']);
            if ($cat) {
                $products[] = new Product(
                    $row['id'],
                    $row['name'],
                    $cat,
                    (int)$row['price'],
                    (int)$row['quantity']
                );
            }
        }
        return $products;
    }

    public function create(string $name, string $categoryId, int $price, int $quantity): void
    {
        $cat = $this->categoryService->findById($categoryId);
        if (!$cat) {
            throw new ValidationException("Category not found.");
        }
        $id = $this->generateId();
        $stmt = $this->pdo->prepare("
            INSERT INTO products (id, name, category_id, price, quantity)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id, $name, $categoryId, $price, $quantity]);
    }

    public function update(string $id, string $name, string $categoryId, int $price, int $quantity): void
    {
        $cat = $this->categoryService->findById($categoryId);
        if (!$cat) {
            throw new ValidationException("Category not found.");
        }
        $stmt = $this->pdo->prepare("
            UPDATE products SET name = ?, category_id = ?, price = ?, quantity = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $categoryId, $price, $quantity, $id]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function filter(array $filters): array
    {
        $sql = "SELECT p.id, p.name, p.category_id, p.price, p.quantity, c.name AS category_name
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];

        if (($filters['name'] ?? '') !== '') {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . trim($filters['name']) . '%';
        }

        if (($filters['category'] ?? '') !== '') {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }

        if (($filters['min_price'] ?? '') !== '') {
            if (!is_numeric($filters['min_price'])) {
                throw new ValidationException("Min price must be numeric.");
            }
            $sql .= " AND p.price >= ?";
            $params[] = (int)$filters['min_price'];
        }

        if (($filters['max_price'] ?? '') !== '') {
            if (!is_numeric($filters['max_price'])) {
                throw new ValidationException("Max price must be numeric.");
            }
            $sql .= " AND p.price <= ?";
            $params[] = (int)$filters['max_price'];
        }

        if (($filters['min_quantity'] ?? '') !== '') {
            if (!is_numeric($filters['min_quantity'])) {
                throw new ValidationException("Min quantity must be numeric.");
            }
            $sql .= " AND p.quantity >= ?";
            $params[] = (int)$filters['min_quantity'];
        }

        if (($filters['max_quantity'] ?? '') !== '') {
            if (!is_numeric($filters['max_quantity'])) {
                throw new ValidationException("Max quantity must be numeric.");
            }
            $sql .= " AND p.quantity <= ?";
            $params[] = (int)$filters['max_quantity'];
        }

        $sortField = $filters['sort_field'] ?? '';
        $sortDir = strtoupper($filters['sort_dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $allowedSort = ['id' => 'p.id', 'name' => 'p.name', 'category' => 'c.name', 'price' => 'p.price', 'quantity' => 'p.quantity'];
        if ($sortField !== '' && isset($allowedSort[$sortField])) {
            $sql .= " ORDER BY " . $allowedSort[$sortField] . " " . $sortDir;
        } else {
            $sql .= " ORDER BY p.id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $products = [];
        while ($row = $stmt->fetch()) {
            $cat = new Category((string)$row['category_id'], $row['category_name']);
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $cat,
                (int)$row['price'],
                (int)$row['quantity']
            );
        }
        return $products;
    }
}
