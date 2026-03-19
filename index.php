<?php
/**
 * CHRONOS — Premium Watch Gallery
 * Main Public Page (Single Page)
 */
require_once __DIR__ . '/db.php';

try {
    $db = getDB();
    $products = $db->query("SELECT * FROM products ORDER BY is_featured DESC, sort_order ASC, created_at DESC")->fetchAll();
    $totalProducts = count($products);
    $brands = $db->query("SELECT COUNT(DISTINCT brand) as cnt FROM products")->fetch()['cnt'];
    $featured = $db->query("SELECT COUNT(*) as cnt FROM products WHERE is_featured = 1")->fetch()['cnt'];
} catch (Exception $e) {
    $products = [];
    $totalProducts = 0;
    $brands = 0;
    $featured = 0;
}

function formatThaiPrice($price) {
    return number_format($price, 0, '.', ',');
}

function getTypeLabel($type) {
    $labels = ['analog' => '⏱ Analog', 'digital' => '🔢 Digital', 'both' => '⌚ Analog + Digital'];
    return $labels[$type] ?? $type;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CHRONOS — แกลเลอรี่นาฬิกาเครื่องใหญ่ระดับพรีเมียม Analog และ Digital คุณภาพสูง">
    <title>CHRONOS — Premium Watch Gallery</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⌚</text></svg>">
</head>
<body>

<!-- ====== Navbar ====== -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="#" class="navbar-brand">
            <div class="brand-icon">⌚</div>
            <div class="brand-text">CHRO<span>NOS</span></div>
        </a>
        <ul class="navbar-nav" id="navMenu">
            <li><a href="#hero">หน้าแรก</a></li>
            <li><a href="#products">สินค้า</a></li>
            <li><a href="#footer">ติดต่อ</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- ====== Hero ====== -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    
    <!-- Animated Clock -->
    <div class="hero-clock">
        <div class="clock-ticks"></div>
        <div class="clock-hand hour"></div>
        <div class="clock-hand minute"></div>
        <div class="clock-hand second"></div>
    </div>

    <div class="hero-content">
        <div class="hero-overline">✦ Premium Watch Gallery ✦</div>
        <h1 class="hero-title">
            <span class="line1">นาฬิกาเครื่องใหญ่</span>
            <span class="line2">ระดับพรีเมียม</span>
        </h1>
        <p class="hero-desc">
            คอลเลกชันนาฬิกาเครื่องใหญ่คัดสรรพิเศษ ทั้ง Analog และ Digital
            จากแบรนด์ชั้นนำระดับโลก ตอบโจทย์ทุกไลฟ์สไตล์
        </p>
        <a href="#products" class="hero-cta">
            ชมคอลเลกชัน →
        </a>
    </div>

    <div class="hero-scroll">
        <div class="scroll-line"></div>
        SCROLL
    </div>
</section>

<!-- ====== Stats Bar ====== -->
<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal">
                <div class="stat-number"><?= $totalProducts ?></div>
                <div class="stat-label">รุ่นสินค้า</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number"><?= $brands ?></div>
                <div class="stat-label">แบรนด์ชั้นนำ</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number"><?= $featured ?></div>
                <div class="stat-label">สินค้าแนะนำ</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number">100%</div>
                <div class="stat-label">ของแท้รับประกัน</div>
            </div>
        </div>
    </div>
</div>

<!-- ====== Products ====== -->
<section class="products-section" id="products">
    <div class="container">
        <div class="products-header reveal">
            <div class="section-badge">✦ Our Collection</div>
            <h2 class="section-title">คอลเลกชัน<span>นาฬิกา</span></h2>
            <p class="section-subtitle">รวมนาฬิกาเครื่องใหญ่คุณภาพสูงทั้ง Analog และ Digital จากแบรนด์ระดับโลก</p>
            
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">ทั้งหมด</button>
                <button class="filter-tab" data-filter="analog">Analog</button>
                <button class="filter-tab" data-filter="digital">Digital</button>
                <button class="filter-tab" data-filter="both">Analog + Digital</button>
            </div>
        </div>

        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <div class="no-products-icon">⌚</div>
                    <p>ยังไม่มีสินค้าในขณะนี้</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="product-card reveal" 
                         data-id="<?= $p['id'] ?>"
                         data-type="<?= htmlspecialchars($p['type']) ?>"
                         data-name="<?= htmlspecialchars($p['name']) ?>"
                         data-brand="<?= htmlspecialchars($p['brand']) ?>"
                         data-price="<?= $p['price'] ?>"
                         data-description="<?= htmlspecialchars($p['description']) ?>"
                         data-features="<?= htmlspecialchars($p['features']) ?>"
                         data-image="<?= $p['image'] ? htmlspecialchars(UPLOAD_URL . $p['image']) : '' ?>"
                         onclick="openProductModal(<?= $p['id'] ?>)">
                        
                        <div class="card-image">
                            <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
                                <img src="<?= htmlspecialchars(UPLOAD_URL . $p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="placeholder-img">⌚</div>
                            <?php endif; ?>
                            <div class="card-badge <?= $p['type'] ?>"><?= getTypeLabel($p['type']) ?></div>
                            <?php if ($p['is_featured']): ?>
                                <div class="card-featured" title="สินค้าแนะนำ">★</div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="card-brand"><?= htmlspecialchars($p['brand']) ?></div>
                            <h3 class="card-name"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($p['description']) ?></p>
                            <div class="card-footer">
                                <div class="card-price">
                                    ฿<?= formatThaiPrice($p['price']) ?>
                                    <small>THB</small>
                                </div>
                                <span class="card-view-btn">ดูรายละเอียด →</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ====== Product Modal ====== -->
<div class="modal-overlay" id="productModal">
    <div class="modal-content">
        <button class="modal-close">✕</button>
        <div class="modal-image">
            <div class="placeholder-img">⌚</div>
        </div>
        <div class="modal-body">
            <div class="modal-badge card-badge analog">⏱ Analog</div>
            <div class="modal-brand"></div>
            <h2 class="modal-name"></h2>
            <div class="modal-price-box">
                <span class="modal-price">฿0</span>
                <span class="modal-price-label">THB</span>
            </div>
            <p class="modal-desc"></p>
            <div class="modal-features-title">✦ คุณสมบัติเด่น</div>
            <div class="modal-features"></div>
        </div>
    </div>
</div>

<!-- ====== Footer ====== -->
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer-top">
            <div>
                <div class="footer-brand">CHRO<span>NOS</span></div>
                <div class="footer-tagline">นาฬิกาเครื่องใหญ่ระดับพรีเมียม</div>
            </div>
            <ul class="footer-links">
                <li><a href="#hero">หน้าแรก</a></li>
                <li><a href="#products">สินค้า</a></li>
                <li><a href="admin/">จัดการระบบ</a></li>
            </ul>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">
                © <?= date('Y') ?> <a href="#">CHRONOS</a>. All rights reserved.
            </div>
            <div class="footer-copy">
                Premium Watch Gallery — Analog & Digital
            </div>
        </div>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
