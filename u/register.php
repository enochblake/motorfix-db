<?php
/**
 * Motor Fix - Temporary Super Admin Registration
 *
 * This is a temporary script for development and testing purposes only.
 * It allows the creation of an initial super admin account.
 *
 * WARNING: This file should be DELETED from the production server
 * once the initial super admin account has been created.
 * Leaving it on a live server poses a significant security risk.
 *
 * @version 1.0
 * @date Monday, October 13, 2025
 */

// Start session to handle user feedback messages.
session_start();

// Include the master database connection file.
// Assumes this file is in a '/u/' directory, and db.php is in the root.
require_once __DIR__ . '/../db.php';

$error_message = '';
$success_message = '';

// Check for feedback messages from a previous attempt.
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}


// --- FORM PROCESSING ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize and retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Basic Validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    }

    try {
        // 3. Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "An account with this email already exists.";
            header("Location: register.php");
            exit();
        }

        // 4. Hash the password securely
        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        if ($password_hash === false) {
             throw new Exception("Password hashing failed.");
        }
        
        // 5. Insert the new super admin user into the database
        // role_id for 'super_admin' is 1 as per the database schema.
        // branch_id is NULL for super_admin.
        $sql = "INSERT INTO users (name, email, password_hash, role_id, is_active, online_status, last_login_timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        
        // is_active = 1 (true), online_status = 1 (true), last_login_timestamp = current time.
        // This simulates an immediate login upon registration.
        if ($stmt->execute([$name, $email, $password_hash, 1, 1, 1])) {
            
            // Set session variables to log the user in
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = 'super_admin';

            // Redirect to the super admin dashboard
            header("Location: sdashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Registration failed. Please try again.";
            header("Location: register.php");
            exit();
        }

    } catch (PDOException $e) {
        // Log the detailed error and show a generic message
        error_log("Registration Error: " . $e->getMessage());
        $_SESSION['error_message'] = "A database error occurred. Could not register the user.";
        header("Location: register.php");
        exit();
    } catch (Exception $e) {
        error_log("General Error: " . $e->getMessage());
        $_SESSION['error_message'] = "An unexpected error occurred. Please try again.";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Super Admin Account</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .register-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-container h1 {
            color: #000;
            margin-bottom: 0.5rem;
        }
        .register-container p {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #007bff; /* Blue */
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background-color: #28a745; /* Green */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Super Admin Setup</h1>
        <p>Create your initial administrator account.</p>

        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
    </div>
</body>
</html>
