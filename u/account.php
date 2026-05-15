<?php
/**
 * Motor Fix - Admin Account Management
 *
 * Allows logged-in admins to update their name, email, or password.
 * All changes require current password verification for security.
 *
 * @version 1.0
 * @date Thursday, October 23, 2025
 */

// Includes session, db connection, and security checks
require_once __DIR__ . '/ic/aheader.php';

// Initialize feedback variables
$details_feedback = '';
$details_type = '';
$password_feedback = '';
$password_type = '';

// Check for session-based feedback messages
if (isset($_SESSION['feedback_message'])) {
    $details_feedback = $_SESSION['feedback_message'];
    $details_type = $_SESSION['feedback_type'] ?? 'error';
    // Unset the session variables so the message doesn't appear on reload
    unset($_SESSION['feedback_message'], $_SESSION['feedback_type']);
}

try {
    // --- Main POST Request Handler ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $user_id = $_SESSION['user_id'];

        // --- SECTION 1: Handle Account Detail Updates ---
        if (isset($_POST['update_details'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $current_password = $_POST['current_password_details'];

            // 1.1: Validate input
            if (empty($name) || empty($email) || empty($current_password)) {
                $details_feedback = "All fields are required to update details.";
                $details_type = 'error';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $details_feedback = "Please enter a valid email address.";
                $details_type = 'error';
            } else {
                // 1.2: Verify current password
                $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current_password, $user['password_hash'])) {
                    // 1.3: Check if email is being changed and if it's already taken
                    if ($email !== $_SESSION['user_email']) {
                        $stmt_email = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                        $stmt_email->execute([$email, $user_id]);
                        if ($stmt_email->fetch()) {
                            $details_feedback = "That email address is already in use by another account.";
                            $details_type = 'error';
                        }
                    }

                    // 1.4: If no email conflict, proceed with update
                    if (empty($details_feedback)) {
                        $stmt_update = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                        $stmt_update->execute([$name, $email, $user_id]);

                        // CRITICAL: Update session variables to reflect the change
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;

                        // Set session feedback and redirect
                        $_SESSION['feedback_message'] = 'Your account details have been updated successfully.';
                        $_SESSION['feedback_type'] = 'success';
                        header("Location: account.php");
                        exit();
                    }
                } else {
                    $details_feedback = "Your current password was incorrect. Details not saved.";
                    $details_type = 'error';
                }
            }
        }

        // --- SECTION 2: Handle Password Change ---
        elseif (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password_pass'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // 2.1: Validate input
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $password_feedback = "All password fields are required.";
                $password_type = 'error';
            } elseif (strlen($new_password) < 8) {
                $password_feedback = "New password must be at least 8 characters long.";
                $password_type = 'error';
            } elseif ($new_password !== $confirm_password) {
                $password_feedback = "Your new passwords do not match.";
                $password_type = 'error';
            } else {
                // 2.2: Verify current password
                $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current_password, $user['password_hash'])) {
                    // 2.3: Hash and update new password
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $stmt_update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt_update->execute([$new_password_hash, $user_id]);

                    // Set session feedback and redirect
                    $_SESSION['feedback_message'] = 'Your password has been changed successfully.';
                    $_SESSION['feedback_type'] = 'success';
                    header("Location: account.php");
                    exit();
                } else {
                    $password_feedback = "Your current password was incorrect. Password not changed.";
                    $password_type = 'error';
                }
            }
        }
    }
} catch (PDOException $e) {
    // General database error
    error_log("Account Page Error: " . $e->getMessage());
    $details_feedback = "A database error occurred. Please try again later.";
    $details_type = 'error';
}
?>

<main class="mfa-main-content">
    <div class="mfa-page-title">
        <h1>My Account</h1>
        <p>Update your personal details and manage your password.</p>
    </div>

    <!-- Session Feedback Area -->
    <?php if ($details_feedback && isset($_SESSION['feedback_message'])): // Only show session feedback once ?>
        <div class="mfa-feedback-message <?php echo htmlspecialchars($details_type); ?>">
            <?php echo htmlspecialchars($details_feedback); ?>
        </div>
    <?php endif; ?>

    <!-- Panel 1: Update Details -->
    <div class="mfa-content-panel">
        <h2><i class="fas fa-user-edit"></i> Update Your Details</h2>
        
        <!-- Details Form Feedback -->
        <?php if ($details_feedback && !isset($_SESSION['feedback_message'])): ?>
            <div class="mfa-feedback-message <?php echo htmlspecialchars($details_type); ?>">
                <?php echo htmlspecialchars($details_feedback); ?>
            </div>
        <?php endif; ?>

        <form action="account.php" method="POST" class="mfa-form-grid">
            <input type="hidden" name="update_details" value="1">
            
            <!-- Name -->
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
            </div>
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" required>
            </div>
            
            <!-- Current Password -->
            <div class="form-group form-group-full">
                <label for="current_password_details">Confirm with Password</label>
                <input type="password" id="current_password_details" name="current_password_details" placeholder="Enter your current password to save changes" required>
            </div>
            
            <!-- Submit -->
            <div class="form-group form-group-full">
                <button type="submit" class="mfa-button-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Panel 2: Change Password -->
    <div class="mfa-content-panel">
        <h2><i class="fas fa-key"></i> Change Password</h2>
        
        <!-- Password Form Feedback -->
        <?php if ($password_feedback): ?>
            <div class="mfa-feedback-message <?php echo htmlspecialchars($password_type); ?>">
                <?php echo htmlspecialchars($password_feedback); ?>
            </div>
        <?php endif; ?>

        <form action="account.php" method="POST" class="mfa-form-grid">
            <input type="hidden" name="change_password" value="1">
            
            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password_pass">Current Password</label>
                <input type="password" id="current_password_pass" name="current_password_pass" required>
            </div>
            
            <!-- New Password -->
            <div class="form-group">
                <label for="new_password">New Password (min. 8 characters)</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            
            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <!-- Submit -->
            <div class="form-group form-group-full">
                <button type="submit" class="mfa-button-primary">Set New Password</button>
            </div>

            <!-- Forgot Password Link -->
            <div class="form-group form-group-full">
                <p class="mfa-form-note">
                    Forgot your current password? 
                    <a href="../reset.php" target="_blank">Reset it here</a>
                </p>
            </div>
        </form>
    </div>
</main>

<style>
    /* Styles for Account Page (using mfa- prefixes) */
    .mfa-main-content {
        padding: 20px 30px;
        max-width: 900px;
        margin: 0 auto;
    }

    .mfa-page-title h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: var(--mfa-dark);
    }

    .mfa-page-title p {
        font-size: 1rem;
        color: var(--mfa-text-light);
        margin-top: 0;
    }

    /* Feedback Messages */
    .mfa-feedback-message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .mfa-feedback-message.success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .mfa-feedback-message.error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    /* Content Panels */
    .mfa-content-panel {
        background-color: var(--mfa-white);
        padding: 25px 30px;
        border-radius: 12px;
        border: 1px solid var(--mfa-grey-border);
        margin-top: 25px;
        box-shadow: 0 4px 6px var(--mfa-shadow);
    }

    .mfa-content-panel h2 {
        margin: 0 0 25px 0;
        font-size: 1.3rem;
        color: var(--mfa-dark);
        border-bottom: 1px solid var(--mfa-grey-border);
        padding-bottom: 15px;
    }

    /* Form Grid */
    .mfa-form-grid {
        display: grid;
        grid-template-columns: 1fr; /* Default to single column */
        gap: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--mfa-text);
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--mfa-grey-border);
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box; /* Important for padding to work right */
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--mfa-blue);
        box-shadow: 0 0 0 3px rgba(0,123,255,0.15);
    }

    /* Primary Button */
    .mfa-button-primary {
        background-color: var(--mfa-blue);
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: background-color 0.2s ease;
    }

    .mfa-button-primary:hover {
        background-color: #0069d9;
    }

    /* Form Note for Forgot Password */
    .mfa-form-note {
        font-size: 0.9rem;
        color: var(--mfa-text-light);
        margin: 10px 0 0 0;
    }
    .mfa-form-note a {
        color: var(--mfa-blue);
        font-weight: 500;
        text-decoration: none;
    }
    .mfa-form-note a:hover {
        text-decoration: underline;
    }

    /* Responsive Grid */
    @media (min-width: 768px) {
        .mfa-form-grid {
            grid-template-columns: repeat(2, 1fr); /* Two columns on desktop */
        }
        
        .form-group-full {
            grid-column: 1 / -1; /* Make this span full width */
        }
    }
</style>

<?php 
// Includes the standard admin footer
require_once __DIR__ . '/ic/afooter.php'; 
?>
