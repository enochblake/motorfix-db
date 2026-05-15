<?php
/**
 * Motor Fix - Get Product Data (AJAX Endpoint)
 * 
 * Returns product details and images in JSON format
 * for the edit modal to display
 * 
 * @version 1.0
 * @date October 23, 2025
 */

session_start();
require_once '../db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$product_id = intval($_GET['id']);

try {
    // Fetch product details
    $stmt = $pdo->prepare("
        SELECT p.*, pc.name as category_name 
        FROM products p 
        JOIN product_categories pc ON p.category_id = pc.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    // Fetch product images
    $stmt_images = $pdo->prepare("
        SELECT id, image_path, is_primary 
        FROM product_images 
        WHERE product_id = ? 
        ORDER BY is_primary DESC, id ASC
    ");
    $stmt_images->execute([$product_id]);
    $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);
    
    // Return data
    echo json_encode([
        'success' => true,
        'product' => $product,
        'images' => $images
    ]);
    
} catch(PDOException $e) {
    error_log("Get Product Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>