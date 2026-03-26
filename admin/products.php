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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — จัดการสินค้า</title>
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
                <span class="nav-icon">📊</span> แดชบอร์ด
            </a>
            <a href="products.php" class="active">
                <span class="nav-icon">⌚</span> จัดการสินค้า
            </a>
            <a href="categories.php">
                <span class="nav-icon">📂</span> จัดการประเภท
            </a>
            <a href="settings.php">
                <span class="nav-icon">⚙️</span> ตั้งค่าเว็บไซต์
            </a>
            <a href="../" target="_blank">
                <span class="nav-icon">🌐</span> ดูเว็บไซต์
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php">🚪 ออกจากระบบ</a>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">⌚ จัดการ<span>สินค้า</span></h1>
            <button class="btn btn-primary" onclick="openAddModal()">➕ เพิ่มสินค้า</button>
        </div>

        <!-- Products Table -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3>รายการสินค้าทั้งหมด (<?= count($products) ?> รายการ)</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>รูป</th>
                        <th>ชื่อสินค้า</th>
                        <th>แบรนด์</th>
                        <th>ประเภท</th>
                        <th>ราคา</th>
                        <th>แนะนำ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--gray);">
                                ยังไม่มีสินค้า — กดปุ่ม "เพิ่มสินค้า" เพื่อเริ่มต้น
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
                                        <span class="type-badge" style="color:var(--gray);">— ไม่ระบุ</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--gold); font-weight: 600;">฿<?= number_format($p['price'], 0, '.', ',') ?></td>
                                <td><?= $p['is_featured'] ? '⭐' : '—' ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)">✏️ แก้ไข</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')">🗑 ลบ</button>
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
            <h3 id="modalTitle">เพิ่มสินค้าใหม่</h3>
            <button class="close-btn" onclick="closeProductModal()">✕</button>
        </div>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="formId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>ชื่อสินค้า <span class="required">*</span></label>
                        <input type="text" name="name" id="formName" class="form-control" placeholder="เช่น Grand Seiko SBGA211" required>
                    </div>
                    <div class="form-group">
                        <label>แบรนด์ <span class="required">*</span></label>
                        <input type="text" name="brand" id="formBrand" class="form-control" placeholder="เช่น Grand Seiko" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>ราคา (บาท) <span class="required">*</span></label>
                        <input type="number" name="price" id="formPrice" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>ประเภท</label>
                        <select name="category_id" id="formCategory" class="form-control">
                            <option value="">— ไม่ระบุประเภท —</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['icon'] . ' ' . $cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>รายละเอียด</label>
                    <textarea name="description" id="formDesc" class="form-control" placeholder="คำอธิบายสินค้า..."></textarea>
                </div>
                <div class="form-group">
                    <label>คุณสมบัติเด่น <small style="color: var(--gray);">(คั่นด้วย | เช่น กันน้ำ 200m|Sapphire Crystal)</small></label>
                    <textarea name="features" id="formFeatures" class="form-control" placeholder="คุณสมบัติ 1|คุณสมบัติ 2|คุณสมบัติ 3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>ลำดับการแสดง</label>
                        <input type="number" name="sort_order" id="formSort" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 4px;">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" id="formFeatured" value="1">
                            <label for="formFeatured" style="margin-bottom: 0;">⭐ สินค้าแนะนำ</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>รูปสินค้า</label>
                    <input type="file" name="image" id="formImage" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div class="image-preview-box" id="imagePreview">
                        <div class="preview-placeholder">
                            <span>📷</span>
                            <p>เลือกรูปภาพสินค้า</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductModal()">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">💾 บันทึก</button>
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
    document.getElementById('modalTitle').textContent = '➕ เพิ่มสินค้าใหม่';
    document.getElementById('productForm').reset();
    document.getElementById('formId').value = '';
    document.getElementById('imagePreview').innerHTML = '<div class="preview-placeholder"><span>📷</span><p>เลือกรูปภาพสินค้า</p></div>';
    document.getElementById('productModal').classList.add('active');
}

function openEditModal(product) {
    document.getElementById('modalTitle').textContent = '✏️ แก้ไขสินค้า';
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
        preview.innerHTML = '<div class="preview-placeholder"><span>📷</span><p>ยังไม่มีรูปภาพ</p></div>';
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
    submitBtn.textContent = '⏳ กำลังบันทึก...';

    try {
        const url = isEdit ? `${API_URL}?id=${id}` : API_URL;
        
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const res = await fetch(url, { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || (isEdit ? 'แก้ไขสำเร็จ!' : 'เพิ่มสินค้าสำเร็จ!'), 'success');
            closeProductModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด: ' + err.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = '💾 บันทึก';
    }
});

/* ==================== Delete ==================== */
async function deleteProduct(id, name) {
    if (!confirm(`ต้องการลบสินค้า "${name}" ใช่หรือไม่?`)) return;

    try {
        const res = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        const data = await res.json();

        if (data.success) {
            showToast('ลบสินค้าสำเร็จ!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด: ' + err.message, 'error');
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
