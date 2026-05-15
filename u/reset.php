<?php
/**
 * Motor Fix - Password Reset Request Page (Timezone Fix)
 * @version 1.2
 * @date Tuesday, October 14, 2025
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../db.php'; // This now sets the timezone

$feedback_message = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = "Please enter a valid email address.";
        $feedback_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            if ($user = $stmt->fetch()) {
                $token = bin2hex(random_bytes(32));
                $expires_at = (new DateTime('+1 hour'))->format('Y-m-d H:i:s'); // Use PHP's time

                $pdo->prepare("INSERT INTO password_resets (email, token, purpose, expires_at) VALUES (?, ?, 'reset', ?)")->execute([$email, $token, $expires_at]);

                $reset_link = "https://dash.motorfix.co.ke/setpass.php?token=" . $token;
                
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port = $_ENV['SMTP_PORT'];
                $mail->setFrom('admin@motorfix.co.ke', 'Motor Fix Support');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "<p>You requested a password reset. Click the link below to set a new password. This link is valid for 1 hour.</p><p><a href='{$reset_link}'>Reset Your Password</a></p>";
                $mail->send();
            }
            $feedback_message = "If an account with that email exists, a password reset link has been sent.";
            $feedback_type = 'success';
        } catch (Exception $e) {
            error_log("Password Reset Error: " . $e->getMessage());
            $feedback_message = "An error occurred. Could not process your request.";
            $feedback_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Motor Fix</title>
    <link rel="icon" href="../media/slogo.png" type="image/png">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } .form-container { background-color: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; } .logo img { height: 60px; margin-bottom: 1rem; } h1 { color: #212529; margin-bottom: 0.5rem; } p { color: #6c757d; margin-bottom: 2rem; } .form-group { margin-bottom: 1.5rem; text-align: left; } label { display: block; margin-bottom: 0.5rem; font-weight: 600; } input { width: 100%; padding: 0.8rem; border: 1px solid #ced4da; border-radius: 8px; font-size: 1rem; box-sizing: border-box; } button { width: 100%; padding: 0.85rem; background-color: #007bff; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; } .feedback { padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; font-weight: 500; } .feedback.success { background-color: #d4edda; color: #155724; } .feedback.error { background-color: #f8d7da; color: #721c24; } .login-link { margin-top: 1.5rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="logo"><img src="../media/slogo.png" alt="Motor Fix Logo"></div>
        <h1>Forgot Your Password?</h1>
        <p>Enter your email and we'll send you a link to get back into your account.</p>
        <?php if ($feedback_message): ?>
            <div class="feedback <?php echo $feedback_type; ?>"><?php echo htmlspecialchars($feedback_message); ?></div>
        <?php endif; ?>
        <form action="reset.php" method="POST">
            <div class="form-group"><label for="email">Email Address</label><input type="email" id="email" name="email" required></div>
            <button type="submit">Send Reset Link</button>
        </form>
        <div class="login-link"><a href="login.php">Back to Login</a></div>
    </div>
</body>
</html>

