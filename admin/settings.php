<?php
/**
 * CHRONOS Admin — ตั้งค่าเว็บไซต์
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

$db = getDB();
$settings = [];
$rows = $db->query("SELECT * FROM site_settings ORDER BY id ASC")->fetchAll();
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row;
}

function val($settings, $key) {
    return htmlspecialchars($settings[$key]['setting_value'] ?? '');
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — ตั้งค่าเว็บไซต์</title>
    <link rel="stylesheet" href="css/admin.css">
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
            <a href="categories.php">
                <span class="nav-icon">📂</span> จัดการประเภท
            </a>
            <a href="settings.php" class="active">
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
            <h1 class="page-title">⚙️ ตั้งค่า<span>เว็บไซต์</span></h1>
            <button class="btn btn-primary" onclick="saveAllSettings()" id="saveBtn">💾 บันทึกทั้งหมด</button>
        </div>

        <form id="settingsForm" enctype="multipart/form-data">

            <!-- Logo -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>🏷 Logo & ชื่อเว็บ</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-group">
                        <label>ชื่อ Logo <small style="color:var(--gray);">(แสดงบน Navbar / Footer)</small></label>
                        <input type="text" name="logo_text" class="form-control" value="<?= val($settings, 'logo_text') ?>" placeholder="CHRONOS">
                    </div>
                    <div class="form-group">
                        <label>รูป Logo <small style="color:var(--gray);">(ถ้ามี จะแสดงแทนข้อความ)</small></label>
                        <input type="file" name="logo_image" class="form-control" accept="image/*" onchange="previewLogo(this)">
                        <div class="image-preview-box" id="logoPreview">
                            <?php
                            $logoImg = $settings['logo_image']['setting_value'] ?? '';
                            if ($logoImg && file_exists(UPLOAD_DIR . $logoImg)):
                            ?>
                                <img src="../uploads/products/<?= htmlspecialchars($logoImg) ?>" alt="Logo">
                                <button type="button" class="remove-img-btn" onclick="removeLogo()">✕ ลบรูป</button>
                            <?php else: ?>
                                <div class="preview-placeholder"><span>🏷</span><p>ยังไม่มีรูป Logo</p></div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="remove_logo" id="removeLogo" value="0">
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>🔍 SEO</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-group">
                        <label>ชื่อเว็บไซต์ (Title Tag)</label>
                        <input type="text" name="site_title" class="form-control" value="<?= val($settings, 'site_title') ?>">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control"><?= val($settings, 'meta_description') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>🎯 Hero Section</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-group">
                        <label>ข้อความเล็กเหนือชื่อ (Overline)</label>
                        <input type="text" name="hero_overline" class="form-control" value="<?= val($settings, 'hero_overline') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>หัวข้อ Hero บรรทัดที่ 1</label>
                            <input type="text" name="hero_title_1" class="form-control" value="<?= val($settings, 'hero_title_1') ?>">
                        </div>
                        <div class="form-group">
                            <label>หัวข้อ Hero บรรทัดที่ 2 <small style="color:var(--gold);">(สีทอง)</small></label>
                            <input type="text" name="hero_title_2" class="form-control" value="<?= val($settings, 'hero_title_2') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>คำอธิบาย Hero</label>
                        <textarea name="hero_desc" class="form-control"><?= val($settings, 'hero_desc') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>ข้อความปุ่ม CTA</label>
                        <input type="text" name="hero_cta_text" class="form-control" value="<?= val($settings, 'hero_cta_text') ?>">
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>📦 ส่วนแสดงสินค้า</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-group">
                        <label>Badge</label>
                        <input type="text" name="section_badge" class="form-control" value="<?= val($settings, 'section_badge') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>หัวข้อ (ส่วนแรก)</label>
                            <input type="text" name="section_title_1" class="form-control" value="<?= val($settings, 'section_title_1') ?>">
                        </div>
                        <div class="form-group">
                            <label>หัวข้อ (ส่วนสี — highlight) <small style="color:var(--gold);">(สีทอง)</small></label>
                            <input type="text" name="section_title_2" class="form-control" value="<?= val($settings, 'section_title_2') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>คำอธิบาย</label>
                        <textarea name="section_subtitle" class="form-control"><?= val($settings, 'section_subtitle') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Stats (all 4) -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>📊 สถิติ (4 ช่อง)</h3>
                    <small style="color:var(--gray);">พิมพ์ auto ในช่องตัวเลข = นับอัตโนมัติ</small>
                </div>
                <div class="settings-section-body">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="form-row" style="margin-bottom:8px;">
                        <div class="form-group">
                            <label>สถิติ <?= $i ?> — ตัวเลข<?= $i <= 3 ? ' <small style="color:var(--gray);">(auto = นับอัตโนมัติ)</small>' : '' ?></label>
                            <input type="text" name="stat_<?= $i ?>_value" class="form-control" value="<?= val($settings, 'stat_'.$i.'_value') ?>">
                        </div>
                        <div class="form-group">
                            <label>สถิติ <?= $i ?> — ป้าย</label>
                            <input type="text" name="stat_<?= $i ?>_label" class="form-control" value="<?= val($settings, 'stat_'.$i.'_label') ?>">
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Contact -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>📞 ข้อมูลติดต่อ</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>📱 เบอร์โทรศัพท์</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= val($settings, 'contact_phone') ?>" placeholder="08x-xxx-xxxx">
                        </div>
                        <div class="form-group">
                            <label>📧 อีเมล</label>
                            <input type="text" name="contact_email" class="form-control" value="<?= val($settings, 'contact_email') ?>" placeholder="info@example.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>💚 LINE ID</label>
                            <input type="text" name="contact_line" class="form-control" value="<?= val($settings, 'contact_line') ?>" placeholder="@lineid">
                        </div>
                        <div class="form-group">
                            <label>📘 Facebook URL</label>
                            <input type="text" name="contact_facebook" class="form-control" value="<?= val($settings, 'contact_facebook') ?>" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>📍 ที่อยู่</label>
                        <textarea name="contact_address" class="form-control"><?= val($settings, 'contact_address') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3>📄 Footer</h3>
                </div>
                <div class="settings-section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tagline</label>
                            <input type="text" name="footer_tagline" class="form-control" value="<?= val($settings, 'footer_tagline') ?>">
                        </div>
                        <div class="form-group">
                            <label>Copyright</label>
                            <input type="text" name="footer_copyright" class="form-control" value="<?= val($settings, 'footer_copyright') ?>">
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Floating Save Button -->
        <div class="floating-save">
            <button class="btn btn-primary" onclick="saveAllSettings()" id="saveBtn2">💾 บันทึกทั้งหมด</button>
        </div>
    </main>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
async function saveAllSettings() {
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    
    const btns = [document.getElementById('saveBtn'), document.getElementById('saveBtn2')];
    btns.forEach(b => { if(b) { b.disabled = true; b.textContent = '⏳ กำลังบันทึก...'; }});

    try {
        const res = await fetch('api/settings.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด: ' + err.message, 'error');
    } finally {
        btns.forEach(b => { if(b) { b.disabled = false; b.textContent = '💾 บันทึกทั้งหมด'; }});
    }
}

function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('removeLogo').value = '0';
    }
}

function removeLogo() {
    document.getElementById('removeLogo').value = '1';
    document.getElementById('logoPreview').innerHTML = '<div class="preview-placeholder"><span>🏷</span><p>จะลบรูปเมื่อบันทึก</p></div>';
    // Clear file input
    const fileInput = document.querySelector('input[name="logo_image"]');
    if (fileInput) fileInput.value = '';
}

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
</script>
</body>
</html>
