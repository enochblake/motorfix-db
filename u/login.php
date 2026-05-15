<?php
/**
 * Motor Fix - Enhanced Universal Dashboard Login (FIXED)
 *
 * CRITICAL FIX:
 * - Remember Me now properly stores BOTH session_id and remember_token
 * - Admin dashboard security check will work with both types of sessions
 * - Added password reset link
 *
 * @version 3.1
 * @date Monday, October 20, 2025
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- STEP 1: LOAD CORE FILES ---
$root_path = __DIR__ . '/..';
require_once $root_path . '/vendor/autoload.php';
require_once $root_path . '/db.php';

// Smart Error Reporting
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Start secure session
session_start();

// --- STEP 2: COOKIE-BASED AUTO-LOGIN CHECK ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['mf_remember_token'])) {
    $remember_token = $_COOKIE['mf_remember_token'];
    
    try {
        // FIXED: Look for remember_token column (we'll add this)
        $stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u 
                               JOIN roles r ON u.role_id = r.id 
                               JOIN logged_in_devices l ON u.id = l.user_id 
                               WHERE l.remember_token = ? AND l.is_active = 1 AND u.is_active = 1");
        $stmt->execute([$remember_token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Restore session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role_name'];
            $_SESSION['user_email'] = $user['email'];
            
            // CRITICAL: Also store the current PHP session_id for the security check
            $pdo->prepare("UPDATE logged_in_devices SET session_id = ?, last_seen = NOW() WHERE remember_token = ?")
                ->execute([session_id(), $remember_token]);
            
            $pdo->prepare("UPDATE users SET online_status = 1, last_login_timestamp = NOW() WHERE id = ?")
                ->execute([$user['id']]);
            
            redirect_by_role($user['role_name']);
        } else {
            // Invalid token, clear cookie
            setcookie('mf_remember_token', '', time() - 3600, '/', '', true, true);
        }
    } catch (PDOException $e) {
        error_log("Auto-login error: " . $e->getMessage());
    }
}

// --- STEP 3: HELPER FUNCTIONS ---
function sendLoginNotification($user_email, $user_name, $user_agent) {
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
        $mail->Subject = 'New Login to Your Motor Fix Account';
        $login_time = date('l, F j, Y \a\t g:i A');
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 40px 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .info-grid { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0; }
        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #dee2e6; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #495057; min-width: 120px; }
        .info-value { color: #212529; }
        .alert-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 6px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; color: #6c757d; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Security Alert</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{$user_name}</strong>,</p>
            <p>We detected a new login to your Motor Fix dashboard account.</p>
            
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Account:</span>
                    <span class="info-value">{$user_email}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Login Time:</span>
                    <span class="info-value">{$login_time}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Device/Browser:</span>
                    <span class="info-value">{$user_agent}</span>
                </div>
            </div>
            
            <div class="alert-box">
                <strong>⚠️ Was this you?</strong>
                <p>If you did not perform this login, please contact the site administrator immediately and change your password.</p>
            </div>
        </div>
        <div class="footer">
            <p><strong>Motor Fix</strong> | Automotive Parts & Services</p>
            <p>For assistance, contact us at <a href="mailto:info@motorfix.co.ke">info@motorfix.co.ke</a></p>
        </div>
    </div>
</body>
</html>
HTML;

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

function redirect_by_role($role) {
    switch ($role) {
        case 'super_admin': header("Location: sdashboard.php"); break;
        case 'admin': header("Location: adashboard.php"); break;
        case 'customer_care': header("Location: cdashboard.php"); break;
        default: header("Location: login.php?error=role"); break;
    }
    exit();
}

// --- STEP 4: MAIN SCRIPT LOGIC ---
$error_message = '';
$success_message = '';

// Check for various statuses
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'loggedout':
            $success_message = 'You have been successfully logged out.';
            break;
        case 'password_reset':
            $success_message = 'Password reset email sent! Check your inbox.';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'forced_logout':
            $error_message = 'Your session was terminated by an administrator.';
            break;
        case 'deactivated':
            $error_message = 'Your account has been deactivated.';
            break;
        case 'unauthorized':
            $error_message = 'You do not have permission to access that resource.';
            break;
    }
}

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    redirect_by_role($_SESSION['user_role']);
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_active']) {
                    $error_message = "Your account is deactivated. Please contact administration.";
                } else {
                    // Successful login
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role_name'];
                    $_SESSION['user_email'] = $user['email'];

                    // Update user status
                    $pdo->prepare("UPDATE users SET last_login_timestamp = NOW(), online_status = 1 WHERE id = ?")
                        ->execute([$user['id']]);
                    
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    
                    // FIXED: Handle Remember Me properly
                    if ($remember_me) {
                        // Generate secure token for persistent login
                        $remember_token = bin2hex(random_bytes(32));
                        
                        // CRITICAL FIX: Store BOTH session_id AND remember_token
                        $pdo->prepare("INSERT INTO logged_in_devices (user_id, session_id, ip_address, user_agent, remember_token) VALUES (?, ?, ?, ?, ?)")
                            ->execute([$user['id'], session_id(), $ip_address, $user_agent, $remember_token]);
                        
                        // Set cookie for 90 days
                        setcookie('mf_remember_token', $remember_token, time() + (90 * 24 * 60 * 60), '/', '', true, true);
                    } else {
                        // Regular session-based login (no remember_token)
                        $pdo->prepare("INSERT INTO logged_in_devices (user_id, session_id, ip_address, user_agent) VALUES (?, ?, ?, ?)")
                            ->execute([$user['id'], session_id(), $ip_address, $user_agent]);
                    }

                    // Send notification email
                    sendLoginNotification($user['email'], $user['name'], $user_agent);
                    
                    redirect_by_role($user['role_name']);
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login PDO Error: " . $e->getMessage());
            $error_message = "A database error occurred. Please try again.";
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
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
    <link rel="icon" href="../media/slogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-wrapper {
            background-color: #ffffff;
            padding: 50px 45px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 460px;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-logo img {
            height: 70px;
            margin-bottom: 20px;
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .login-header h1 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 15px;
        }
        
        .message {
            padding: 14px 18px;
            margin-bottom: 25px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            animation: fadeIn 0.4s ease-in;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .message.error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 16px;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .remember-me label {
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            user-select: none;
        }
        
        .forgot-password {
            font-size: 14px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: #764ba2;
        }
        
        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }
        
        .login-footer p {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: #28a745;
            font-size: 13px;
        }
        
        .security-badge i {
            margin-right: 6px;
        }
        
        @media (max-width: 480px) {
            .login-wrapper {
                padding: 35px 25px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
            
            .form-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <div class="login-logo">
                <img src="../media/slogo.png" alt="Motor Fix Logo">
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to access your dashboard</p>
        </div>

        <?php if ($error_message): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="on">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
            </div>

            <div class="form-footer">
                <div class="remember-me">
                    <input type="checkbox" id="remember_me" name="remember_me" value="1">
                    <label for="remember_me">Keep me signed in</label>
                </div>
                <a href="reset.php" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i>
                <span>Sign In Securely</span>
            </button>
        </form>

        <div class="login-footer">
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Protected by 256-bit SSL encryption</span>
            </div>
            <p style="margin-top: 15px;">
                MotorFix &copy; <?php echo date('Y'); ?><br>
                All rights reserved
            </p>
        </div>
    </div>
</body>
</html>