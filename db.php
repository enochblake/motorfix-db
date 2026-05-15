<?php

/**
 * Motor Fix - Database Connection File
 *
 * This file is the central point of connection to the MySQL database for the entire application.
 * It uses PDO for a secure, fast, and consistent database interface.
 * It securely loads credentials from a .env file and provides a robust error handling mechanism.
 *
 * @version 1.3 - Implemented robust UTC offset timezone fix.
 * @date Tuesday, October 14, 2025
 */

// 1. COMPOSER AUTOLOADER
require_once __DIR__ . '/vendor/autoload.php';

// 2. LOAD ENVIRONMENT VARIABLES
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // This line is correct and necessary. It ensures the timezone variable is available.
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'APP_ENV', 'APP_TIMEZONE']);
} catch (\Dotenv\Exception\InvalidPathException $e) {
    die("Error: The .env configuration file is missing. Please create one based on .env.example.");
} catch (\Dotenv\Exception\ValidationException $e) {
    die("Error: Missing required environment variables. " . $e->getMessage());
}

// This line is correct and necessary for all PHP date/time functions.
date_default_timezone_set($_ENV['APP_TIMEZONE']);


// 3. ESTABLISH PDO DATABASE CONNECTION
$dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Your original, working connection line.
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
    
    // --- !! ROBUST TIMEZONE FIX START !! ---
    // This new method is compatible with ALL MySQL servers.
    // It calculates the UTC offset (e.g., '+03:00') from your APP_TIMEZONE setting.
    // This avoids the "Unknown or incorrect time zone" error permanently.
    $timezone_offset = (new DateTime('now', new DateTimeZone($_ENV['APP_TIMEZONE'])))->format('P');
    $pdo->exec("SET time_zone = '" . $timezone_offset . "'");
    // --- !! ROBUST TIMEZONE FIX END !! ---

} catch (\PDOException $e) {
    // Log the error regardless of the environment.
    error_log("Database Connection Error: " . $e->getMessage());

    // Check the application environment to determine how to report the error.
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
        // --- PRODUCTION MODE ---
        // Your original, professional maintenance page. UNCHANGED.
        http_response_code(503); // Service Unavailable
        die('
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - Motor Fix Injection Services</title>
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; color: #343a40; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
        .container { max-width: 600px; padding: 2rem; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .logo-placeholder { width: 80px; height: 80px; background-color: #007bff; border: 5px solid #28a745; border-radius: 50%; margin: 0 auto 1.5rem auto; display: flex; justify-content: center; align-items: center; font-size: 2rem; color: white; font-weight: bold; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; color: #000000; }
        p { font-size: 1.1rem; line-height: 1.6; color: #6c757d; }
        .footer { margin-top: 1.5rem; font-size: 0.9rem; color: #adb5bd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-placeholder">MF</div>
        <h1>We are currently down for maintenance.</h1>
        <p>We are performing some scheduled maintenance to improve our services. We will be back online shortly. Thank you for your patience!</p>
        <div class="footer">&copy; ' . date("Y") . ' Motor Fix Injection Services</div>
    </div>
</body>
</html>
        ');
    } else {
        // --- DEVELOPMENT MODE ---
        // Your original, professional debug page. UNCHANGED.
        http_response_code(500); // Internal Server Error
        $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        die('
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Connection Error</title>
    <style>
        body { font-family: Consolas, monaco, monospace; background-color: #1e1e1e; color: #d4d4d4; padding: 2rem; }
        .error-container { background-color: #252526; border: 1px solid #333; border-radius: 8px; padding: 1.5rem; }
        h1 { color: #ce9178; border-bottom: 1px solid #444; padding-bottom: 0.5rem; margin-top: 0; }
        p { font-size: 1.1rem; line-height: 1.6; word-wrap: break-word; }
        code { background-color: #333; padding: 2px 6px; border-radius: 4px; color: #9cdcfe; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Database Connection Failed</h1>
        <p>The application could not connect to the database. Please check your <code>.env</code> file and ensure the database server is running.</p>
        <p><strong>Error Details:</strong></p>
        <p><code>' . $errorMessage . '</code></p>
    </div>
    <script>
        // Also log the error to the browser console for easier debugging.
        console.error("Database Connection Failed: ' . addslashes($errorMessage) . '");
    </script>
</body>
</html>
        ');
    }
}

// If the script reaches this point, the connection object '$pdo' is ready to be used in other pages.
?>

