<?php
/**
 * Motor Fix - Enhanced Universal Logout Script
 *
 * Features:
 * - Clears remember me cookies
 * - Proper session cleanup
 * - Database session management
 *
 * @version 3.0
 * @date Sunday, October 19, 2025
 */

session_start();
require_once __DIR__ . '/../db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $session_id = session_id();

    // Deactivate the current session in the database
    $stmt = $pdo->prepare("UPDATE logged_in_devices SET is_active = 0 WHERE session_id = ?");
    $stmt->execute([$session_id]);
    
    // If user has a remember me cookie, deactivate that session too
    if (isset($_COOKIE['mf_remember_token'])) {
        $remember_token = $_COOKIE['mf_remember_token'];
        $stmt = $pdo->prepare("UPDATE logged_in_devices SET is_active = 0 WHERE session_id = ?");
        $stmt->execute([$remember_token]);
        
        // Clear the remember me cookie
        setcookie('mf_remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Check for any other active sessions for this user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM logged_in_devices WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user_id]);
    $active_sessions_count = $stmt->fetchColumn();
    
    // If this was the last active session, set the user's main status to offline
    if ($active_sessions_count == 0) {
        $stmt = $pdo->prepare("UPDATE users SET online_status = 0 WHERE id = ?");
        $stmt->execute([$user_id]);
    }
}

// Standard logout procedure
$_SESSION = [];

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

// Redirect to the login page with status message
header("Location: login.php?status=loggedout");
exit();
?>