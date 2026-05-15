<?php
/**
 * Motor Fix - Inventory Actions Backend Handler
 *
 * This script handles all AJAX requests from manage_inventory.php.
 * It deals with creating categories, fetching product data for editing,
 * and deleting product images.
 *
 * @version 1.0
 * @date Tuesday, October 14, 2025
 */
require_once __DIR__ . '/../db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Basic security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
header('Content-Type: application/json');

try {
    switch ($action) {
        // CASE: CREATE A NEW CATEGORY
        case 'add_category':
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                throw new Exception('Category name cannot be empty.');
            }
            $stmt = $pdo->prepare("INSERT INTO product_categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $new_id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'id' => $new_id, 'name' => $name]);
            break;

        // CASE: GET DETAILS FOR A SINGLE PRODUCT TO POPULATE THE EDIT MODAL
        case 'get_product_details':
            $product_id = (int)($_GET['id'] ?? 0);
            if ($product_id <= 0) {
                throw new Exception('Invalid product ID.');
            }
            $stmt_prod = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt_prod->execute([$product_id]);
            $product = $stmt_prod->fetch();

            $stmt_img = $pdo->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY is_primary DESC");
            $stmt_img->execute([$product_id]);
            $images = $stmt_img->fetchAll();
            $product['images'] = $images;

            echo json_encode(['success' => true, 'data' => $product]);
            break;

        // CASE: DELETE A SINGLE PRODUCT IMAGE
        case 'delete_image':
             $image_id = (int)($_POST['image_id'] ?? 0);
             if ($image_id <= 0) {
                 throw new Exception('Invalid image ID.');
             }
             
             // First, get the image path from the DB
             $stmt_get = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
             $stmt_get->execute([$image_id]);
             $image_path = $stmt_get->fetchColumn();

             if ($image_path) {
                 // Delete the record from the database
                 $stmt_del = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
                 $stmt_del->execute([$image_id]);

                 // Now, delete the actual file from the server
                 $file_to_delete = __DIR__ . '/../media/products/' . $image_path;
                 if (file_exists($file_to_delete)) {
                     unlink($file_to_delete);
                 }
                 echo json_encode(['success' => true, 'message' => 'Image deleted successfully.']);
             } else {
                 throw new Exception('Image not found in database.');
             }
            break;
        
        default:
            throw new Exception('Invalid action specified.');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit();
