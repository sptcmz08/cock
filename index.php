<?php
/**
 * CHRONOS — Premium Watch Gallery
 * Main Public Page (Single Page)
 */
require_once __DIR__ . '/helpers.php';

try {
    $db = getDB();
    $products = $db->query("SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.is_featured DESC, p.sort_order ASC, p.created_at DESC")->fetchAll();
    $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
    $totalProducts = count($products);
    $brands = $db->query("SELECT COUNT(DISTINCT brand) as cnt FROM products")->fetch()['cnt'];
    $featured = $db->query("SELECT COUNT(*) as cnt FROM products WHERE is_featured = 1")->fetch()['cnt'];
} catch (Exception $e) {
    $products = [];
    $categories = [];
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

// Check if any contact info exists
$hasContact = !empty($S['contact_email']);

// Stats values — 'auto' means count from DB
$autoStats = [$totalProducts, $brands, $featured];
$stats = [];
for ($i = 1; $i <= 4; $i++) {
    $val = $S["stat_{$i}_value"] ?? ($i <= 3 ? 'auto' : '100%');
    if (strtolower(trim($val)) === 'auto' && $i <= 3) {
        $val = $autoStats[$i - 1];
    }
    $stats[$i] = [
        'value' => $val,
        'label' => $S["stat_{$i}_label"] ?? ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
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
            <li><a href="#hero">Home</a></li>
            <li><a href="#products">Products</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- ====== Hero ====== -->
<section class="hero" id="hero">
    <div class="hero-bg-pattern">
        <div class="hero-clock">
            <div class="clock-center"></div>
            <div class="clock-hand hour"></div>
            <div class="clock-hand minute"></div>
            <div class="clock-hand second"></div>
            <div class="clock-ticks">
                <?php for ($i = 0; $i < 60; $i++): ?>
                    <div class="clock-tick<?= $i % 5 === 0 ? ' major' : '' ?>" style="transform: rotate(<?= $i * 6 ?>deg)"></div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="hero-content">
        <div class="hero-overline"><?= htmlspecialchars($S['hero_overline'] ?? '✦ Premium Watch Gallery ✦') ?></div>
        <h1 class="hero-title">
            <span class="line1"><?= htmlspecialchars($S['hero_title_1'] ?? 'Premium Large') ?></span>
            <span class="line2"><?= htmlspecialchars($S['hero_title_2'] ?? 'Wall Clocks') ?></span>
        </h1>
        <p class="hero-desc"><?= htmlspecialchars($S['hero_desc'] ?? '') ?></p>
        <a href="#products" class="hero-cta">
            <?= htmlspecialchars($S['hero_cta_text'] ?? 'View Collection →') ?>
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
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="stat-item reveal">
                <div class="stat-number"><?= htmlspecialchars($stats[$i]['value']) ?></div>
                <div class="stat-label"><?= htmlspecialchars($stats[$i]['label']) ?></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- ====== Products ====== -->
<section class="products-section" id="products">
    <div class="container">
        <div class="products-header reveal">
            <div class="section-badge"><?= htmlspecialchars($S['section_badge'] ?? '✦ Our Collection') ?></div>
            <h2 class="section-title"><?= htmlspecialchars($S['section_title_1'] ?? 'Watch') ?><span><?= htmlspecialchars($S['section_title_2'] ?? 'Collection') ?></span></h2>
            <p class="section-subtitle"><?= htmlspecialchars($S['section_subtitle'] ?? '') ?></p>
            
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-tab" data-filter="<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <div class="no-products-icon">⌚</div>
                    <p>No products available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="product-card reveal" 
                         data-id="<?= $p['id'] ?>"
                         data-category="<?= htmlspecialchars($p['category_slug'] ?? '') ?>"
                         data-name="<?= htmlspecialchars($p['name']) ?>"
                         data-brand="<?= htmlspecialchars($p['brand']) ?>"
                         data-price="<?= $p['price'] ?>"
                         data-description="<?= htmlspecialchars($p['description']) ?>"
                         data-features="<?= htmlspecialchars($p['features']) ?>"
                         data-image="<?= $p['image'] ? htmlspecialchars(UPLOAD_URL . $p['image']) : '' ?>"
                         data-category-name="<?= htmlspecialchars($p['category_name'] ?? '') ?>"
                         data-category-icon="<?= htmlspecialchars($p['category_icon'] ?? '⌚') ?>"
                         onclick="openProductModal(<?= $p['id'] ?>)">
                        
                        <div class="card-image">
                            <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
                                <img src="<?= htmlspecialchars(UPLOAD_URL . $p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="placeholder-img">⌚</div>
                            <?php endif; ?>
                            <?php if ($p['category_name']): ?>
                                <div class="card-badge analog"><?= htmlspecialchars(($p['category_icon'] ?? '') . ' ' . $p['category_name']) ?></div>
                            <?php endif; ?>
                            <?php if ($p['is_featured']): ?>
                                <div class="card-featured" title="Featured">★</div>
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
                                <span class="card-view-btn">View Details →</span>
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
            <div class="modal-features-title">✦ Key Features</div>
            <div class="modal-features"></div>
        </div>
    </div>
</div>

<!-- ====== Contact Section ====== -->
<?php if ($hasContact): ?>
<section class="contact-section" id="contact">
    <div class="contact-bg-dots"></div>
    <div class="container">
        <div class="contact-hero reveal">
            <div class="contact-glow"></div>
            <div class="contact-badge">✦ Contact Us</div>
            <h2 class="contact-heading">Get In <span>Touch</span></h2>
            <p class="contact-subtitle"><?= htmlspecialchars($S['contact_subtitle'] ?? "Have questions about our collection? We'd love to hear from you.") ?></p>
            
            <a href="mailto:<?= htmlspecialchars($S['contact_email']) ?>" class="contact-email-card">
                <div class="contact-email-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 4L12 13L2 4"/>
                    </svg>
                </div>
                <div class="contact-email-info">
                    <div class="contact-email-label">Email Us</div>
                    <div class="contact-email-address"><?= htmlspecialchars($S['contact_email']) ?></div>
                </div>
                <div class="contact-email-arrow">→</div>
            </a>
        </div>
    </div>
</section>
<?php else: ?>
<section id="contact"></section>
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
                <div class="footer-tagline"><?= htmlspecialchars($S['footer_tagline'] ?? 'Premium Large Wall Clocks') ?></div>
            </div>

            <?php if (!empty($S['contact_email'])): ?>
            <div class="footer-contact-info">
                <a href="mailto:<?= htmlspecialchars($S['contact_email']) ?>" class="footer-contact-item">📧 <?= htmlspecialchars($S['contact_email']) ?></a>
            </div>
            <?php endif; ?>

            <ul class="footer-links">
                <li><a href="#hero">Home</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">
                © <?= date('Y') ?> <?= htmlspecialchars($S['footer_copyright'] ?? 'CHRONOS. All rights reserved.') ?>
            </div>
        </div>
    </div>
</footer>

<script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
</body>
</html>
