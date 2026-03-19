-- Watch Showcase Database
-- สร้าง database ก่อน: CREATE DATABASE watch_showcase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS `watch_showcase` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `watch_showcase`;

-- ตาราง admins
CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง products
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(100) NOT NULL DEFAULT '',
    `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `description` TEXT,
    `type` ENUM('analog','digital','both') NOT NULL DEFAULT 'analog',
    `features` TEXT,
    `image` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample products
INSERT INTO `products` (`name`, `brand`, `price`, `description`, `type`, `features`, `is_featured`, `sort_order`) VALUES
('Grand Seiko Heritage SBGA211', 'Grand Seiko', 185000.00, 'นาฬิกาเครื่องใหญ่ Grand Seiko รุ่น Heritage Collection ตัวเรือนสแตนเลสสตีล หน้าปัด Snowflake อันเป็นเอกลักษณ์ ขับเคลื่อนด้วยระบบ Spring Drive ที่รวมความแม่นยำของควอตซ์และความสวยงามของเครื่องกล', 'analog', 'Spring Drive Caliber 9R65|กันน้ำ 100 เมตร|สำรองพลังงาน 72 ชั่วโมง|เส้นผ่านศูนย์กลาง 41mm|กระจก Sapphire Crystal|ตัวเรือน Stainless Steel', 1, 1),

('Casio G-Shock GWG-2000', 'Casio', 18500.00, 'นาฬิกาเครื่องใหญ่ G-Shock รุ่น Mudmaster ทนทานสุดขีด โครงสร้าง Carbon Core Guard กันกระแทก กันโคลน กันฝุ่น พร้อมเซ็นเซอร์วัดทิศ อุณหภูมิ และความกดอากาศ', 'both', 'Tough Solar + Multi-Band 6|Triple Sensor V3|Carbon Core Guard|กันน้ำ 200 เมตร|เส้นผ่านศูนย์กลาง 54.4mm|Mud Resistant|Bluetooth Connected', 1, 2),

('Omega Seamaster Planet Ocean 600M', 'Omega', 245000.00, 'นาฬิกาเครื่องใหญ่ Omega Seamaster รุ่น Planet Ocean กันน้ำ 600 เมตร ตัวเรือนสแตนเลสสตีล ขอบ Ceramic พร้อม Master Chronometer Calibre 8900 ที่ผ่านการรับรองจาก METAS', 'analog', 'Master Chronometer Calibre 8900|กันน้ำ 600 เมตร|เส้นผ่านศูนย์กลาง 43.5mm|Co-Axial Escapement|Silicon Balance Spring|กระจก Sapphire Crystal', 1, 3),

('Casio G-Shock GA-2100-1A1', 'Casio', 4590.00, 'นาฬิกาเครื่องใหญ่ G-Shock รุ่น CasiOak ดีไซน์บาง เบา ทันสมัย หน้าปัดทรงแปดเหลี่ยม ผสมผสานจอ Analog และ Digital กันกระแทกตามมาตรฐาน G-Shock', 'both', 'กันกระแทก 200G|กันน้ำ 200 เมตร|เส้นผ่านศูนย์กลาง 45.4mm|หนา 11.8mm|น้ำหนัก 51g|World Time 31 โซนเวลา|LED Light', 0, 4),

('Seiko Presage SPB167J1', 'Seiko', 28900.00, 'นาฬิกาเครื่องใหญ่ Seiko Presage Sharp Edged Series หน้าปัดลาย Asanoha อันประณีต แรงบันดาลใจจากลวดลายญี่ปุ่นโบราณ ตัวเรือนสแตนเลสสตีลเจียระไนคม ขับเคลื่อนด้วย Caliber 6R35', 'analog', 'Caliber 6R35 Automatic|สำรองพลังงาน 70 ชั่วโมง|กันน้ำ 100 เมตร|เส้นผ่านศูนย์กลาง 39.3mm|กระจก Sapphire Crystal|หน้าปัดลาย Asanoha', 0, 5),

('Citizen Promaster Eco-Drive BN0150-28E', 'Citizen', 12500.00, 'นาฬิกาเครื่องใหญ่ Citizen Promaster Eco-Drive ดำน้ำ ISO 6425 พลังงานแสงอาทิตย์ ไม่ต้องเปลี่ยนแบตเตอรี่ ตัวเรือนสแตนเลสสตีลเคลือบ PVD สีดำ ขอบหมุนได้', 'analog', 'Eco-Drive (พลังงานแสง)|สำรองพลังงาน 180 วัน|กันน้ำ 200 เมตร|เส้นผ่านศูนย์กลาง 44mm|ISO 6425 Dive Certified|ขอบหมุนทิศทางเดียว', 0, 6),

('Casio Pro Trek PRW-6600Y', 'Casio', 14900.00, 'นาฬิกาเครื่องใหญ่ Casio Pro Trek สำหรับนักผจญภัย พร้อม Triple Sensor วัดทิศ ความสูง ความกดอากาศ และอุณหภูมิ ใช้พลังงานแสงอาทิตย์ + คลื่นวิทยุปรับเวลาอัตโนมัติ', 'digital', 'Tough Solar + Multi-Band 6|Triple Sensor V3|กันน้ำ 100 เมตร|เส้นผ่านศูนย์กลาง 51.5mm|Neon Illuminator|Full Auto LED Light', 0, 7),

('Orient Star RE-AV0005L00B', 'Orient', 22500.00, 'นาฬิกาเครื่องใหญ่ Orient Star Contemporary Collection สีน้ำเงิน Open Heart หน้าปัดเจาะเปิดให้เห็นการทำงานของเครื่อง เครื่อง Caliber F6N43 Automatic Made in Japan', 'analog', 'Caliber F6N43 Automatic|สำรองพลังงาน 50 ชั่วโมง|กันน้ำ 100 เมตร|เส้นผ่านศูนย์กลาง 41mm|Open Heart Design|Power Reserve Indicator|กระจก Sapphire Crystal', 0, 8);
