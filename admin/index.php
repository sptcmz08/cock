<?php
/**
 * CHRONOS Admin — Dashboard
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$db = getDB();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalFeatured = $db->query("SELECT COUNT(*) FROM products WHERE is_featured=1")->fetchColumn();
$totalBrands = $db->query("SELECT COUNT(DISTINCT brand) FROM products")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — Admin Dashboard</title>
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
            <a href="index.php" class="active">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="products.php">
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
            <h1 class="page-title">📊 Dash<span>board</span></h1>
            <span style="color: var(--gray); font-size: 0.85rem;">
                Welcome, <strong style="color: var(--gold);"><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
            </span>
        </div>

        <!-- Stats -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="icon">⌚</div>
                <div class="info">
                    <h3><?= $totalProducts ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">📂</div>
                <div class="info">
                    <h3><?= $totalCategories ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">⭐</div>
                <div class="info">
                    <h3><?= $totalFeatured ?></h3>
                    <p>Featured</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon">🏷</div>
                <div class="info">
                    <h3><?= $totalBrands ?></h3>
                    <p>Brands</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3>⚡ Quick Actions</h3>
            </div>
            <div style="padding: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="products.php" class="btn btn-primary">⌚ Products</a>
                <a href="products.php?action=add" class="btn btn-secondary">➕ Add Product</a>
                <a href="categories.php" class="btn btn-secondary">📂 Categories</a>
                <a href="settings.php" class="btn btn-secondary">⚙️ Settings</a>
                <a href="../" target="_blank" class="btn btn-secondary">🌐 View Site</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
