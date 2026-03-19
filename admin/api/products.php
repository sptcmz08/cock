<?php
/**
 * CHRONOS Admin — Products API
 * Handles: POST (create), POST+_method=PUT (update), DELETE
 */
session_start();

// Auth check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// Support _method override for PUT
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $method = 'PUT';
}

$db = getDB();

try {
    switch ($method) {
        case 'GET':
            handleGet($db);
            break;
        case 'POST':
            handleCreate($db);
            break;
        case 'PUT':
            handleUpdate($db);
            break;
        case 'DELETE':
            handleDelete($db);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/* ==================== GET ==================== */
function handleGet($db) {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        echo json_encode(['success' => true, 'data' => $product]);
    } else {
        $products = $db->query("SELECT * FROM products ORDER BY sort_order ASC, created_at DESC")->fetchAll();
        echo json_encode(['success' => true, 'data' => $products]);
    }
}

/* ==================== CREATE ==================== */
function handleCreate($db) {
    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $type = $_POST['type'] ?? 'analog';
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? intval($_POST['is_featured']) : 0;

    // Validation
    if (!$name || !$brand) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อสินค้าและแบรนด์']);
        return;
    }

    if (!in_array($type, ['analog', 'digital', 'both'])) {
        $type = 'analog';
    }

    // Handle image
    $imageName = handleImageUpload();

    $stmt = $db->prepare("INSERT INTO products (name, brand, price, description, type, features, image, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $brand, $price, $description, $type, $features, $imageName, $is_featured, $sort_order]);

    echo json_encode(['success' => true, 'message' => 'เพิ่มสินค้าสำเร็จ!', 'id' => $db->lastInsertId()]);
}

/* ==================== UPDATE ==================== */
function handleUpdate($db) {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing product ID']);
        return;
    }

    // Get existing product
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบสินค้า']);
        return;
    }

    $name = trim($_POST['name'] ?? $existing['name']);
    $brand = trim($_POST['brand'] ?? $existing['brand']);
    $price = floatval($_POST['price'] ?? $existing['price']);
    $type = $_POST['type'] ?? $existing['type'];
    $description = trim($_POST['description'] ?? $existing['description']);
    $features = trim($_POST['features'] ?? $existing['features']);
    $sort_order = intval($_POST['sort_order'] ?? $existing['sort_order']);
    $is_featured = isset($_POST['is_featured']) ? intval($_POST['is_featured']) : 0;

    if (!in_array($type, ['analog', 'digital', 'both'])) {
        $type = 'analog';
    }

    // Handle image
    $imageName = handleImageUpload();
    if ($imageName) {
        // Delete old image
        deleteImage($existing['image']);
    } else {
        $imageName = $existing['image'];
    }

    $stmt = $db->prepare("UPDATE products SET name=?, brand=?, price=?, description=?, type=?, features=?, image=?, is_featured=?, sort_order=? WHERE id=?");
    $stmt->execute([$name, $brand, $price, $description, $type, $features, $imageName, $is_featured, $sort_order, $id]);

    echo json_encode(['success' => true, 'message' => 'แก้ไขสินค้าสำเร็จ!']);
}

/* ==================== DELETE ==================== */
function handleDelete($db) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing product ID']);
        return;
    }

    // Get existing product
    $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบสินค้า']);
        return;
    }

    // Delete image file
    deleteImage($product['image']);

    // Delete record
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'ลบสินค้าสำเร็จ!']);
}

/* ==================== Image Helpers ==================== */
function handleImageUpload() {
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES['image'];

    // Validate size
    if ($file['size'] > MAX_FILE_SIZE) {
        return null;
    }

    // Validate type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_TYPES)) {
        return null;
    }

    // Generate unique name
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'watch_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);

    // Ensure directory
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // Move file
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        return $filename;
    }

    return null;
}

function deleteImage($filename) {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
    }
}
