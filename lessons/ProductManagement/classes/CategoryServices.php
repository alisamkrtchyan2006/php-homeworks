<?php

declare(strict_types=1);

include_once 'classes/CsvManagement.php';
include_once 'classes/Category.php';

class CategoryServices
{
    private CsvManagement $csvManagement;

    public function __construct(string $dataFile = __DIR__ . '/../data/categories.csv')
    {
        $this->csvManagement = new CsvManagement($dataFile);
    }

    public function getCategoriesFromCsv(): array
    {
        $categoriesData = $this->csvManagement->readCsv();
        $categories = [];

        foreach ($categoriesData as $data) {
            if (count($data) < 2) continue;
            [$id, $name] = $data;
            $categories[] = new Category($id, $name);
        }

        return $categories;
    }

    public function findById(string $id)
    {
        foreach ($this->getCategoriesFromCsv() as $category) {
            if ($category->id === $id) {
                return $category;
            }
        }
        return null;
    }

    public function createCategory(string $name): Category
    {
        $all = $this->getCategoriesFromCsv();
        $ids = array_map(fn($category) => (int)$category->id, $all);
        $newId = $ids ? (string)(max($ids) + 1) : '1';

        $newCategory = new Category($newId, $name);
        $all[] = $newCategory;
        $this->saveAll($all);
        return $newCategory;
    }

    public function updateCategory(string $id, string $name): bool
    {
        $all = $this->getCategoriesFromCsv();
        $found = false;
        foreach ($all as $category) {
            if ($category->id === $id) {
                $category->name = $name;
                $found = true;
                break;
            }
        }
        if ($found) {
            $this->saveAll($all);
        }
        return $found;
    }

    public function deleteCategory(string $id): bool
    {
        $all = $this->getCategoriesFromCsv();
        $new = array_filter($all, fn($c) => $c->id !== $id);
        $changed = count($new) !== count($all);
        if ($changed) {
            $this->saveAll(array_values($new));
        }
        return $changed;
    }

    private function saveAll(array $categories): void
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = [$category->id, $category->name];
        }
        $this->csvManagement->writeCsv($data);
    }
}
