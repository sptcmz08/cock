<?php
/**
 * CHRONOS Admin — Settings API
 * POST: Save settings (upsert) + handle logo upload
 */
session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = getDB();
    $updated = 0;

    // Handle logo upload
    if (!empty($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo_image'];

        // Validate
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (in_array($mime, ALLOWED_TYPES) && $file['size'] <= MAX_FILE_SIZE) {
            // Delete old logo
            $oldLogo = $db->query("SELECT setting_value FROM site_settings WHERE setting_key='logo_image'")->fetchColumn();
            if ($oldLogo && file_exists(UPLOAD_DIR . $oldLogo)) {
                unlink(UPLOAD_DIR . $oldLogo);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'logo_' . time() . '.' . strtolower($ext);

            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

            if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
                upsertSetting($db, 'logo_image', $filename);
                $updated++;
            }
        }
    }

    // Handle remove logo flag
    if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
        $oldLogo = $db->query("SELECT setting_value FROM site_settings WHERE setting_key='logo_image'")->fetchColumn();
        if ($oldLogo && file_exists(UPLOAD_DIR . $oldLogo)) {
            unlink(UPLOAD_DIR . $oldLogo);
        }
        upsertSetting($db, 'logo_image', '');
        $updated++;
    }

    // Handle all text settings
    $allowedKeys = [
        'logo_text', 'site_title', 'meta_description',
        'hero_overline', 'hero_title_1', 'hero_title_2', 'hero_desc', 'hero_cta_text',
        'section_badge', 'section_title_1', 'section_title_2', 'section_subtitle',
        'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label',
        'stat_3_value', 'stat_3_label', 'stat_4_value', 'stat_4_label',
        'contact_phone', 'contact_email', 'contact_line', 'contact_facebook', 'contact_address',
        'footer_tagline', 'footer_copyright'
    ];

    foreach ($allowedKeys as $key) {
        if (isset($_POST[$key])) {
            upsertSetting($db, $key, $_POST[$key]);
            $updated++;
        }
    }

    echo json_encode(['success' => true, 'message' => "Settings saved! ({$updated} items updated)"]);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
}

function upsertSetting($db, $key, $value) {
    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([$key, $value]);
}
