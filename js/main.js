/**
 * CHRONOS — Premium Watch Gallery
 * Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initClockTicks();
    initFilterTabs();
    initModal();
    initScrollReveal();
    initStatCounters();
});

/* ==================== Navbar ==================== */
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.navbar-nav');

    // Scroll effect
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
    });

    // Mobile toggle
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('open');
        });

        // Close on link click
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => navMenu.classList.remove('open'));
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('open');
            }
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

/* ==================== Clock Tick Marks ==================== */
function initClockTicks() {
    const container = document.querySelector('.clock-ticks');
    if (!container) return;

    for (let i = 0; i < 60; i++) {
        const tick = document.createElement('div');
        tick.className = 'clock-tick' + (i % 5 === 0 ? ' major' : '');
        tick.style.transform = `rotate(${i * 6}deg)`;
        container.appendChild(tick);
    }
}

/* ==================== Filter Tabs ==================== */
function initFilterTabs() {
    const tabs = document.querySelectorAll('.filter-tab');
    const cards = document.querySelectorAll('.product-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const filter = tab.dataset.filter;

            cards.forEach((card, index) => {
                const category = card.dataset.category;
                const show = filter === 'all' || category === filter;

                if (show) {
                    card.style.display = '';
                    card.style.animation = `fadeUp 0.5s ease ${index * 0.05}s forwards`;
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

/* ==================== Product Modal ==================== */
function initModal() {
    const overlay = document.getElementById('productModal');
    if (!overlay) return;

    const closeBtn = overlay.querySelector('.modal-close');

    // Close button
    closeBtn.addEventListener('click', closeModal);

    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}

function openProductModal(productId) {
    const overlay = document.getElementById('productModal');
    const card = document.querySelector(`.product-card[data-id="${productId}"]`);
    if (!overlay || !card) return;

    // Get data from card's data attributes
    const data = card.dataset;
    const imageSrc = card.querySelector('.card-image img, .placeholder-img');

    // Populate modal
    overlay.querySelector('.modal-brand').textContent = data.brand || '';
    overlay.querySelector('.modal-name').textContent = data.name || '';
    overlay.querySelector('.modal-price').textContent = formatPrice(data.price);
    overlay.querySelector('.modal-desc').textContent = data.description || '';

    // Badge
    const badge = overlay.querySelector('.modal-badge');
    const catName = data.categoryName || '';
    const catIcon = data.categoryIcon || '⌚';
    if (catName) {
        badge.className = 'modal-badge card-badge analog';
        badge.textContent = catIcon + ' ' + catName;
    } else {
        badge.className = 'modal-badge card-badge';
        badge.textContent = '';
    }

    // Image
    const modalImg = overlay.querySelector('.modal-image');
    if (data.image) {
        modalImg.innerHTML = `<img src="${data.image}" alt="${data.name}">`;
    } else {
        modalImg.innerHTML = '<div class="placeholder-img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>';
    }

    // Features
    const featuresContainer = overlay.querySelector('.modal-features');
    featuresContainer.innerHTML = '';
    if (data.features) {
        const features = data.features.split('|');
        features.forEach(f => {
            if (f.trim()) {
                featuresContainer.innerHTML += `
                    <div class="feature-item">
                        <span class="feature-icon">◆</span>
                        ${f.trim()}
                    </div>`;
            }
        });
    }

    // Show
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const overlay = document.getElementById('productModal');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/* ==================== Helpers ==================== */
function formatPrice(price) {
    const num = parseFloat(price);
    if (isNaN(num)) return '0';
    return num.toLocaleString('th-TH');
}



/* ==================== Scroll Reveal ==================== */
function initScrollReveal() {
    const reveals = document.querySelectorAll('.reveal');
    if (!reveals.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    reveals.forEach(el => observer.observe(el));
}

/* ==================== Stat Counter Animation ==================== */
function initStatCounters() {
    const statNumbers = document.querySelectorAll('.stat-number');
    if (!statNumbers.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statNumbers.forEach(el => observer.observe(el));
}

function animateCounter(el) {
    const text = el.textContent.trim();
    const match = text.match(/^([\d,]+)/);
    if (!match) return;

    const target = parseInt(match[1].replace(/,/g, ''), 10);
    if (isNaN(target) || target === 0) return;

    const suffix = text.replace(match[1], '');
    const duration = 1200;
    const start = performance.now();

    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(target * eased);
        el.textContent = current.toLocaleString() + suffix;
        if (progress < 1) requestAnimationFrame(update);
    }

    el.textContent = '0' + suffix;
    requestAnimationFrame(update);
}
