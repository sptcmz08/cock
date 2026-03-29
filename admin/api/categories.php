<?php
/**
 * CHRONOS Admin — Categories API
 */
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../db.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    switch ($method) {
        case 'GET':
            $cats = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
            echo json_encode(['success' => true, 'data' => $cats]);
            break;

        case 'POST':
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '⌚');
            $slug = trim($_POST['slug'] ?? '');
            $sort = intval($_POST['sort_order'] ?? 0);

            if (!$name) {
                echo json_encode(['success' => false, 'message' => 'Please enter category name']);
                exit;
            }

            if (!$slug) {
                $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
                $slug = trim($slug, '-');
            }

            $stmt = $db->prepare("INSERT INTO categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $icon, $sort]);
            echo json_encode(['success' => true, 'message' => 'Category added successfully', 'id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            parse_str(file_get_contents('php://input'), $data);
            $id = intval($data['id'] ?? 0);
            $name = trim($data['name'] ?? '');
            $icon = trim($data['icon'] ?? '⌚');
            $slug = trim($data['slug'] ?? '');
            $sort = intval($data['sort_order'] ?? 0);

            if (!$id || !$name) {
                echo json_encode(['success' => false, 'message' => 'Incomplete data']);
                exit;
            }

            $stmt = $db->prepare("UPDATE categories SET name=?, slug=?, icon=?, sort_order=? WHERE id=?");
            $stmt->execute([$name, $slug, $icon, $sort, $id]);
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            break;

        case 'DELETE':
            parse_str(file_get_contents('php://input'), $data);
            $id = intval($data['id'] ?? 0);

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID not found']);
                exit;
            }

            // Set products with this category to NULL
            $stmt = $db->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
            $stmt->execute([$id]);

            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
