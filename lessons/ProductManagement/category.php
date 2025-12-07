<?php

declare(strict_types=1);

include_once 'classes/CategoryServices.php';

function helperFunction($value): string
{
    return htmlspecialchars(trim((string)$value));
}

$categoryService = new CategoryServices();
$action = $_POST['action'] ?? null;
$message = '';

if ($action === 'create') {
    $name = helperFunction($_POST['name'] ?? '');
    if ($name === '') {
        $message = 'Category name cannot be empty.';
    } else {
        $categoryService->createCategory($name);
        $message = 'Category created successfully.';
    }
}

if ($action === 'update') {
    $id = helperFunction($_POST['id'] ?? '');
    $name = helperFunction($_POST['name'] ?? '');
    if ($id !== '' && $name !== '') {
        if ($categoryService->updateCategory($id, $name)) {
            $message = 'Category updated successfully.';
        } else {
            $message = 'Failed to update category.';
        }
    }
}

if ($action === 'delete') {
    $id = helperFunction($_POST['id'] ?? '');
    if ($id !== '') {
        if ($categoryService->deleteCategory($id)) {
            $message = 'Category deleted successfully.';
        } else {
            $message = 'Failed to delete category.';
        }
    }
}

$categories = $categoryService->getCategoriesFromCsv();

echo '<h1>Categories</h1>';
echo '<p><a href="index.php">Back</a> | <a href="product.php">Products</a></p>';

echo '<h2>Create Category</h2>';
echo '<form method="post">
        <input type="hidden" name="action" value="create">
        <input name="name" placeholder="Category name" required>
        <button type="submit">Create</button>
    </form>';

if ($message !== '') {
    echo '<p>' . htmlspecialchars($message) . '</p>';
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
                <td>' . htmlspecialchars($category->id) . '</td>
                <td>' . htmlspecialchars($category->name) . '</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="' . htmlspecialchars($category->id) . '">
                        <input name="name" value="' . htmlspecialchars($category->name) . '" required>
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="' . htmlspecialchars($category->id) . '">
                        <button type="submit" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                </td>
            </tr>';
    }
    echo '</table>';
}
