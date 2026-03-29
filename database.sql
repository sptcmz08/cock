-- Watch Showcase Database
-- Create database first: CREATE DATABASE watch_showcase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;



-- Admins table
CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(10) DEFAULT '⌚',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site settings table
CREATE TABLE `site_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` ENUM('text','image','textarea') NOT NULL DEFAULT 'text',
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `setting_label` VARCHAR(255) NOT NULL DEFAULT '',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
-- Logo
('logo_text', 'CHRONOS', 'text', 'logo', 'Logo Text'),
('logo_image', '', 'image', 'logo', 'Logo Image (replaces text if provided)'),
-- SEO
('site_title', 'CHRONOS — Premium Watch Gallery', 'text', 'seo', 'Site Title (Title Tag)'),
('meta_description', 'CHRONOS — Premium large wall clock gallery featuring high-quality Analog and Digital clocks', 'textarea', 'seo', 'Meta Description'),
-- Hero
('hero_overline', '✦ Premium Watch Gallery ✦', 'text', 'hero', 'Overline Text'),
('hero_title_1', 'Premium Large', 'text', 'hero', 'Hero Title Line 1'),
('hero_title_2', 'Wall Clocks', 'text', 'hero', 'Hero Title Line 2'),
('hero_desc', 'Curated collection of premium large wall clocks, both Analog and Digital, from world-class brands for every lifestyle', 'textarea', 'hero', 'Hero Description'),
('hero_cta_text', 'View Collection →', 'text', 'hero', 'CTA Button Text'),
-- Products Section
('section_badge', '✦ Our Collection', 'text', 'section', 'Products Section Badge'),
('section_title_1', 'Watch', 'text', 'section', 'Products Section Title (Part 1)'),
('section_title_2', 'Collection', 'text', 'section', 'Products Section Title (Part 2 — highlight)'),
('section_subtitle', 'Premium large wall clocks in both Analog and Digital styles from world-renowned brands', 'textarea', 'section', 'Products Section Subtitle'),
-- Stats (all 4 customizable)
('stat_1_value', 'auto', 'text', 'stats', 'Stat 1 — Value (type auto = count products)'),
('stat_1_label', 'Products', 'text', 'stats', 'Stat 1 — Label'),
('stat_2_value', 'auto', 'text', 'stats', 'Stat 2 — Value (type auto = count brands)'),
('stat_2_label', 'Top Brands', 'text', 'stats', 'Stat 2 — Label'),
('stat_3_value', 'auto', 'text', 'stats', 'Stat 3 — Value (type auto = featured count)'),
('stat_3_label', 'Featured', 'text', 'stats', 'Stat 3 — Label'),
('stat_4_value', '100%', 'text', 'stats', 'Stat 4 — Value'),
('stat_4_label', 'Authentic Guaranteed', 'text', 'stats', 'Stat 4 — Label'),
-- Contact
('contact_phone', '08x-xxx-xxxx', 'text', 'contact', 'Phone'),
('contact_email', 'info@chronos.com', 'text', 'contact', 'Email'),
('contact_line', '@chronos', 'text', 'contact', 'LINE ID'),
('contact_facebook', 'https://facebook.com/chronos', 'text', 'contact', 'Facebook URL'),
('contact_address', 'Bangkok, Thailand', 'textarea', 'contact', 'Address'),
-- Footer
('footer_tagline', 'Premium Large Wall Clocks', 'text', 'footer', 'Footer Tagline'),
('footer_copyright', 'CHRONOS. All rights reserved.', 'text', 'footer', 'Footer Copyright');

-- Products table
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(100) NOT NULL DEFAULT '',
    `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `description` TEXT,
    `category_id` INT DEFAULT NULL,
    `features` TEXT,
    `image` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$2CEcqp84H6Bz7z10wERxoewBwJ9FX/ZUPDWeCtdUwr3eJ5jZIBV2C');

-- Default categories
INSERT INTO `categories` (`name`, `slug`, `icon`, `sort_order`) VALUES
('Analog', 'analog', 'A', 1),
('Digital', 'digital', 'D', 2),
('Analog + Digital', 'both', 'A+D', 3);

-- Sample products
INSERT INTO `products` (`name`, `brand`, `price`, `description`, `category_id`, `features`, `is_featured`, `sort_order`) VALUES
('Grand Seiko Heritage SBGA211', 'Grand Seiko', 185000.00, 'Grand Seiko Heritage Collection large wall clock with stainless steel case, iconic Snowflake dial, powered by the Spring Drive system combining quartz precision with mechanical beauty', 1, 'Spring Drive Caliber 9R65|Water Resistant 100m|72-hour Power Reserve|41mm Diameter|Sapphire Crystal|Stainless Steel Case', 1, 1),

('Casio G-Shock GWG-2000', 'Casio', 18500.00, 'G-Shock Mudmaster large wall clock, ultimate durability with Carbon Core Guard structure — shock, mud, and dust resistant. Features compass, thermometer, and barometer sensors', 3, 'Tough Solar + Multi-Band 6|Triple Sensor V3|Carbon Core Guard|Water Resistant 200m|54.4mm Diameter|Mud Resistant|Bluetooth Connected', 1, 2),

('Omega Seamaster Planet Ocean 600M', 'Omega', 245000.00, 'Omega Seamaster Planet Ocean large wall clock, 600m water resistance, stainless steel case with ceramic bezel, featuring Master Chronometer Calibre 8900 certified by METAS', 1, 'Master Chronometer Calibre 8900|Water Resistant 600m|43.5mm Diameter|Co-Axial Escapement|Silicon Balance Spring|Sapphire Crystal', 1, 3),

('Casio G-Shock GA-2100-1A1', 'Casio', 4590.00, 'G-Shock CasiOak large wall clock with slim, lightweight modern design. Octagonal dial combining Analog and Digital displays with G-Shock standard shock resistance', 3, 'Shock Resistant 200G|Water Resistant 200m|45.4mm Diameter|11.8mm Thickness|51g Weight|World Time 31 Zones|LED Light', 0, 4),

('Seiko Presage SPB167J1', 'Seiko', 28900.00, 'Seiko Presage Sharp Edged Series large wall clock with exquisite Asanoha pattern dial inspired by traditional Japanese geometric designs. Sharp-edged stainless steel case powered by Caliber 6R35', 1, 'Caliber 6R35 Automatic|70-hour Power Reserve|Water Resistant 100m|39.3mm Diameter|Sapphire Crystal|Asanoha Pattern Dial', 0, 5),

('Citizen Promaster Eco-Drive BN0150-28E', 'Citizen', 12500.00, 'Citizen Promaster Eco-Drive dive wall clock, ISO 6425 certified. Solar-powered — no battery changes needed. Black PVD-coated stainless steel case with unidirectional bezel', 1, 'Eco-Drive (Solar Powered)|180-day Power Reserve|Water Resistant 200m|44mm Diameter|ISO 6425 Dive Certified|Unidirectional Rotating Bezel', 0, 6),

('Casio Pro Trek PRW-6600Y', 'Casio', 14900.00, 'Casio Pro Trek adventure large wall clock with Triple Sensor for compass, altitude, barometric pressure, and temperature. Solar powered with automatic radio time calibration', 2, 'Tough Solar + Multi-Band 6|Triple Sensor V3|Water Resistant 100m|51.5mm Diameter|Neon Illuminator|Full Auto LED Light', 0, 7),

('Orient Star RE-AV0005L00B', 'Orient', 22500.00, 'Orient Star Contemporary Collection blue Open Heart large wall clock with cut-out dial revealing the movement. Caliber F6N43 Automatic, Made in Japan', 1, 'Caliber F6N43 Automatic|50-hour Power Reserve|Water Resistant 100m|41mm Diameter|Open Heart Design|Power Reserve Indicator|Sapphire Crystal', 0, 8);
