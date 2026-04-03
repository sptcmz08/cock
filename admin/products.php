<?php
/**
 * CHRONOS Admin — Products Management (CRUD)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$db = getDB();
$products = $db->query("SELECT p.*, c.name as category_name, c.icon as category_icon, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.sort_order ASC, p.created_at DESC")->fetchAll();
$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — Products</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime(__DIR__ . '/css/admin.css') ?>">
</head>
<body>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h1>CHRO<span>NOS</span></h1>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="products.php" class="active">
                <span class="nav-icon">⌚</span> Products
            </a>
            <a href="categories.php">
                <span class="nav-icon">📂</span> Categories
            </a>
            <a href="settings.php">
                <span class="nav-icon">⚙️</span> Settings
            </a>
            <a href="../" target="_blank">
                <span class="nav-icon">🌐</span> View Site
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php">🚪 Logout</a>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">⌚ Manage <span>Products</span></h1>
            <button class="btn btn-primary" onclick="openAddModal()">➕ Add Product</button>
        </div>

        <!-- Products Table -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3>All Products (<?= count($products) ?> items)</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--gray);">
                                No products yet — click "Add Product" to get started
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="product-thumb">
                                        <?php if ($p['image'] && file_exists(__DIR__ . '/../uploads/products/' . $p['image'])): ?>
                                            <img src="../uploads/products/<?= htmlspecialchars($p['image']) ?>" alt="">
                                        <?php else: ?>
                                            ⌚
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                                <td><?= htmlspecialchars($p['brand']) ?></td>
                                <td>
                                    <?php if ($p['category_name']): ?>
                                        <span class="type-badge analog"><?= htmlspecialchars($p['category_icon'] . ' ' . $p['category_name']) ?></span>
                                    <?php else: ?>
                                        <span class="type-badge" style="color:var(--gray);">— None</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--gold); font-weight: 600;">$<?= number_format($p['price'], 2, '.', ',') ?></td>
                                <td><?= $p['is_featured'] ? '⭐' : '—' ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)">✏️ Edit</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')">🗑 Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- ====== Product Modal ====== -->
<div class="admin-modal-overlay" id="productModal">
    <div class="admin-modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Product</h3>
            <button class="close-btn" onclick="closeProductModal()">✕</button>
        </div>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="formId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name <span class="required">*</span></label>
                        <input type="text" name="name" id="formName" class="form-control" placeholder="e.g. Grand Seiko SBGA211" required>
                    </div>
                    <div class="form-group">
                        <label>Brand <span class="required">*</span></label>
                        <input type="text" name="brand" id="formBrand" class="form-control" placeholder="e.g. Grand Seiko" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (USD) <span class="required">*</span></label>
                        <input type="number" name="price" id="formPrice" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" id="formCategory" class="form-control">
                            <option value="">— No Category —</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['icon'] . ' ' . $cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="formDesc" class="form-control" placeholder="Product description..."></textarea>
                </div>
                <div class="form-group">
                    <label>Key Features <small style="color: var(--gray);">(separated by | e.g. Water Resistant 200m|Sapphire Crystal)</small></label>
                    <textarea name="features" id="formFeatures" class="form-control" placeholder="Feature 1|Feature 2|Feature 3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" id="formSort" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 4px;">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" id="formFeatured" value="1">
                            <label for="formFeatured" style="margin-bottom: 0;">⭐ Featured Product</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" id="formImage" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div class="image-preview-box" id="imagePreview">
                        <div class="preview-placeholder">
                            <span>📷</span>
                            <p>Select product image</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">💾 Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
const API_URL = 'api/products.php';

/* ==================== Modal ==================== */
function openAddModal() {
    document.getElementById('modalTitle').textContent = '➕ Add New Product';
    document.getElementById('productForm').reset();
    document.getElementById('formId').value = '';
    document.getElementById('imagePreview').innerHTML = '<div class="preview-placeholder"><span>📷</span><p>Select product image</p></div>';
    document.getElementById('productModal').classList.add('active');
}

function openEditModal(product) {
    document.getElementById('modalTitle').textContent = '✏️ Edit Product';
    document.getElementById('formId').value = product.id;
    document.getElementById('formName').value = product.name;
    document.getElementById('formBrand').value = product.brand;
    document.getElementById('formPrice').value = product.price;
    document.getElementById('formCategory').value = product.category_id || '';
    document.getElementById('formDesc').value = product.description || '';
    document.getElementById('formFeatures').value = product.features || '';
    document.getElementById('formSort').value = product.sort_order || 0;
    document.getElementById('formFeatured').checked = product.is_featured == 1;

    // Show current image
    const preview = document.getElementById('imagePreview');
    if (product.image) {
        preview.innerHTML = `<img src="../uploads/products/${product.image}" alt="Current">`;
    } else {
        preview.innerHTML = '<div class="preview-placeholder"><span>📷</span><p>No image</p></div>';
    }

    document.getElementById('productModal').classList.add('active');
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
}

/* ==================== Image Preview ==================== */
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ==================== Form Submit ==================== */
document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const id = formData.get('id');
    const isEdit = !!id;

    // Handle checkbox
    if (!document.getElementById('formFeatured').checked) {
        formData.set('is_featured', '0');
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Saving...';

    try {
        const url = isEdit ? `${API_URL}?id=${id}` : API_URL;
        
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const res = await fetch(url, { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || (isEdit ? 'Updated successfully!' : 'Product added!'), 'success');
            closeProductModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'An error occurred', 'error');
        }
    } catch (err) {
        showToast('Error: ' + err.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = '💾 Save';
    }
});

/* ==================== Delete ==================== */
async function deleteProduct(id, name) {
    if (!confirm(`Delete product "${name}"?`)) return;

    try {
        const res = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        const data = await res.json();

        if (data.success) {
            showToast('Product deleted!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'An error occurred', 'error');
        }
    } catch (err) {
        showToast('Error: ' + err.message, 'error');
    }
}

/* ==================== Toast ==================== */
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* ==================== Auto-open Add Modal ==================== */
if (new URLSearchParams(window.location.search).get('action') === 'add') {
    openAddModal();
}

/* ==================== Close modal on ESC / Overlay ==================== */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeProductModal();
});
document.getElementById('productModal').addEventListener('click', (e) => {
    if (e.target.id === 'productModal') closeProductModal();
});
</script>
</body>
</html>
