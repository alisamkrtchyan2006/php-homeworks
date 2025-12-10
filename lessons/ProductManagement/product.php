<?php 

declare(strict_types=1);

require_once 'spl_autoload_register.php';
require_once __DIR__ . '/iface/exceptions.php'; 

$categoryService = new CategoryService();
$productService  = new ProductService();

$action  = $_POST['action'] ?? null;
$message = '';
$filters = [];

try {

    if ($action === 'create') {
        $productService->create(
            trim((string)$_POST['name'] ?? ''),
            trim((string)$_POST['category_id'] ?? ''),
            (float)($_POST['price'] ?? 0),
            (int)($_POST['quantity'] ?? 0)
        );

        $message = 'Product created successfully.';
    }

    if ($action === 'update') {
        $productService->update(
            trim((string)$_POST['id'] ?? ''),
            trim((string)$_POST['name'] ?? ''),
            trim((string)$_POST['category_id'] ?? ''),
            (float)($_POST['price'] ?? 0),
            (int)($_POST['quantity'] ?? 0)
        );

        $message = 'Product updated successfully.';
    }

    if ($action === 'delete') {
        $productService->delete(trim((string)$_POST['id'] ?? ''));
        $message = 'Product deleted successfully.';
    }

    if ($action === 'filter') {
        $filters = [
            'name'         => trim((string)($_POST['name'] ?? '')),
            'category'     => trim((string)($_POST['category'] ?? '')),
            'min_price'    => trim((string)($_POST['price_min'] ?? '')),
            'max_price'    => trim((string)($_POST['price_max'] ?? '')),
            'min_quantity' => trim((string)($_POST['qty_min'] ?? '')),
            'max_quantity' => trim((string)($_POST['qty_max'] ?? '')),
            'sort_field'   => trim((string)($_POST['sort_field'] ?? '')),
            'sort_dir'     => trim((string)($_POST['sort_dir'] ?? 'ASC')),
        ];
    } else {
        $filters = [
            'name' => '',
            'category' => '',
            'min_price' => '',
            'max_price' => '',
            'min_quantity' => '',
            'max_quantity' => '',
            'sort_field' => '',
            'sort_dir' => 'ASC',
        ];
    }

} catch (ValidationException $e) {
    $message = 'Validation error: ' . $e->getMessage();
} catch (CategoryNotFoundException $e) {
    $message = 'Category error: ' . $e->getMessage();
} catch (ProductNotFoundException $e) {
    $message = 'Product error: ' . $e->getMessage();
} catch (IFileException $e) {
    $message = 'Internal file error: ' . $e->getMessage();
} catch (Exception $e) {
    $message = 'Unknown error: ' . $e->getMessage();
}

try {
    $products   = $productService->filter($filters);
    $categories = $categoryService->getCategoriesFromCsv();
} catch (IFileException $e) {
    $products = [];
    $categories = [];
    $message = 'Failed to load data: ' . $e->getMessage();
}

echo '<h1>Products</h1>';
echo '<p><a href="index.php">Back</a> | <a href="category.php">Categories</a></p>';

echo '<h2>Create Product</h2>';
echo '<form method="post">
        <input type="hidden" name="action" value="create">
        <input name="name" placeholder="Product name">
        <select name="category_id">
            <option value="">Select Category</option>';

foreach ($categories as $category) {
    echo '<option value="' . $category->id . '">' . $category->name . '</option>';
}

echo '</select>
        <input type="number" name="price" placeholder="Price">
        <input type="number" name="quantity" placeholder="Quantity">
        <button type="submit">Create</button>
    </form>';

echo '<h2>Filters</h2>
<form method="post" style="margin-bottom:14px">
  <input type="hidden" name="action" value="filter">

  <label>Name contains:
    <input type="text" name="name" value="' . $filters['name'] . '">
  </label>

  <label>Category:
    <select name="category">
      <option value="">-- all --</option>';

foreach ($categories as $c) {
    $selected = ($c->id == $filters['category']) ? 'selected' : '';
    echo '<option value="' . $c->id . '" ' . $selected . '>' . $c->name . '</option>';
}

echo '</select></label>

  <label>Price >=
    <input type="number" step="0.01" name="price_min" value="' . $filters['min_price'] . '">
  </label>

  <label>Price <=
    <input type="number" step="0.01" name="price_max" value="' . $filters['max_price'] . '">
  </label>

  <label>Qty >=
    <input type="number" name="qty_min" value="' . $filters['min_quantity'] . '">
  </label>

  <label>Qty <=
    <input type="number" name="qty_max" value="' . $filters['max_quantity'] . '">
  </label>

  <label>Sort by:
    <select name="sort_field">
      <option value="">-- none --</option>
      <option value="id"' . ($filters['sort_field']==='id'?' selected':'') . '>id</option>
      <option value="name"' . ($filters['sort_field']==='name'?' selected':'') . '>name</option>
      <option value="category"' . ($filters['sort_field']==='category'?' selected':'') . '>category</option>
      <option value="price"' . ($filters['sort_field']==='price'?' selected':'') . '>price</option>
      <option value="quantity"' . ($filters['sort_field']==='quantity'?' selected':'') . '>quantity</option>
    </select>
  </label>

  <label>Direction:
    <select name="sort_dir">
      <option value="ASC"' . ($filters['sort_dir']==='ASC'?' selected':'') . '>ASC</option>
      <option value="DESC"' . ($filters['sort_dir']==='DESC'?' selected':'') . '>DESC</option>
    </select>
  </label>

  <button type="submit">Apply</button>
  <a href="product.php" style="margin-left:10px">Reset</a>
</form>';

echo '<h2>All Products</h2>';
if ($message !== '') {
    echo '<p>' . $message . '</p>';
}

if (count($products) === 0) {
    echo '<p>No products yet.</p>';
} else {
    echo '<table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>';

    foreach ($products as $product) {
        echo '<tr>
                <td>' . $product->id . '</td>
                <td>' . $product->name . '</td>
                <td>' . $product->category->name . '</td>
                <td>' . (string)$product->price . '</td>
                <td>' . (string)$product->quantity . '</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="' . $product->id . '">
                        <input name="name" value="' . $product->name . '">

                        <select name="category_id">';
        foreach ($categories as $cat) {
            $sel = ($cat->id === $product->category->id) ? 'selected' : '';
            echo '<option value="' . $cat->id . '" ' . $sel . '>' . $cat->name . '</option>';
        }

        echo '</select>

                        <input type="number" name="price" value="' . (string)$product->price . '">
                        <input type="number" name="quantity" value="' . (string)$product->quantity . '">
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="' . $product->id . '">
                        <button type="submit">Delete</button>
                    </form>

                </td>
            </tr>';
    }

    echo '</table>';
}
