const API_BASE_URL = '../api';

async function loadAllCategories() {
    const response = await fetch(`${API_BASE_URL}/categories.php`);
    const categories = await response.json();

    const categoryListElement = document.getElementById('catList');
    categoryListElement.innerHTML = '';

    categories.forEach(category => {
        categoryListElement.insertAdjacentHTML(
            'beforeend',
            `<li>
                ${category.name}
                <button onclick="editExistingCategory('${category.id}', '${category.name}')">Edit</button>
                <button onclick="deleteExistingCategory('${category.id}')">Delete</button>
            </li>`
        );
    });

    return categories;
}

function isValidCategoryName(name) {
    return typeof name === 'string' && name.trim() !== '';
}

async function createCategory() {
    const categoryName = document.getElementById('catName').value.trim();
    
    if (!isValidCategoryName(categoryName)) {
        alert('Please enter a valid category name.');
        return;
    }

    await fetch(`${API_BASE_URL}/categories.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: categoryName })
    });

    document.getElementById('catName').value = '';
    loadAllCategories();
}

async function editExistingCategory(categoryId, currentName) {
    const newName = prompt('New category name', currentName);
    if (!isValidCategoryName(newName)) {
        alert('Invalid category name.');
        return;
    }

    await fetch(`${API_BASE_URL}/categories.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: categoryId, name: newName.trim() })
    });

    loadAllCategories();
}

async function deleteExistingCategory(categoryId) {
    const confirmDelete = confirm('Are you sure you want to delete this category?');
    if (!confirmDelete) {
        return;
    }

    await fetch(`${API_BASE_URL}/categories.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: categoryId })
    });

    loadAllCategories();
}

loadAllCategories();
