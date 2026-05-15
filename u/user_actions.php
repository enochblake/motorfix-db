<?php
/**
 * Motor Fix - Backend User Actions Handler (Upgraded)
 *
 * Handles force logout and secure user deletion with password verification.
 *
 * @version 2.0
 * @date Tuesday, October 14, 2025
 */

require_once __DIR__ . '/../db.php';
session_start();

// Security Check: Only super admins can perform these actions.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    die("Access Denied.");
}

$action = $_POST['action'] ?? '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'force_logout' && isset($_POST['session_id_to_logout'])) {
            $session_to_logout_id = (int)$_POST['session_id_to_logout'];
            $stmt = $pdo->prepare("SELECT user_id FROM logged_in_devices WHERE id = ?");
            $stmt->execute([$session_to_logout_id]);
            $session_details = $stmt->fetch();

            if ($session_details) {
                $pdo->prepare("UPDATE logged_in_devices SET is_active = 0 WHERE id = ?")->execute([$session_to_logout_id]);
                $user_id_to_logout = $session_details['user_id'];
                
                $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM logged_in_devices WHERE user_id = ? AND is_active = 1");
                $stmt_count->execute([$user_id_to_logout]);
                if ($stmt_count->fetchColumn() == 0) {
                    $pdo->prepare("UPDATE users SET online_status = 0 WHERE id = ?")->execute([$user_id_to_logout]);
                }
                $_SESSION['feedback_message'] = "Session has been successfully logged out.";
                $_SESSION['feedback_type'] = 'success';
            }
        } elseif ($action === 'delete_user' && isset($_POST['user_id_to_delete'], $_POST['super_admin_password'])) {
            $user_id_to_delete = (int)$_POST['user_id_to_delete'];
            $super_admin_password = $_POST['super_admin_password'];
            $super_admin_id = $_SESSION['user_id'];

            // Prevent self-deletion
            if ($user_id_to_delete === $super_admin_id) {
                $_SESSION['feedback_message'] = "You cannot delete your own account.";
                $_SESSION['feedback_type'] = 'error';
            } else {
                // Verify super admin's password
                $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([$super_admin_id]);
                $super_admin = $stmt->fetch();

                if ($super_admin && password_verify($super_admin_password, $super_admin['password_hash'])) {
                    // Password is correct, proceed with deletion
                    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id_to_delete]);
                    $_SESSION['feedback_message'] = "User has been permanently deleted.";
                    $_SESSION['feedback_type'] = 'success';
                } else {
                    // Password was incorrect
                    $_SESSION['feedback_message'] = "Incorrect password. User deletion cancelled.";
                    $_SESSION['feedback_type'] = 'error';
                }
            }
        }
    }
} catch (PDOException $e) {
    error_log("User Action Error: " . $e->getMessage());
    $_SESSION['feedback_message'] = "A database error occurred.";
    $_SESSION['feedback_type'] = 'error';
}

header("Location: manage_users.php");
exit();

