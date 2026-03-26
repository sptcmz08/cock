<?php
/**
 * CHRONOS Admin — จัดการประเภทสินค้า
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$db = getDB();
$categories = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.sort_order ASC, c.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — จัดการประเภทสินค้า</title>
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
            <a href="products.php">
                <span class="nav-icon">⌚</span> จัดการสินค้า
            </a>
            <a href="categories.php" class="active">
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
            <h1 class="page-title">📂 จัดการ<span>ประเภทสินค้า</span></h1>
            <button class="btn btn-primary" onclick="showModal()">➕ เพิ่มประเภท</button>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ไอคอน</th>
                        <th>ชื่อประเภท</th>
                        <th>Slug</th>
                        <th>สินค้า</th>
                        <th>ลำดับ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr data-id="<?= $cat['id'] ?>">
                        <td style="font-size:1.4rem;"><?= htmlspecialchars($cat['icon']) ?></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td style="color:var(--gray);font-size:0.82rem;"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td><span class="type-badge analog"><?= $cat['product_count'] ?> สินค้า</span></td>
                        <td><?= $cat['sort_order'] ?></td>
                        <td class="actions">
                            <button class="btn btn-secondary btn-sm" onclick="editCat(<?= htmlspecialchars(json_encode($cat)) ?>)">✏️</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCat(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>')">🗑</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--gray);">ยังไม่มีประเภทสินค้า</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal -->
<div class="admin-modal-overlay" id="modal">
    <div class="admin-modal">
        <div class="modal-header">
            <h3 id="modalTitle">เพิ่มประเภทสินค้า</h3>
            <button class="close-btn" onclick="closeModal()">✕</button>
        </div>
        <form id="catForm">
            <div class="modal-body">
                <input type="hidden" name="id" id="catId">
                <div class="form-row">
                    <div class="form-group">
                        <label>ชื่อประเภท <span class="required">*</span></label>
                        <input type="text" name="name" id="catName" class="form-control" required placeholder="เช่น Analog, Digital">
                    </div>
                    <div class="form-group">
                        <label>Slug <small style="color:var(--gray);">(อัตโนมัติถ้าเว้นว่าง)</small></label>
                        <input type="text" name="slug" id="catSlug" class="form-control" placeholder="analog">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>ไอคอน (emoji)</label>
                        <input type="text" name="icon" id="catIcon" class="form-control" placeholder="⌚" value="⌚">
                    </div>
                    <div class="form-group">
                        <label>ลำดับการแสดง</label>
                        <input type="number" name="sort_order" id="catSort" class="form-control" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">💾 บันทึก</button>
            </div>
        </form>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
const modal = document.getElementById('modal');
let editMode = false;

function showModal() {
    editMode = false;
    document.getElementById('modalTitle').textContent = 'เพิ่มประเภทสินค้า';
    document.getElementById('catForm').reset();
    document.getElementById('catId').value = '';
    document.getElementById('catIcon').value = '⌚';
    modal.classList.add('active');
}

function editCat(cat) {
    editMode = true;
    document.getElementById('modalTitle').textContent = 'แก้ไขประเภทสินค้า';
    document.getElementById('catId').value = cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catSlug').value = cat.slug;
    document.getElementById('catIcon').value = cat.icon;
    document.getElementById('catSort').value = cat.sort_order;
    modal.classList.add('active');
}

function closeModal() { modal.classList.remove('active'); }

document.getElementById('catForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = new FormData(e.target);
    const id = form.get('id');

    try {
        let res;
        if (editMode && id) {
            const body = new URLSearchParams(form).toString();
            res = await fetch('api/categories.php', { method: 'PUT', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body });
        } else {
            res = await fetch('api/categories.php', { method: 'POST', body: form });
        }
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message, 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
});

async function deleteCat(id, name) {
    if (!confirm(`ลบประเภท "${name}" ?\n\nสินค้าที่อยู่ในประเภทนี้จะถูกตั้งเป็น "ไม่ระบุประเภท"`)) return;
    try {
        const res = await fetch('api/categories.php', {
            method: 'DELETE',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `id=${id}`
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message, 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.add('hiding'); setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
</body>
</html>
