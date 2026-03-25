<?php
/**
 * CHRONOS — Premium Watch Gallery
 * Main Public Page (Single Page)
 */
require_once __DIR__ . '/helpers.php';

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

// Load all settings
$S = getAllSettings();
$logoText = $S['logo_text'] ?? 'CHRONOS';
$logoImage = $S['logo_image'] ?? '';
$siteTitle = $S['site_title'] ?? 'CHRONOS — Premium Watch Gallery';
$metaDesc = $S['meta_description'] ?? '';

function formatThaiPrice($price) {
    return number_format($price, 0, '.', ',');
}

function getTypeLabel($type) {
    $labels = ['analog' => '⏱ Analog', 'digital' => '🔢 Digital', 'both' => '⌚ Analog + Digital'];
    return $labels[$type] ?? $type;
}

// Check if any contact info exists
$hasContact = !empty($S['contact_phone']) || !empty($S['contact_email']) || !empty($S['contact_line']) || !empty($S['contact_facebook']) || !empty($S['contact_address']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⌚</text></svg>">
</head>
<body>

<!-- ====== Navbar ====== -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="#" class="navbar-brand">
            <?php if ($logoImage && file_exists(UPLOAD_DIR . $logoImage)): ?>
                <img src="<?= htmlspecialchars(UPLOAD_URL . $logoImage) ?>" alt="<?= htmlspecialchars($logoText) ?>" class="brand-logo-img">
            <?php else: ?>
                <div class="brand-icon">⌚</div>
            <?php endif; ?>
            <div class="brand-text"><?= htmlspecialchars($logoText) ?></div>
        </a>
        <ul class="navbar-nav" id="navMenu">
            <li><a href="#hero">หน้าแรก</a></li>
            <li><a href="#products">สินค้า</a></li>
            <?php if ($hasContact): ?>
                <li><a href="#contact">ติดต่อ</a></li>
            <?php endif; ?>
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
        <div class="hero-overline"><?= htmlspecialchars($S['hero_overline'] ?? '✦ Premium Watch Gallery ✦') ?></div>
        <h1 class="hero-title">
            <span class="line1"><?= htmlspecialchars($S['hero_title_1'] ?? 'นาฬิกาเครื่องใหญ่') ?></span>
            <span class="line2"><?= htmlspecialchars($S['hero_title_2'] ?? 'ระดับพรีเมียม') ?></span>
        </h1>
        <p class="hero-desc">
            <?= htmlspecialchars($S['hero_desc'] ?? 'คอลเลกชันนาฬิกาเครื่องใหญ่คัดสรรพิเศษ ทั้ง Analog และ Digital จากแบรนด์ชั้นนำระดับโลก ตอบโจทย์ทุกไลฟ์สไตล์') ?>
        </p>
        <a href="#products" class="hero-cta">
            <?= htmlspecialchars($S['hero_cta_text'] ?? 'ชมคอลเลกชัน →') ?>
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
                <div class="stat-number"><?= htmlspecialchars($S['stat_custom_value'] ?? '100%') ?></div>
                <div class="stat-label"><?= htmlspecialchars($S['stat_custom_label'] ?? 'ของแท้รับประกัน') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ====== Products ====== -->
<section class="products-section" id="products">
    <div class="container">
        <div class="products-header reveal">
            <div class="section-badge"><?= htmlspecialchars($S['section_badge'] ?? '✦ Our Collection') ?></div>
            <h2 class="section-title"><?= htmlspecialchars($S['section_title_1'] ?? 'คอลเลกชัน') ?><span><?= htmlspecialchars($S['section_title_2'] ?? 'นาฬิกา') ?></span></h2>
            <p class="section-subtitle"><?= htmlspecialchars($S['section_subtitle'] ?? 'รวมนาฬิกาเครื่องใหญ่คุณภาพสูงทั้ง Analog และ Digital จากแบรนด์ระดับโลก') ?></p>
            
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

<!-- ====== Contact Section ====== -->
<?php if ($hasContact): ?>
<section class="contact-section" id="contact">
    <div class="container">
        <div class="products-header reveal" style="margin-bottom: 48px;">
            <div class="section-badge">✦ Contact Us</div>
            <h2 class="section-title">ติดต่อ<span>เรา</span></h2>
        </div>
        <div class="contact-grid reveal">
            <?php if (!empty($S['contact_phone'])): ?>
            <a href="tel:<?= htmlspecialchars($S['contact_phone']) ?>" class="contact-card">
                <div class="contact-icon">📱</div>
                <div class="contact-label">โทรศัพท์</div>
                <div class="contact-value"><?= htmlspecialchars($S['contact_phone']) ?></div>
            </a>
            <?php endif; ?>

            <?php if (!empty($S['contact_email'])): ?>
            <a href="mailto:<?= htmlspecialchars($S['contact_email']) ?>" class="contact-card">
                <div class="contact-icon">📧</div>
                <div class="contact-label">อีเมล</div>
                <div class="contact-value"><?= htmlspecialchars($S['contact_email']) ?></div>
            </a>
            <?php endif; ?>

            <?php if (!empty($S['contact_line'])): ?>
            <a href="https://line.me/R/ti/p/<?= htmlspecialchars($S['contact_line']) ?>" target="_blank" class="contact-card">
                <div class="contact-icon">💚</div>
                <div class="contact-label">LINE</div>
                <div class="contact-value"><?= htmlspecialchars($S['contact_line']) ?></div>
            </a>
            <?php endif; ?>

            <?php if (!empty($S['contact_facebook'])): ?>
            <a href="<?= htmlspecialchars($S['contact_facebook']) ?>" target="_blank" class="contact-card">
                <div class="contact-icon">📘</div>
                <div class="contact-label">Facebook</div>
                <div class="contact-value">เยี่ยมชมเพจ →</div>
            </a>
            <?php endif; ?>

            <?php if (!empty($S['contact_address'])): ?>
            <div class="contact-card contact-card-wide">
                <div class="contact-icon">📍</div>
                <div class="contact-label">ที่อยู่</div>
                <div class="contact-value"><?= nl2br(htmlspecialchars($S['contact_address'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== Footer ====== -->
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer-top">
            <div>
                <div class="footer-brand">
                    <?php if ($logoImage && file_exists(UPLOAD_DIR . $logoImage)): ?>
                        <img src="<?= htmlspecialchars(UPLOAD_URL . $logoImage) ?>" alt="<?= htmlspecialchars($logoText) ?>" class="footer-logo-img">
                    <?php else: ?>
                        <?= htmlspecialchars($logoText) ?>
                    <?php endif; ?>
                </div>
                <div class="footer-tagline"><?= htmlspecialchars($S['footer_tagline'] ?? 'นาฬิกาเครื่องใหญ่ระดับพรีเมียม') ?></div>
            </div>
            <ul class="footer-links">
                <li><a href="#hero">หน้าแรก</a></li>
                <li><a href="#products">สินค้า</a></li>
                <?php if ($hasContact): ?>
                    <li><a href="#contact">ติดต่อ</a></li>
                <?php endif; ?>
                <li><a href="admin/">จัดการระบบ</a></li>
            </ul>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">
                © <?= date('Y') ?> <?= htmlspecialchars($S['footer_copyright'] ?? 'CHRONOS. All rights reserved.') ?>
            </div>
        </div>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
