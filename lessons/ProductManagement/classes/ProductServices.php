<?php

declare(strict_types=1);

include_once 'classes/CsvManagement.php';
include_once 'classes/Product.php';
include_once 'classes/CategoryServices.php';

class ProductServices
{
    private CsvManagement $csvManagement;
    private CategoryServices $categoryService;

    public function __construct(string $dataFile = __DIR__ . '/../data/products.csv')
    {
        $this->csvManagement = new CsvManagement($dataFile);
        $this->categoryService = new CategoryServices();
    }

    public function getProductsFromCsv(): array
    {
        $productsData = $this->csvManagement->readCsv();
        $products = [];

        foreach ($productsData as $data) {
            if (count($data) < 5) continue;

            $category = $this->categoryService->findById($data[2]);
            if ($category === null) continue;

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

    public function findById(string $id)
    {
        foreach ($this->getProductsFromCsv() as $product) {
            if ($product->id === $id) return $product;
        }
        return null;
    }

    public function create(string $name, Category $category, int $price, int $quantity): Product
    {
        $all = $this->getProductsFromCsv();
        $ids = array_map(fn($p) => (int)$p->id, $all);
        $newId = $ids ? (string)(max($ids) + 1) : '1';

        $newProduct = new Product($newId, $name, $category, $price, $quantity);
        $all[] = $newProduct;

        $this->saveAll($all);
        return $newProduct;
    }

    public function update(string $id, string $name, Category $category, int $price, int $quantity): bool
    {
        $all = $this->getProductsFromCsv();
        $found = false;

        foreach ($all as $product) {
            if ($product->id === $id) {
                $product->name = $name;
                $product->category = $category;
                $product->price = $price;
                $product->quantity = $quantity;
                $found = true;
                break;
            }
        }

        if ($found) $this->saveAll($all);
        return $found;
    }

    public function delete(string $id): bool
    {
        $all = $this->getProductsFromCsv();
        $new = array_filter($all, fn($p) => $p->id !== $id);
        $changed = count($new) !== count($all);

        if ($changed) $this->saveAll(array_values($new));
        return $changed;
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
        $this->csvManagement->writeCsv($data);
    }

    public function filter(array $filters): array
    {
        $products = $this->getProductsFromCsv();

        if (isset($filters['name']) && trim($filters['name']) !== '') {
            $search = trim($filters['name']);
            $products = array_filter($products, fn($p) => stripos($p->name, $search) !== false);
        }

        if (isset($filters['category']) && $filters['category'] !== '') {
            $catId = $filters['category'];
            $products = array_filter($products, fn($p) => $p->category->id === $catId);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '' && is_numeric($filters['min_price'])) {
            $min = (int)$filters['min_price'];
            $products = array_filter($products, fn($p) => $p->price >= $min);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '' && is_numeric($filters['max_price'])) {
            $max = (int)$filters['max_price'];
            $products = array_filter($products, fn($p) => $p->price <= $max);
        }

        if (isset($filters['min_quantity']) && $filters['min_quantity'] !== '' && is_numeric($filters['min_quantity'])) {
            $min = (int)$filters['min_quantity'];
            $products = array_filter($products, fn($p) => $p->quantity >= $min);
        }

        if (isset($filters['max_quantity']) && $filters['max_quantity'] !== '' && is_numeric($filters['max_quantity'])) {
            $max = (int)$filters['max_quantity'];
            $products = array_filter($products, fn($p) => $p->quantity <= $max);
        }

        if (isset($filters['sort_field']) && $filters['sort_field'] !== '') {
            $dir = $filters['sort_dir'] ?? 'ASC';
            $products = $this->sort($products, $filters['sort_field'], $dir);
        }

        return array_values($products);
    }


    public function sort(array $products, string $field, string $dir = 'ASC'): array
    {
        $allowed = ['id', 'name', 'category', 'price', 'quantity'];
        if (!in_array($field, $allowed)) return $products;

        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        usort($products, function($a, $b) use ($field, $dir) {
            $va = ($field === 'category') ? $a->category->name : $a->$field;
            $vb = ($field === 'category') ? $b->category->name : $b->$field;

            if (in_array($field, ['id', 'price', 'quantity'])) {
                $cmp = ($va == $vb) ? 0 : (($va < $vb) ? -1 : 1);
            } else {
                $cmp = strcasecmp((string)$va, (string)$vb);
            }

            return $dir === 'ASC' ? $cmp : -$cmp;
        });

        return $products;
    }
}
