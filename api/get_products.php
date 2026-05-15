<?php
/**
 * API: Get Products with Filters and Search
 * 
 * Returns active products with optional filtering by:
 * - Category
 * - Car Make
 * - Car Model
 * - Search Query (searches name, description, part number, category)
 * 
 * Products are ordered by view count (most viewed first)
 * 
 * @version 2.0
 * @date Wednesday, October 23, 2025
 */

// Prevent any HTML output before JSON
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Correct path to db.php (from api/ folder to root)
$db_path = dirname(__DIR__) . '/db.php';

if (!file_exists($db_path)) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection file not found',
        'debug_path' => $db_path
    ]);
    exit;
}

require_once $db_path;

// Clear any output buffer
ob_end_clean();

try {
    // Verify PDO connection exists
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    // Get filter parameters
    $category_id = isset($_GET['category']) && !empty($_GET['category']) ? intval($_GET['category']) : null;
    $make_id = isset($_GET['make']) && !empty($_GET['make']) ? intval($_GET['make']) : null;
    $model_id = isset($_GET['model']) && !empty($_GET['model']) ? intval($_GET['model']) : null;
    $search = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : null;

    // Build the query
    $query = "
        SELECT DISTINCT
            p.id,
            p.name,
            p.description,
            p.manufacturer_part_number,
            pc.name as category_name,
            (SELECT pi.image_path 
             FROM product_images pi 
             WHERE pi.product_id = p.id 
             ORDER BY pi.is_primary DESC, pi.id ASC 
             LIMIT 1) as image_path,
            (SELECT COUNT(*) 
             FROM page_views pv 
             WHERE pv.product_id = p.id) as view_count
        FROM products p
        INNER JOIN product_categories pc ON p.category_id = pc.id
    ";

    // Add model/make filtering if specified
    if ($model_id || $make_id) {
        $query .= " INNER JOIN product_vehicle_compatibility pvc ON p.id = pvc.product_id";
        $query .= " INNER JOIN car_models cm ON pvc.model_id = cm.id";
    }

    $query .= " WHERE p.is_active = 1";

    $params = [];

    // Add category filter
    if ($category_id) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    // Add make filter
    if ($make_id && !$model_id) {
        $query .= " AND cm.make_id = ?";
        $params[] = $make_id;
    }

    // Add model filter (overrides make if both specified)
    if ($model_id) {
        $query .= " AND pvc.model_id = ?";
        $params[] = $model_id;
    }

    // Add search filter
    if ($search) {
        $query .= " AND (
            p.name LIKE ? OR 
            p.description LIKE ? OR 
            p.manufacturer_part_number LIKE ? OR 
            pc.name LIKE ?
        )";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    // Order by view count (most viewed first), then by name
    $query .= " ORDER BY view_count DESC, p.name ASC LIMIT 100";

    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return success response
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'filters' => [
            'category' => $category_id,
            'make' => $make_id,
            'model' => $model_id,
            'search' => $search
        ]
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Get Products API Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch products',
        'message' => $e->getMessage()
    ]);
}
?>