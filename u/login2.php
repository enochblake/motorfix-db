<?php
/**
 * Motor Fix - Universal Dashboard Login (Fixed Paths & Debugging)
 *
 * Handles secure authentication for all user roles.
 * Correctly points to root directory for core files.
 * Includes enhanced debugging based on .env settings.
 *
 * @version 2.1
 * @date Tuesday, October 14, 2025
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- STEP 1: LOAD CORE FILES & START DEBUGGING ---

// Path to the project root directory
$root_path = __DIR__ . '/..';

// Load Composer's autoloader for PHPMailer etc.
require_once $root_path . '/vendor/autoload.php';
// Load the database connection and environment variables
require_once $root_path . '/db.php'; // This file now handles the .env loading

// --- Smart Error Reporting ---
// This will display errors only if APP_ENV in .env is 'development'
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Start a secure session AFTER including files
session_start();


// --- STEP 2: HELPER FUNCTIONS ---

function sendLoginNotification($user_email, $user_name, $ip_address, $user_agent) {
    // This function remains the same, but it's good practice to have it here.
    // Make sure your .env has correct SMTP_ settings
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom('admin@motorfix.co.ke', 'Motor Fix Security');
        $mail->addAddress($user_email, $user_name);
        $mail->addBCC('info@motorfix.co.ke');

        $mail->isHTML(true);
        $mail->Subject = 'Security Alert: New Login to Your Account';
        $login_time = date('l, F j, Y \a\t g:i A');
        $mail->Body    = "<p>Hello {$user_name},</p><p>Your account was just accessed:</p><p><strong>Time:</strong> {$login_time}<br><strong>IP Address:</strong> {$ip_address}<br><strong>Device:</strong> {$user_agent}</p><p>If this was not you, please contact administration immediately.</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

function redirect_by_role($role) {
    // These are relative paths, which is correct since all dashboard files are in /u/
    switch ($role) {
        case 'super_admin': header("Location: sdashboard.php"); break;
        case 'admin': header("Location: adashboard.php"); break;
        case 'customer_care': header("Location: cdashboard.php"); break;
        default: header("Location: login.php?error=role"); break;
    }
    exit();
}


// --- STEP 3: MAIN SCRIPT LOGIC ---

$error_message = '';

if (isset($_SESSION['user_id'])) {
    redirect_by_role($_SESSION['user_role']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_active']) {
                    $error_message = "Your account is deactivated.";
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role_name'];

                    $pdo->prepare("UPDATE users SET last_login_timestamp = NOW(), online_status = 1 WHERE id = ?")->execute([$user['id']]);
                    
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    
                    $pdo->prepare("INSERT INTO logged_in_devices (user_id, session_id, ip_address, user_agent) VALUES (?, ?, ?, ?)")
                        ->execute([$user['id'], session_id(), $ip_address, $user_agent]);

                    sendLoginNotification($user['email'], $user['name'], $ip_address, $user_agent);
                    redirect_by_role($user['role_name']);
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login PDO Error: " . $e->getMessage());
            $error_message = "A database error occurred.";
            // For console debugging in dev mode
            if ($_ENV['APP_ENV'] === 'development') {
                echo "<script>console.error('PDO Error: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login - Motor Fix</title>
    <!-- Corrected path to logo -->
    <link rel="icon" href="../media/slogo.png" type="image/png">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background-color: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        .login-logo img { height: 60px; margin-bottom: 1rem; }
        h1 { color: #212529; margin-bottom: 0.5rem; font-size: 1.8rem; }
        p { color: #6c757d; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input { width: 100%; padding: 0.8rem; border: 1px solid #ced4da; border-radius: 8px; font-size: 1rem; box-sizing: border-box; transition: all 0.2s; }
        input:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2); }
        button { width: 100%; padding: 0.85rem; background-color: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
        button:hover { background-color: #218838; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Corrected path to logo -->
        <div class="login-logo"><img src="../media/slogo.png" alt="Motor Fix Logo"></div>
        <h1>Dashboard Access</h1>
        <p>Please sign in to continue.</p>
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
