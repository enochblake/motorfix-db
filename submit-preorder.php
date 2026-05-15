<?php
/**
 * Motorfix - Pre-order Form Submission Handler
 *
 * Handles AJAX request from product.php
 * Validates data and sends an email to the admin.
 * Returns JSON response.
 *
 * @version 1.0
 * @date Monday, October 27, 2025
 */

// --- Configuration ---

// Load .env variables
// This is a simple parser. For production, a library like vlucas/phpdotenv is better.
try {
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            putenv(sprintf('%s=%s', $name, $value));
        }
    }
} catch (Exception $e) {
    // Failed to load .env, proceed with getenv() which might get from server config
    error_log('Failed to parse .env file: ' . $e->getMessage());
}

// Get SMTP details from environment
$admin_email = getenv('SMTP_USER') ?: 'admin@motorfix.co.ke'; // Recipient email
$sender_email = getenv('SMTP_USER') ?: 'admin@motorfix.co.ke'; // "From" email
$smtp_host = getenv('SMTP_HOST');
$smtp_port = getenv('SMTP_PORT');

// Set content type to JSON
header('Content-Type: application/json');

// --- Helper Function ---
function json_response($success, $message) {
    echo json_encode(['success' => (bool)$success, 'message' => (string)$message]);
    exit;
}

// --- Main Logic ---

// 1. Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method.');
}

// 2. Get and sanitize form data
$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
$phone_raw = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
$county = trim(filter_input(INPUT_POST, 'county', FILTER_SANITIZE_STRING));
$part = trim(filter_input(INPUT_POST, 'part', FILTER_SANITIZE_STRING));
$vehicle = trim(filter_input(INPUT_POST, 'vehicle', FILTER_SANITIZE_STRING));
$requirement = trim(filter_input(INPUT_POST, 'requirement', FILTER_SANITIZE_STRING));
$product_name = trim(filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING));
$product_id = intval($_POST['product_id'] ?? 0);

// 3. Validate required fields
if (empty($name)) {
    json_response(false, 'Please enter your name.');
}
if (empty($phone_raw) || !preg_match('/^[0-9]{9}$/', $phone_raw)) {
    json_response(false, 'Please enter a valid 9-digit phone number (e.g., 712345678).');
}
if (empty($part)) {
    json_response(false, 'Please specify the spare part(s) you need.');
}

// Format phone number
$phone = "+254" . $phone_raw;

// 4. Construct Email
$to = $admin_email;
$subject = "New Spare Part Pre-order: " . $product_name;

// Build the email body
$body = "You have received a new pre-order request from the website.\n\n";
$body .= "--- Customer Details ---\n";
$body .= "Name: " . htmlspecialchars($name) . "\n";
$body .= "Phone: " . htmlspecialchars($phone) . "\n";
if (!empty($county)) {
    $body .= "County: " . htmlspecialchars($county) . "\n";
}

$body .= "\n--- Request Details ---\n";
$body .= "Product Page: " . htmlspecialchars($product_name) . " (ID: $product_id)\n";
$body .= "Requested Part(s): " . htmlspecialchars($part) . "\n";
if (!empty($vehicle)) {
    $body .= "Vehicle: " . htmlspecialchars($vehicle) . "\n";
}
if (!empty($requirement)) {
    $body .= "Requirement: \n" . htmlspecialchars($requirement) . "\n";
}

$body .= "\n---\nTimestamp: " . date('Y-m-d H:i:s') . "\n";

// Build email headers
$headers = "From: Motorfix Pre-Order <" . $sender_email . ">\r\n";
$headers .= "Reply-To: " . htmlspecialchars($name) . " <no-reply@motorfix.co.ke>\r\n"; // Use a no-reply, or could be user's email if collected
$headers .= "X-Mailer: PHP/" . phpversion();
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";


// 5. Send Email
/*
 * IMPORTANT: Using mail() function.
 * This relies on the server's local mail configuration (e.g., sendmail, postfix).
 * It does NOT use the SMTP_USER and SMTP_PASS for authentication, as mail()
 * does not support SMTP authentication natively.
 *
 * For authenticated SMTP (using your .env details fully),
 * you MUST use a library like PHPMailer.
 *
 * This script assumes the server running this PHP code is allowed to send
 * mail as 'admin@motorfix.co.ke' via its local mail setup.
 */

// We can try setting the INI values, but it's not guaranteed to work without a mail agent
if ($smtp_host && $smtp_port) {
    ini_set('SMTP', $smtp_host);
    ini_set('smtp_port', $smtp_port);
    ini_set('sendmail_from', $sender_email);
}

try {
    if (mail($to, $subject, $body, $headers)) {
        // Success
        json_response(true, 'Your pre-order has been sent! We will contact you shortly.');
    } else {
        // Mail function failed
        error_log("Pre-order mail() failed. To: $to, Subject: $subject");
        json_response(false, 'Could not send your request. Please try again later or contact us via WhatsApp.');
    }
} catch (Exception $e) {
    error_log("Pre-order mail() exception: " . $e->getMessage());
    json_response(false, 'An error occurred while sending your request.');
}

?>
