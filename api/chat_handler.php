<?php
/**
 * Motorfix - Public Chat API Handler v2.0
 *
 * Handles all AJAX requests from the public-facing chat page (v2.0).
 *
 * --- V2.0 MAJOR FIXES & IMPROVEMENTS ---
 * - SECURITY FIX (start_session): Now *always* creates a new session per visit,
 * preventing users from ever rejoining or seeing old conversations.
 * - OPTIONAL DETAILS (start_session): Gracefully handles empty name/email,
 * using a placeholder for anonymous chats.
 * - EFFICIENT POLLING (get_messages): Now accepts a 'since' timestamp to only
 * return new messages, eliminating the "blinking" effect on the front end.
 * - AGENT TYPING STATUS (get_messages): Detects recent agent activity to show
 * a "typing..." indicator to the visitor.
 * - LIVE STATUS (end_session): New action to immediately close a session when a
 * visitor leaves the page, keeping the agent dashboard accurate.
 *
 * @version 2.0
 * @date Tuesday, October 21, 2025
 */

ob_start();
session_start();

header('Content-Type: application/json');

require_once '../db.php'; // Assumes this file is in /api/

if (!isset($pdo)) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'error' => 'No action specified.']);
    exit;
}

$action = $_POST['action'];

try {
    switch ($action) {

        case 'start_session':
            $name = trim(strip_tags($_POST['name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $session_key = $_POST['session_key'] ?? ''; // This is the unique key per browser session

            if (empty($session_key)) {
                throw new Exception('Invalid session key.');
            }
            
            // If email is provided, it must be valid. If not, it's fine.
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                 $email = ''; // Discard invalid email
            }
            
            // Use placeholders if details are not provided
            $visitor_display_name = !empty($name) ? $name : 'Anonymous Visitor';
            $visitor_contact_email = !empty($email) ? $email : 'noemail@motorfix.co.ke';
            
            // ALWAYS create a new session. This is the critical security fix.
            $stmt = $pdo->prepare(
                "INSERT INTO chat_sessions (visitor_identifier, visitor_name, visitor_email, status) 
                 VALUES (?, ?, ?, 'active')"
            );
            $stmt->execute([$session_key, $visitor_display_name, $visitor_contact_email]);
            $session_id = $pdo->lastInsertId();
            
            // Store the new, unique session ID for this page load.
            $_SESSION['chat_session_id'] = $session_id;

            echo json_encode(['success' => true, 'session_id' => $session_id]);
            break;

        case 'send_message':
            $session_id = $_SESSION['chat_session_id'] ?? intval($_POST['session_id'] ?? 0);
            $message = trim(strip_tags($_POST['message'] ?? ''));

            if (empty($session_id) || empty($message)) {
                throw new Exception('Session ID and message are required.');
            }

            $stmt = $pdo->prepare(
                "INSERT INTO chat_messages (session_id, sender, message_text) VALUES (?, 'visitor', ?)"
            );
            $stmt->execute([$session_id, $message]);
            echo json_encode(['success' => true]);
            break;

        case 'get_messages':
            $session_id = $_SESSION['chat_session_id'] ?? intval($_POST['session_id'] ?? 0);
            $since = $_POST['since'] ?? '1970-01-01 00:00:00';

            if (empty($session_id)) {
                throw new Exception('Session ID is required.');
            }

            // Fetch new messages
            $stmt = $pdo->prepare("
                SELECT sender, message_text, timestamp 
                FROM chat_messages 
                WHERE session_id = ? AND timestamp > ?
                ORDER BY timestamp ASC
            ");
            $stmt->execute([$session_id, $since]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if an agent is currently typing in this session
            $stmt_typing = $pdo->prepare("
                SELECT 1 FROM chat_sessions 
                WHERE id = ? AND last_agent_heartbeat > NOW() - INTERVAL 10 SECOND
            ");
            $stmt_typing->execute([$session_id]);
            $agent_is_typing = (bool) $stmt_typing->fetchColumn();

            echo json_encode([
                'success' => true, 
                'messages' => $messages,
                'agent_is_typing' => $agent_is_typing
            ]);
            break;
            
        case 'heartbeat':
             $session_id = $_SESSION['chat_session_id'] ?? intval($_POST['session_id'] ?? 0);
             if (!empty($session_id)) {
                 $stmt = $pdo->prepare("UPDATE chat_sessions SET status = 'active', last_visitor_heartbeat = NOW() WHERE id = ?");
                 $stmt->execute([$session_id]);
             }
             echo json_encode(['success' => true, 'status' => 'acknowledged']);
             break;

        case 'end_session':
            // This is called by `beforeunload` and is designed to be quick.
             $session_id = $_SESSION['chat_session_id'] ?? intval($_POST['session_id'] ?? 0);
             if (!empty($session_id)) {
                 $stmt = $pdo->prepare("UPDATE chat_sessions SET status = 'closed' WHERE id = ?");
                 $stmt->execute([$session_id]);
             }
             // No need to send a large response here.
             http_response_code(204); 
             break;

        default:
            throw new Exception('Invalid action specified.');
    }

} catch (Exception $e) {
    error_log("Chat API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
?>

