<?php

declare(strict_types=1);

require_once __DIR__ . '/../iface/exceptions.php'; 

class CategoryService
{
    private CsvManagement $csvManagement;

    public function __construct(string $dataFile = __DIR__ . '/../data/categories.csv')
    {
        $this->csvManagement = new CsvManagement($dataFile);
    }

    public function getCategoriesFromCsv(): array
    {
        try {
            $categoriesData = $this->csvManagement->readCsv();
        } catch (IFileException $e) {
            throw $e;
        }

        $categories = [];

        foreach ($categoriesData as $data) {
            if (count($data) < 2) {
                throw new ValidationException("Invalid category string format in CSV");
            }

            [$id, $name] = $data;
            $categories[] = new Category($id, $name);
        }

        return $categories;
    }

    public function findById(string $id): Category
    {
        foreach ($this->getCategoriesFromCsv() as $category) {
            if ($category->id === $id) {
                return $category;
            }
        }

        throw new CategoryNotFoundException("Category '$id' not found");
    }

    public function createCategory(string $name): Category
    {
        if (trim($name) === '') {
            throw new ValidationException("Category name cannot be empty");
        }

        $all = $this->getCategoriesFromCsv();

        $ids = array_map(fn($category) => (int)$category->id, $all);
        $newId = $ids ? (string)(max($ids) + 1) : '1';

        $newCategory = new Category($newId, $name);
        $all[] = $newCategory;

        try {
            $this->saveAll($all);
        } catch (IFileException $e) {
            throw $e;
        }

        return $newCategory;
    }

    public function updateCategory(string $id, string $name): bool
    {
        if (trim($name) === '') {
            throw new ValidationException("Category name cannot be empty");
        }

        $all = $this->getCategoriesFromCsv();
        $found = false;

        foreach ($all as $category) {
            if ($category->id === $id) {
                $category->name = $name;
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new CategoryNotFoundException("Category '$id' not found");
        }

        $this->saveAll($all);
        return true;
    }

    public function deleteCategory(string $id): bool
    {
        $this->findById($id);

        $all = $this->getCategoriesFromCsv();
        $new = array_filter($all, fn($c) => $c->id !== $id);

        $this->saveAll(array_values($new));
        return true;
    }

    private function saveAll(array $categories): void
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = [$category->id, $category->name];
        }

        try {
            $this->csvManagement->writeCsv($data);
        } catch (IFileException $e) {
            throw $e;
        }
    }
}
