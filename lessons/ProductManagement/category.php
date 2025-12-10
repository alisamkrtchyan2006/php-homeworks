<?php

declare(strict_types=1);

require_once 'spl_autoload_register.php';
require_once __DIR__ . '/iface/exceptions.php'; 

$categoryService = new CategoryService();
$action = $_POST['action'] ?? null;
$message = '';

try {
    if ($action === 'create') {
        $name = htmlspecialchars(trim((string)($_POST['name'] ?? '')));
        $categoryService->createCategory($name);
        $message = 'Category created successfully.';
    }

    if ($action === 'update') {
        $id = htmlspecialchars(trim((string)($_POST['id'] ?? '')));
        $name = htmlspecialchars(trim((string)($_POST['name'] ?? '')));
        $categoryService->updateCategory($id, $name);
        $message = 'Category updated successfully.';
    }

    if ($action === 'delete') {
        $id = htmlspecialchars(trim((string)($_POST['id'] ?? '')));
        $categoryService->deleteCategory($id);
        $message = 'Category deleted successfully.';
    }
} catch (ValidationException $e) {
    $message = 'Validation error: ' . $e->getMessage();
} catch (CategoryNotFoundException $e) {
    $message = 'Category error: ' . $e->getMessage();
} catch (IFileException $e) {
    $message = 'Internal file error: ' . $e->getMessage();
} catch (Exception $e) {
    $message = 'Unknown error: ' . $e->getMessage();
}

try {
    $categories = $categoryService->getCategoriesFromCsv();
} catch (IFileException $e) {
    $categories = [];
    $message = 'Failed to load categories: ' . $e->getMessage();
}

echo '<h1>Categories</h1>';
echo '<p><a href="index.php">Back</a> | <a href="product.php">Products</a></p>';

echo '<h2>Create Category</h2>';
echo '<form method="post">
        <input type="hidden" name="action" value="create">
        <input name="name" placeholder="Category name">
        <button type="submit">Create</button>
    </form>';

if ($message !== '') {
    echo '<p>' . $message . '</p>';
}

echo '<h2>All categories</h2>';
if (count($categories) === 0) {
    echo '<p>No categories yet.</p>';
} else {
    echo '<table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>';
    foreach ($categories as $category) {
        echo '<tr>
                <td>' . $category->id . '</td>
                <td>' . $category->name . '</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="' . $category->id . '">
                        <input name="name" value="' . $category->name . '">
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="' . $category->id . '">
                        <button type="submit" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                </td>
            </tr>';
    }
    echo '</table>';
}
