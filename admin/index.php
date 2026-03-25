<?php
/**
 * CHRONOS Admin — Dashboard
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$db = getDB();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalAnalog = $db->query("SELECT COUNT(*) FROM products WHERE type='analog'")->fetchColumn();
$totalDigital = $db->query("SELECT COUNT(*) FROM products WHERE type='digital'")->fetchColumn();
$totalBoth = $db->query("SELECT COUNT(*) FROM products WHERE type='both'")->fetchColumn();
$totalFeatured = $db->query("SELECT COUNT(*) FROM products WHERE is_featured=1")->fetchColumn();
$totalBrands = $db->query("SELECT COUNT(DISTINCT brand) FROM products")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — Admin Dashboard</title>
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
            <a href="index.php" class="active">
                <span class="nav-icon">📊</span> แดชบอร์ด
            </a>
            <a href="products.php">
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
            <h1 class="page-title">📊 แดช<span>บอร์ด</span></h1>
            <span style="color: var(--gray); font-size: 0.85rem;">
                สวัสดี, <strong style="color: var(--gold);"><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
            </span>
        </div>

        <!-- Stats -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="icon">⌚</div>
                <div class="info">
                    <h3><?= $totalProducts ?></h3>
                    <p>สินค้าทั้งหมด</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">⏱</div>
                <div class="info">
                    <h3><?= $totalAnalog ?></h3>
                    <p>Analog</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">🔢</div>
                <div class="info">
                    <h3><?= $totalDigital ?></h3>
                    <p>Digital</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">🔀</div>
                <div class="info">
                    <h3><?= $totalBoth ?></h3>
                    <p>Analog + Digital</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">⭐</div>
                <div class="info">
                    <h3><?= $totalFeatured ?></h3>
                    <p>สินค้าแนะนำ</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">🏷</div>
                <div class="info">
                    <h3><?= $totalBrands ?></h3>
                    <p>แบรนด์</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3>⚡ จัดการด่วน</h3>
            </div>
            <div style="padding: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="products.php" class="btn btn-primary">⌚ จัดการสินค้า</a>
                <a href="products.php?action=add" class="btn btn-secondary">➕ เพิ่มสินค้าใหม่</a>
                <a href="categories.php" class="btn btn-secondary">📂 จัดการประเภท</a>
                <a href="settings.php" class="btn btn-secondary">⚙️ ตั้งค่าเว็บไซต์</a>
                <a href="../" target="_blank" class="btn btn-secondary">🌐 เปิดเว็บไซต์</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
