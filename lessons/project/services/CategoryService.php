<?php

declare(strict_types=1);

require_once __DIR__ . '/../dto/Category.php';
require_once __DIR__ . '/CsvManagement.php';

class CategoryService
{
    private CsvManagement $csvManagement;

    public function __construct(string $file = __DIR__ . '/../csv/categories.csv')
    {
        $this->csvManagement = new CsvManagement($file);
    }

    public function getCategoriesFromCsv(): array
    {
        $data = $this->csvManagement->readCsv();
        $categories = [];
        foreach ($data as [$id, $name]) {
            $categories[] = new Category($id, $name);
        }
        return $categories;
    }

    public function createCategory(string $name): Category
    {
        $all = $this->getCategoriesFromCsv();
        $ids = array_map(fn($c) => (int)$c->id, $all);
        $id = $ids ? (string)(max($ids) + 1) : '1';
        $cat = new Category($id, $name);
        $all[] = $cat;
        $this->saveAll($all);
        return $cat;
    }

    public function updateCategory(string $id, string $name)
    {
        $all = $this->getCategoriesFromCsv();
        foreach ($all as $c) {
            if ($c->id === $id) {
                $c->name = $name;
            }
        }
        $this->saveAll($all);
    }

    public function deleteCategory(string $id)
    {
        $all = $this->getCategoriesFromCsv();
        $new = array_filter($all, fn($c) => $c->id !== $id);
        $this->saveAll(array_values($new));
    }

    private function saveAll(array $categories)
    {
        $data = array_map(fn($c) => [$c->id, $c->name], $categories);
        $this->csvManagement->writeCsv($data);
    }

    public function findById(string $id): ?Category
    {
        foreach ($this->getCategoriesFromCsv() as $c) {
            if ($c->id === $id) return $c;
        }
        return null;
    }
}
