<?php
/**
 * AJAX Handler for Single Product Image Upload
 * - Receives one image file via POST.
 * - Validates the image.
 * - Saves it with a unique name.
 * - Returns JSON response with success status and filename or error message.
 *
 * NOTE: This script assumes it's in the same directory as products.php
 * and can access the configuration and functions defined there if needed,
 * or configuration is redefined here.
 */

// Basic Security & Session Start (reuse from aheader if possible, simplified here)
session_start(); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit;
}

header('Content-Type: application/json');

// --- CONFIGURATION (Redefine or include from a shared config file) ---
$main_site_url = 'https://motorfix.co.ke'; 
// *** CRITICAL: Ensure this path is correct AND writable by the web server! ***
$upload_dir_server_path = realpath(__DIR__ . '/../../u/media/products'); 
if (!$upload_dir_server_path || !is_dir($upload_dir_server_path)) {
    $fallback_path = '/home/' . get_current_user() . '/public_html/u/media/products'; 
     if (is_dir($fallback_path)) {
          $upload_dir_server_path = $fallback_path;
     } else {
         error_log("AJAX Upload Error: Upload directory path misconfigured or does not exist: " . ($upload_dir_server_path ?: 'Path not resolved'));
         echo json_encode(['success' => false, 'error' => 'Server configuration error [Upload Path].']);
         exit;
     }
}
if (!is_writable($upload_dir_server_path)) {
     error_log("AJAX Upload Error: Upload directory not writable: " . $upload_dir_server_path);
     echo json_encode(['success' => false, 'error' => 'Server configuration error [Permissions].']);
     exit;
}

$allowed_image_types_check = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$max_image_size = 5 * 1024 * 1024; // 5 MB

// Helper function (redefined or included)
function generateUniqueFilename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    if (!$extension) $extension = 'jpg'; 
    return 'prod_' . uniqid() . '_' . time() . '.' . strtolower($extension);
}
// --- END CONFIGURATION ---


// Check if file was uploaded
if (!isset($_FILES['product_image']) || !is_uploaded_file($_FILES['product_image']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error.']);
    exit;
}

$file = $_FILES['product_image'];

// Basic Validation
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Upload error code: ' . $file['error']]);
    exit;
}

if ($file['size'] > $max_image_size) {
    echo json_encode(['success' => false, 'error' => 'File exceeds maximum size (' . ($max_image_size / 1024 / 1024) . ' MB).']);
    exit;
}

// MIME Type Check (with fallback)
$mime_type = '';
if (function_exists('mime_content_type')) {
    $mime_type = mime_content_type($file['tmp_name']);
} else {
    // Fallback using browser-provided type (less secure)
    $mime_type = $file['type'];
    error_log("Warning: mime_content_type() function not available. Relying on browser-provided type for AJAX upload: " . $mime_type);
}

if (!in_array($mime_type, $allowed_image_types_check)) {
     echo json_encode(['success' => false, 'error' => 'Invalid file type (' . htmlspecialchars($mime_type) . '). Allowed types: JPG, PNG, WEBP, GIF.']);
     exit;
}

// Generate unique name and move file
$original_name = $file['name'];
$unique_name = generateUniqueFilename($original_name);
$destination = $upload_dir_server_path . '/' . $unique_name;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    // Return success response with the unique filename
    echo json_encode([
        'success' => true, 
        'filename' => $unique_name,
        // Include a public URL for potential preview updates if needed
        'previewUrl' => rtrim($main_site_url, '/') . '/u/media/products/' . $unique_name 
    ]);
} else {
    error_log("AJAX Upload Error: Failed to move file '{$original_name}' to '{$destination}'. Check permissions.");
    echo json_encode(['success' => false, 'error' => 'Server error saving file.']);
}

exit;
?>
