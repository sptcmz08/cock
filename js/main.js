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
                const type = card.dataset.type;
                const show = filter === 'all' || type === filter;

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
    badge.className = 'modal-badge card-badge ' + data.type;
    badge.textContent = getTypeLabel(data.type);

    // Image
    const modalImg = overlay.querySelector('.modal-image');
    if (data.image) {
        modalImg.innerHTML = `<img src="${data.image}" alt="${data.name}">`;
    } else {
        modalImg.innerHTML = '<div class="placeholder-img">⌚</div>';
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

function getTypeLabel(type) {
    const labels = {
        'analog': '⏱ Analog',
        'digital': '🔢 Digital',
        'both': '⌚ Analog + Digital'
    };
    return labels[type] || type;
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
