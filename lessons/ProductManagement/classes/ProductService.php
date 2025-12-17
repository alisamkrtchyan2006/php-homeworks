<?php

declare(strict_types=1);

require_once __DIR__ . '/../iface/exceptions.php'; 

class ProductService
{
    private CsvManagement $csvManagement;
    private CategoryService $categoryService;

    public function __construct(string $dataFile = __DIR__ . '/../data/products.csv')
    {
        $this->csvManagement = new CsvManagement($dataFile);
        $this->categoryService = new CategoryService();
    }

    public function getProductsFromCsv(): array
    {
        try {
            $productsData = $this->csvManagement->readCsv(); 
        } catch (Exception $e) {
            throw new FileReadException("Cannot read products CSV: " . $e->getMessage());
        }

        $products = [];

        foreach ($productsData as $data) {
            if (count($data) < 5) {
                continue;
            } 

            try {
                $category = $this->categoryService->findById($data[2]);  
            } catch (CategoryNotFoundException) {
                continue;
            }

            $products[] = new Product(
                $data[0],
                $data[1],
                $category,
                (int)$data[3],
                (int)$data[4]
            );
        }

        return $products;
    }

    public function findById(string $id): Product
    {
        foreach ($this->getProductsFromCsv() as $product) {
            if ($product->id === $id) {
                return $product;
            }
        }

        throw new ProductNotFoundException("Product with ID '{$id}' not found.");
    }

    public function create(string $name, string $categoryId, float $price, int $quantity)
    {
        if ($name === '') {
            throw new ValidationException("Product name cannot be empty.");
        }

        if ($price < 0 || $quantity < 0) {
            throw new ValidationException("Price and Quantity must be non-negative.");
        }
        
        $category = $this->categoryService->findById($categoryId);
        if (!$category) {
            throw new CategoryNotFoundException("Category not found.");
        }

        $product = new Product(
            uniqid(),
            $name,
            $category,
            (int)$price,
            $quantity
        );

        $this->csvManagement->appendCsv([
            $product->id,
            $product->name,
            $product->category->id,
            (string)$product->price,
            (string)$product->quantity
        ]);
    }

    public function update(string $id, string $name, string $categoryId, float $price, int $quantity): bool
    {
        if ($name === '') {
            throw new ValidationException("Name cannot be empty.");
        }

        if ($price < 0 || $quantity < 0) {
            throw new ValidationException("Price and Quantity must be non-negative.");
        }

        $category = $this->categoryService->findById($categoryId);
        $all = $this->getProductsFromCsv();

        $found = false;

        foreach ($all as $product) {
            if ($product->id === $id) {
                $found = true;
                $product->name = $name;
                $product->category = $category;
                $product->price = (int)$price;
                $product->quantity = $quantity;
                break;
            }
        }

        if (!$found) {
            throw new ProductNotFoundException("Cannot update: product '$id' does not exist.");
        }

        $this->saveAll($all);
        return true;
    }

    public function delete(string $id): bool
    {
        $this->findById($id); 

        $all = $this->getProductsFromCsv();
        $new = array_filter($all, fn($p) => $p->id !== $id);

        $this->saveAll(array_values($new));

        return true;
    }

    private function saveAll(array $products): void
    {
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                $product->id,
                $product->name,
                $product->category->id,
                (string)$product->price,
                (string)$product->quantity
            ];
        }

        try {
            $this->csvManagement->writeCsv($data);
        } catch (Exception $e) {
            throw new FileWriteException("Failed to save products: " . $e->getMessage());
        }
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

        usort($products, function($a, $b) use ($field, $dir) {
            $va = ($field === 'category') ? $a->category->name : $a->$field;
            $vb = ($field === 'category') ? $b->category->name : $b->$field;

            $cmp =
                in_array($field, ['id', 'price', 'quantity'])
                    ? ($va <=> $vb)
                    : strcasecmp((string)$va, (string)$vb);

            return $dir === 'ASC' ? $cmp : -$cmp;
        });

        return $products;
    }
}
