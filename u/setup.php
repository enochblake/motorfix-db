<?php
/**
 * Motor Fix - New User Account Setup (Timezone Fix)
 * @version 1.1
 * @date Tuesday, October 14, 2025
 */
require_once __DIR__ . '/../db.php'; // This now sets the timezone

$token = $_GET['token'] ?? '';
$feedback_message = '';
$feedback_type = 'error';
$is_token_valid = false;
$user_email = '';

if (empty($token)) {
    $feedback_message = "No setup token provided. Please use the link from your invitation email.";
} else {
    try {
        // The check now works because PHP's current time and the database time are in sync.
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND purpose = 'setup' AND expires_at > ?");
        $stmt->execute([$token, date('Y-m-d H:i:s')]);
        $token_data = $stmt->fetch();

        if ($token_data) {
            $is_token_valid = true;
            $user_email = $token_data['email'];
        } else {
            // Clean up expired tokens for this email to allow a new invite to be sent.
            $pdo->prepare("DELETE FROM password_resets WHERE email = (SELECT email FROM password_resets WHERE token = ?) AND purpose = 'setup'")->execute([$token]);
            $feedback_message = "This setup link is invalid or has expired. Please ask an administrator to send a new invitation.";
        }
    } catch (PDOException $e) {
        error_log("Setup Token Validation Error: " . $e->getMessage());
        $feedback_message = "A database error occurred.";
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_token_valid) {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name) || empty($password) || $password !== $password_confirm) {
        $feedback_message = "Please fill out all fields and ensure passwords match.";
    } elseif (strlen($password) < 8) {
        $feedback_message = "Password must be at least 8 characters long.";
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_ARGON2ID);
            
            // Activate the user, set their name and password
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password_hash = ?, is_active = 1 WHERE email = ?");
            $stmt->execute([$name, $password_hash, $user_email]);

            // Clean up the used token
            $pdo->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);
            
            $feedback_message = "Account setup complete! You can now log in.";
            $feedback_type = 'success';
            $is_token_valid = false; // Prevent form resubmission

        } catch (Exception $e) {
            error_log("Account Setup Error: " . $e->getMessage());
            $feedback_message = "An error occurred while setting up your account.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Setup - Motor Fix</title>
    <link rel="icon" href="../media/slogo.png" type="image/png">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } .form-container { background-color: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; } .logo img { height: 60px; margin-bottom: 1rem; } h1 { color: #212529; } p { color: #6c757d; margin-bottom: 2rem; } .form-group { margin-bottom: 1.5rem; text-align: left; } label { display: block; margin-bottom: 0.5rem; font-weight: 600; } input { width: 100%; padding: 0.8rem; border: 1px solid #ced4da; border-radius: 8px; font-size: 1rem; box-sizing: border-box; } input[type="email"]:read-only { background-color: #e9ecef; } button { width: 100%; padding: 0.85rem; background-color: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; } .feedback { padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; font-weight: 500; } .feedback.success { background-color: #d4edda; color: #155724; } .feedback.error { background-color: #f8d7da; color: #721c24; } .login-link { margin-top: 1.5rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="logo"><img src="../media/slogo.png" alt="Motor Fix Logo"></div>
        <h1>Complete Your Account Setup</h1>
        <?php if ($feedback_message): ?>
            <div class="feedback <?php echo $feedback_type; ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
                <?php if ($feedback_type === 'success'): ?><p><a href="login.php">Proceed to Login</a></p><?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($is_token_valid): ?>
        <p>Welcome! Set your name and password to get started.</p>
        <form action="setup.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <div class="form-group"><label for="email">Email Address</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly></div>
            <div class="form-group"><label for="name">Full Name</label><input type="text" id="name" name="name" required></div>
            <div class="form-group"><label for="password">New Password</label><input type="password" id="password" name="password" required></div>
            <div class="form-group"><label for="password_confirm">Confirm New Password</label><input type="password" id="password_confirm" name="password_confirm" required></div>
            <button type="submit">Complete Setup</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>

