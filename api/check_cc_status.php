<?php
/**
 * API: Check Customer Care Online Status
 * 
 * Returns whether any customer care agent is currently online
 * 
 * @version 1.1
 * @date Tuesday, October 21, 2025
 */

// Prevent any HTML output before JSON
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Correct path to db.php (from api/ folder to root)
$db_path = dirname(__DIR__) . '/db.php';

if (!file_exists($db_path)) {
    echo json_encode([
        'success' => true,
        'online' => false,
        'agent_count' => 0,
        'error' => 'Database connection file not found'
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

    // Check if any customer care user is online
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as online_count
        FROM users u
        INNER JOIN roles r ON u.role_id = r.id
        WHERE r.role_name = 'customer_care'
        AND u.is_active = 1
        AND u.online_status = 1
    ");
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $is_online = $result['online_count'] > 0;
    
    echo json_encode([
        'success' => true,
        'online' => $is_online,
        'agent_count' => intval($result['online_count'])
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Check CC Status API Error: " . $e->getMessage());
    
    // Return offline by default on error (failsafe to WhatsApp)
    echo json_encode([
        'success' => true,
        'online' => false,
        'agent_count' => 0,
        'error' => $e->getMessage()
    ]);
}
?>