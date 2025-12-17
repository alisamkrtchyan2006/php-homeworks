const API_BASE_URL = '../api';

async function loadAllCategories() {
    const response = await fetch(`${API_BASE_URL}/categories.php`);
    const categories = await response.json();

    const productCategorySelect = document.getElementById('pCat');
    const filterCategorySelect = document.getElementById('fCategory');

    productCategorySelect.innerHTML = '<option value="">-- Select category --</option>';
    filterCategorySelect.innerHTML = '<option value="">All categories</option>';

    categories.forEach(category => {
        productCategorySelect.insertAdjacentHTML(
            'beforeend',
            `<option value="${category.id}">${category.name}</option>`
        );
        filterCategorySelect.insertAdjacentHTML(
            'beforeend',
            `<option value="${category.id}">${category.name}</option>`
        );
    });
}

function isValidString(value) {
    return typeof value === 'string' && value.trim() !== '';
}

function isValidNumber(value) {
    return !isNaN(value) && value !== '';
}

async function loadAllProducts(filters = {}) {
    const queryString = new URLSearchParams(filters).toString();
    const response = await fetch(`${API_BASE_URL}/products.php?${queryString}`);
    const products = await response.json();

    const productTableBody = document.getElementById('productList');
    productTableBody.innerHTML = '';

    products.forEach(product => {
        productTableBody.insertAdjacentHTML('beforeend', `<tr>
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${product.category.name}</td>
            <td>${product.price}</td>
            <td>${product.quantity}</td>
            <td>
                <button onclick='editExistingProduct(${JSON.stringify(product)})'>Edit</button>
                <button onclick="deleteExistingProduct('${product.id}')">Delete</button>
            </td>
        </tr>`);
    });
}

async function createProduct() {
    const productName = document.getElementById('pName').value.trim();
    const productCategoryId = document.getElementById('pCat').value;
    const productPrice = parseFloat(document.getElementById('pPrice').value);
    const productQuantity = parseInt(document.getElementById('pQty').value);

    if (!isValidString(productName)) {
        alert('Product name is required.');
        return;
    }
    if (!isValidString(productCategoryId)) {
        alert('Select a valid category.');
        return;
    }
    if (!isValidNumber(productPrice) || productPrice < 0) {
        alert('Enter a valid price (>= 0).');
        return;
    }
    if (!isValidNumber(productQuantity) || productQuantity < 0) {
        alert('Enter a valid quantity (>= 0).');
        return;
    }

    await fetch(`${API_BASE_URL}/products.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: productName,
            category_id: productCategoryId,
            price: productPrice,
            quantity: productQuantity
        })
    });

    loadAllProducts();
}

async function editExistingProduct(product) {
    const newName = prompt('Name', product.name);
    if (!isValidString(newName)) {
        alert('Invalid name.');
        return;
    }

    const newPrice = parseFloat(prompt('Price', product.price));
    if (!isValidNumber(newPrice) || newPrice < 0) {
        alert('Invalid price.');
        return;
    }

    const newQuantity = parseInt(prompt('Quantity', product.quantity));
    if (!isValidNumber(newQuantity) || newQuantity < 0) {
        alert('Invalid quantity.');
        return;
    }

    await fetch(`${API_BASE_URL}/products.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: product.id,
            name: newName,
            category_id: product.category.id,
            price: newPrice,
            quantity: newQuantity
        })
    });

    loadAllProducts();
}

async function deleteExistingProduct(productId) {
    const confirmDelete = confirm('Are you sure you want to delete this product?');
    if (!confirmDelete) {
        return;
    }

    await fetch(`${API_BASE_URL}/products.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: productId })
    });

    loadAllProducts();
}

document.getElementById('applyFilters').addEventListener('click', () => {
    const filterValues = {
        name: document.getElementById('fName').value.trim(),
        category: document.getElementById('fCategory').value,
        min_price: document.getElementById('fMinPrice').value,
        max_price: document.getElementById('fMaxPrice').value,
        min_quantity: document.getElementById('fMinQty').value,
        max_quantity: document.getElementById('fMaxQty').value,
        sort_field: document.getElementById('sortField').value,
        sort_dir: document.getElementById('sortDir').value,
    };

    if ((filterValues.min_price && isNaN(filterValues.min_price)) ||
        (filterValues.max_price && isNaN(filterValues.max_price))) {
        alert('Price filters must be numeric.');
        return;
    }
    if ((filterValues.min_quantity && isNaN(filterValues.min_quantity)) ||
        (filterValues.max_quantity && isNaN(filterValues.max_quantity))) {
        alert('Quantity filters must be numeric.');
        return;
    }

    loadAllProducts(filterValues);
});

document.getElementById('clearFilters').addEventListener('click', () => {
    document.getElementById('fName').value = '';
    document.getElementById('fCategory').value = '';
    document.getElementById('fMinPrice').value = '';
    document.getElementById('fMaxPrice').value = '';
    document.getElementById('fMinQty').value = '';
    document.getElementById('fMaxQty').value = '';
    document.getElementById('sortField').value = '';
    document.getElementById('sortDir').value = 'ASC';

    loadAllProducts();
});

loadAllCategories();
loadAllProducts();
