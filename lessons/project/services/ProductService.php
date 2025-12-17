<?php

declare(strict_types=1);

require_once __DIR__ . '/../dto/Product.php';
require_once __DIR__ . '/CsvManagement.php';
require_once __DIR__ . '/CategoryService.php';

class ProductService
{
    private CsvManagement $csvManagement;
    private CategoryService $categoryService;

    public function __construct(string $file = __DIR__ . '/../csv/products.csv')
    {
        $this->csvManagement = new CsvManagement($file);
        $this->categoryService = new CategoryService();
    }

    public function getProductsFromCsv(): array
    {
        $data = $this->csvManagement->readCsv();
        $products = [];
        foreach ($data as [$id, $name, $catId, $price, $qty]) {
            $cat = $this->categoryService->findById($catId);
            if ($cat) {
                $products[] = new Product($id, $name, $cat, (int)$price, (int)$qty);
            }
        }
        return $products;
    }

    public function create(string $name, string $categoryId, int $price, int $quantity)
    {
        $cat = $this->categoryService->findById($categoryId);
        $products = $this->getProductsFromCsv();
        $id = uniqid();
        $products[] = new Product($id, $name, $cat, $price, $quantity);
        $this->saveAll($products);
    }

    public function update(string $id, string $name, string $categoryId, int $price, int $quantity)
    {
        $cat = $this->categoryService->findById($categoryId);
        $all = $this->getProductsFromCsv();
        foreach ($all as $p) {
            if ($p->id === $id) {
                $p->name = $name;
                $p->category = $cat;
                $p->price = $price;
                $p->quantity = $quantity;
            }
        }
        $this->saveAll($all);
    }

    public function delete(string $id)
    {
        $all = $this->getProductsFromCsv();
        $new = array_filter($all, fn($p) => $p->id !== $id);
        $this->saveAll(array_values($new));
    }

    private function saveAll(array $products)
    {
        $data = array_map(fn($p) => [$p->id, $p->name, $p->category->id, $p->price, $p->quantity], $products);
        $this->csvManagement->writeCsv($data);
    }

    public function filter(array $filters): array
    {
        $products = $this->getProductsFromCsv();

        if ($filters['name'] ?? '' !== '') {
            $search = trim($filters['name']);
            $products = array_filter(
                $products,
                fn($p) => contains($p->name, $search) !== false
            );
        }

        if (($filters['category'] ?? '') !== '') {
            $catId = $filters['category'];
            $products = array_filter(
                $products,
                fn($p) => $p->category->id === $catId
            );
        }

        if (($filters['min_price'] ?? '') !== '') {
            if (!is_numeric($filters['min_price'])) {
                throw new ValidationException("Min price must be numeric.");
            }
            $min = (int)$filters['min_price'];
            $products = array_filter($products, fn($p) => $p->price >= $min);
        }

        if (($filters['max_price'] ?? '') !== '') {
            if (!is_numeric($filters['max_price'])) {
                throw new ValidationException("Max price must be numeric.");
            }
            $max = (int)$filters['max_price'];
            $products = array_filter($products, fn($p) => $p->price <= $max);
        }

        if (($filters['min_quantity'] ?? '') !== '') {
            if (!is_numeric($filters['min_quantity'])) {
                throw new ValidationException("Min quantity must be numeric.");
            }
            $min = (int)$filters['min_quantity'];
            $products = array_filter($products, fn($p) => $p->quantity >= $min);
        }

        if (($filters['max_quantity'] ?? '') !== '') {
            if (!is_numeric($filters['max_quantity'])) {
                throw new ValidationException("Max quantity must be numeric.");
            }
            $max = (int)$filters['max_quantity'];
            $products = array_filter($products, fn($p) => $p->quantity <= $max);
        }

        if (($filters['sort_field'] ?? '') !== '') {
            $dir = $filters['sort_dir'] ?? 'ASC';
            $products = $this->sort($products, $filters['sort_field'], $dir);
        }

        return array_values($products);
    }

    public function sort(array $products, string $field, string $dir = 'ASC'): array
    {
        $allowed = ['id', 'name', 'category', 'price', 'quantity'];

        if (!in_array($field, $allowed)) {
            throw new ValidationException("Invalid sort field: '$field'.");
        }

        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $n = count($products);

        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                $va = ($field === 'category') ? $products[$j]->category->name : $products[$j]->$field;
                $vb = ($field === 'category') ? $products[$j + 1]->category->name : $products[$j + 1]->$field;

                $cmp = in_array($field, ['id', 'price', 'quantity'])
                    ? ($va <=> $vb)
                    : strcasecmp((string)$va, (string)$vb);

                if (($dir === 'ASC' && $cmp > 0) || ($dir === 'DESC' && $cmp < 0)) {
                    $temp = $products[$j];
                    $products[$j] = $products[$j + 1];
                    $products[$j + 1] = $temp;
                }
            }
        }

        return $products;
    }

}
