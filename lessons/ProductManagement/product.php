<?php 

declare(strict_types=1);

include_once 'classes/CategoryServices.php';
include_once 'classes/ProductServices.php';

function helperFunction($value): string
{
    return htmlspecialchars(trim((string)$value));
}

$categoryService = new CategoryServices();
$productService = new ProductServices();

$action = $_POST['action'] ?? null;
$message = '';

/* -------------------- CREATE -------------------- */
if ($action === 'create') {
    $name = helperFunction($_POST['name'] ?? '');
    $categoryId = helperFunction($_POST['category_id'] ?? '');
    $price = (int)helperFunction($_POST['price'] ?? '0');
    $quantity = (int)helperFunction($_POST['quantity'] ?? '0');

    if ($name === '' || $categoryId === '') {
        $message = 'Name and Category cannot be empty.';
    } else {
        $category = $categoryService->findById($categoryId);
        if ($category === null) {
            $message = 'Invalid category selected.';
        } else {
            $productService->create($name, $category, $price, $quantity);
            $message = 'Product created successfully.';
        }
    }
}

/* -------------------- UPDATE -------------------- */
if ($action === 'update') {
    $id = helperFunction($_POST['id'] ?? '');
    $name = helperFunction($_POST['name'] ?? '');
    $categoryId = helperFunction($_POST['category_id'] ?? '');
    $price = (int)helperFunction($_POST['price'] ?? '0');
    $quantity = (int)helperFunction($_POST['quantity'] ?? '0');

    if ($id === '' || $name === '' || $categoryId === '') {
        $message = 'ID, Name and Category cannot be empty.';
    } else {
        $category = $categoryService->findById($categoryId);
        if ($category === null) {
            $message = 'Invalid category selected.';
        } else {
            $productService->update($id, $name, $category, $price, $quantity);
            $message = 'Product updated successfully.';
        }
    }
}

/* -------------------- DELETE -------------------- */
if ($action === 'delete') {
    $id = helperFunction($_POST['id'] ?? '');
    if ($id) {
        $productService->delete($id);
        $message = 'Product deleted successfully.';
    }
}

/* -------------------- FILTER (POST ONLY) -------------------- */
if ($action === 'filter') {
    $filters = [
        'name'         => helperFunction($_POST['name'] ?? ''),
        'category'     => helperFunction($_POST['category'] ?? ''),
        'min_price'    => helperFunction($_POST['price_min'] ?? ''),
        'max_price'    => helperFunction($_POST['price_max'] ?? ''),
        'min_quantity' => helperFunction($_POST['qty_min'] ?? ''),
        'max_quantity' => helperFunction($_POST['qty_max'] ?? ''),
        'sort_field'   => helperFunction($_POST['sort_field'] ?? ''),
        'sort_dir'     => helperFunction($_POST['sort_dir'] ?? 'ASC'),
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

$products = $productService->filter($filters);
$categories = $categoryService->getCategoriesFromCsv();

/* -------------------------------------------------------------
   HTML OUTPUT
--------------------------------------------------------------*/

echo '<h1>Products</h1>';
echo '<p><a href="index.php">Back</a> | <a href="category.php">Categories</a></p>';

/* --------------- CREATE PRODUCT FORM --------------- */
echo '<h2>Create Product</h2>';
echo '<form method="post">
        <input type="hidden" name="action" value="create">
        <input name="name" placeholder="Product name" required>
        <select name="category_id" required>
            <option value="">Select Category</option>';

foreach ($categories as $category) {
    echo '<option value="' . htmlspecialchars($category->id) . '">' . htmlspecialchars($category->name) . '</option>';
}

echo '</select>
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <button type="submit">Create</button>
    </form>';

/* -------------------- FILTER FORM (POST) -------------------- */

echo '<h2>Filters</h2>
<form method="post" style="margin-bottom:14px">
  <input type="hidden" name="action" value="filter">

  <label>Name contains:
    <input type="text" name="name" value="' . htmlspecialchars($filters['name']) . '">
  </label>

  <label>Category:
    <select name="category">
      <option value="">-- all --</option>';

foreach ($categories as $c) {
    $selected = ($c->id == $filters['category']) ? 'selected' : '';
    echo '<option value="' . $c->id . '" ' . $selected . '>' . htmlspecialchars($c->name) . '</option>';
}

echo '</select></label>

  <label>Price >=
    <input type="number" step="0.01" name="price_min" value="' . htmlspecialchars($filters['min_price']) . '">
  </label>

  <label>Price <=
    <input type="number" step="0.01" name="price_max" value="' . htmlspecialchars($filters['max_price']) . '">
  </label>

  <label>Qty >=
    <input type="number" name="qty_min" value="' . htmlspecialchars($filters['min_quantity']) . '">
  </label>

  <label>Qty <=
    <input type="number" name="qty_max" value="' . htmlspecialchars($filters['max_quantity']) . '">
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
    echo '<p>' . htmlspecialchars($message) . '</p>';
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
                <td>' . htmlspecialchars($product->id) . '</td>
                <td>' . htmlspecialchars($product->name) . '</td>
                <td>' . htmlspecialchars($product->category->name) . '</td>
                <td>' . htmlspecialchars((string)$product->price) . '</td>
                <td>' . htmlspecialchars((string)$product->quantity) . '</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="' . htmlspecialchars($product->id) . '">
                        <input name="name" value="' . htmlspecialchars($product->name) . '" required>

                        <select name="category_id" required>';
        foreach ($categories as $cat) {
            $sel = ($cat->id === $product->category->id) ? 'selected' : '';
            echo '<option value="' . $cat->id . '" ' . $sel . '>' . $cat->name . '</option>';
        }

        echo '</select>

                        <input type="number" name="price" value="' . htmlspecialchars((string)$product->price) . '" required>
                        <input type="number" name="quantity" value="' . htmlspecialchars((string)$product->quantity) . '" required>
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="' . htmlspecialchars($product->id) . '">
                        <button type="submit">Delete</button>
                    </form>

                </td>
            </tr>';
    }

    echo '</table>';
}
