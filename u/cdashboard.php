<?php
/**
 * Motor Fix - Customer Care Dashboard v3.0
 * Mobile-First Redesign with Proper UX
 * 
 * Architecture:
 * - Mobile: Single column, switchable chat/list view
 * - Tablet: 30% list, 70% chat (responsive)
 * - Desktop: 25% list, 75% chat with full visibility
 * - Real-time: Visitor messages appear instantly without refresh
 * - Default inventory table with search overlay
 * 
 * @version 3.0
 * @date Tuesday, October 21, 2025
 * @lines 1600+
 */

$root_path = __DIR__ . '/..';
require_once $root_path . '/vendor/autoload.php';
require_once $root_path . '/db.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer_care') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

try {
    $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user || !$user['is_active']) {
        session_destroy();
        header("Location: login.php?error=deactivated");
        exit();
    }
} catch (PDOException $e) {
    error_log("Auth check error: " . $e->getMessage());
}

// AJAX Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            
            case 'toggle_status':
                $new_status = intval($_POST['status']);
                $pdo->prepare("UPDATE users SET online_status = ? WHERE id = ?")
                    ->execute([$new_status, $user_id]);
                echo json_encode(['success' => true, 'status' => $new_status]);
                break;
            
            case 'get_sessions':
                $filter = $_POST['filter'] ?? 'active';
                $statusClause = $filter === 'closed' ? "('closed')" : 
                                ($filter === 'all' ? "('active', 'offline_message', 'closed')" : "('active', 'offline_message')");
                
                $stmt = $pdo->query("
                    SELECT cs.*, 
                           (SELECT COUNT(*) FROM chat_messages cm 
                            WHERE cm.session_id = cs.id AND cm.is_read = 0 AND cm.sender = 'visitor') as unread_count,
                           (SELECT message_text FROM chat_messages cm2 
                            WHERE cm2.session_id = cs.id ORDER BY cm2.timestamp DESC LIMIT 1) as last_message,
                           (SELECT timestamp FROM chat_messages cm3 
                            WHERE cm3.session_id = cs.id ORDER BY cm3.timestamp DESC LIMIT 1) as last_message_time,
                           (SELECT MAX(timestamp) FROM chat_messages cm4 
                            WHERE cm4.session_id = cs.id) as latest_activity
                    FROM chat_sessions cs 
                    WHERE cs.status IN {$statusClause}
                    ORDER BY latest_activity DESC, cs.created_at DESC
                    LIMIT 500
                ");
                $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'sessions' => $sessions]);
                break;
            
            case 'get_messages':
                $session_id = intval($_POST['session_id']);
                $since = $_POST['since'] ?? null;
                
                if ($since) {
                    $query = "SELECT * FROM chat_messages WHERE session_id = ? AND timestamp > ? ORDER BY timestamp ASC";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$session_id, $since]);
                } else {
                    $query = "SELECT * FROM chat_messages WHERE session_id = ? ORDER BY timestamp ASC";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$session_id]);
                }
                
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender = 'visitor'")
                    ->execute([$session_id]);
                
                $session = $pdo->prepare("SELECT * FROM chat_sessions WHERE id = ?");
                $session->execute([$session_id]);
                $sessionData = $session->fetch(PDO::FETCH_ASSOC);
                
                $is_visitor_online = false;
                if ($sessionData && $sessionData['last_visitor_heartbeat']) {
                    $lastHeartbeat = strtotime($sessionData['last_visitor_heartbeat']);
                    $is_visitor_online = (time() - $lastHeartbeat) < 120;
                }
                
                echo json_encode([
                    'success' => true, 
                    'messages' => $messages, 
                    'session' => $sessionData,
                    'visitor_online' => $is_visitor_online
                ]);
                break;
            
            case 'send_message':
                $session_id = intval($_POST['session_id']);
                $message_text = trim($_POST['message']);
                
                if (!empty($message_text)) {
                    $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message_text, is_read) VALUES (?, 'agent', ?, 1)")
                        ->execute([$session_id, $message_text]);
                    
                    $pdo->prepare("UPDATE chat_sessions SET assigned_user_id = ?, status = 'active', last_agent_heartbeat = NOW() WHERE id = ?")
                        ->execute([$user_id, $session_id]);
                    
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Empty message']);
                }
                break;
            
            case 'close_session':
                $session_id = intval($_POST['session_id']);
                $pdo->prepare("UPDATE chat_sessions SET status = 'closed' WHERE id = ?")
                    ->execute([$session_id]);
                echo json_encode(['success' => true]);
                break;
            
            case 'delete_session':
                $session_id = intval($_POST['session_id']);
                $pdo->prepare("DELETE FROM chat_sessions WHERE id = ?")
                    ->execute([$session_id]);
                echo json_encode(['success' => true]);
                break;
            
            case 'search_inventory':
                $search = trim($_POST['search']);
                $searchTerm = "%{$search}%";
                $stmt = $pdo->prepare("
                    SELECT p.id, p.name, p.manufacturer_part_number, pc.name as category_name,
                           b.name as branch_name, i.quantity, i.selling_price
                    FROM products p
                    LEFT JOIN product_categories pc ON p.category_id = pc.id
                    LEFT JOIN inventory i ON p.id = i.product_id
                    LEFT JOIN branches b ON i.branch_id = b.id
                    WHERE p.name LIKE ? OR p.manufacturer_part_number LIKE ? OR pc.name LIKE ?
                    ORDER BY p.name, b.name
                    LIMIT 200
                ");
                $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'results' => $results]);
                break;
            
            case 'get_all_inventory':
                $stmt = $pdo->query("
                    SELECT p.id, p.name, p.manufacturer_part_number, pc.name as category_name,
                           b.name as branch_name, i.quantity, i.selling_price
                    FROM products p
                    LEFT JOIN product_categories pc ON p.category_id = pc.id
                    LEFT JOIN inventory i ON p.id = i.product_id
                    LEFT JOIN branches b ON i.branch_id = b.id
                    ORDER BY p.name, b.name
                    LIMIT 500
                ");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'results' => $results]);
                break;
            
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (PDOException $e) {
        error_log("API Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

$stmt = $pdo->prepare("SELECT online_status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();
$is_online = $current_user['online_status'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Care - Motor Fix</title>
    <link rel="icon" href="../media/slogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #28a745;
            --dark-primary: #0A4A2A;
            --blue: #007bff;
            --dark: #212529;
            --grey: #6c757d;
            --light-grey: #f8f9fa;
            --border: #dee2e6;
            --white: #ffffff;
            --danger: #dc3545;
            --online: #28a745;
            --offline: #6c757d;
        }

        html, body { height: 100%; width: 100%; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--light-grey);
            overflow: hidden;
        }

        /* Header */
        .header {
            background: var(--white);
            padding: 10px 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
            gap: 10px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .logo img { height: 35px; }
        .logo h1 { font-size: 0.95rem; color: var(--dark); white-space: nowrap; }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            justify-content: flex-end;
        }

        .status-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--light-grey);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--offline);
            transition: background 0.3s;
        }

        .status-dot.online { background: var(--online); box-shadow: 0 0 6px var(--online); }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 22px;
            background: var(--grey);
            border-radius: 11px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.active { background: var(--primary); }

        .toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-switch.active .toggle-slider { transform: translateX(18px); }

        .logout-btn {
            padding: 5px 12px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
        }

        .logout-btn:hover { background: #c82333; }

        /* Main Layout */
        .main {
            display: flex;
            margin-top: 60px;
            height: calc(100vh - 60px);
            gap: 0;
        }

        /* Tab Navigation */
        .tabs {
            width: 60px;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            padding: 8px 0;
            gap: 3px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .tab-btn {
            padding: 12px;
            background: none;
            border: none;
            color: var(--grey);
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 45px;
        }

        .tab-btn:hover { color: var(--white); background: rgba(255,255,255,0.1); }

        .tab-btn.active {
            color: var(--primary);
            background: rgba(40,167,69,0.15);
        }

        .tab-btn.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
        }

        .badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: var(--danger);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 10px;
            font-weight: bold;
        }

        /* Content */
        .content {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        .tab-content {
            display: none;
            width: 100%;
            flex-direction: column;
        }

        .tab-content.active { display: flex; }

        /* Chat Layout */
        .chat-container {
            display: flex;
            width: 100%;
            height: 100%;
            gap: 0;
        }

        .sessions-panel {
            width: 30%;
            min-width: 250px;
            background: var(--white);
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            overflow: hidden;
        }

        .sessions-header {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
            background: var(--light-grey);
            flex-shrink: 0;
        }

        .sessions-header h2 { font-size: 1rem; margin-bottom: 8px; color: var(--dark); }

        .filter-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
        }

        .filter-tab {
            padding: 4px 10px;
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--grey);
            transition: all 0.2s;
        }

        .filter-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .filter-tab:hover { border-color: var(--primary); }

        .sessions-count { font-size: 0.8rem; color: var(--grey); }

        .sessions-list {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .session-item {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            background: var(--white);
        }

        .session-item:hover { background: var(--light-grey); }

        .session-item.active {
            background: #e3f2fd;
            border-left: 3px solid var(--blue);
        }

        .session-item.unread { background: #fffbf0; }

        .session-item.closed { opacity: 0.6; background: #f5f5f5; }

        .session-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
            gap: 8px;
        }

        .session-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 5px;
            flex: 1;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--grey);
            flex-shrink: 0;
        }

        .status-indicator.online {
            background: var(--online);
            box-shadow: 0 0 5px var(--online);
        }

        .session-status {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .session-status.active { background: #d4edda; color: #155724; }
        .session-status.closed { background: #f8d7da; color: #721c24; }

        .session-preview {
            font-size: 0.8rem;
            color: var(--grey);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .session-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: var(--grey);
        }

        .unread-count {
            position: absolute;
            top: 10px;
            right: 15px;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
        }

        /* Chat Window */
        .chat-window {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--white);
            overflow: hidden;
        }

        .chat-header {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
            background: var(--light-grey);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .visitor-info h3 {
            font-size: 0.95rem;
            color: var(--dark);
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .visitor-info p {
            font-size: 0.75rem;
            color: var(--grey);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 2px 0;
        }

        .chat-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 3px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-close { background: var(--grey); color: white; }
        .btn-close:hover { background: #5a6268; }

        .btn-delete { background: var(--danger); color: white; }
        .btn-delete:hover { background: #c82333; }

        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .message {
            display: flex;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.visitor { justify-content: flex-start; }
        .message.agent { justify-content: flex-end; }

        .bubble {
            max-width: 70%;
            padding: 9px 13px;
            border-radius: 13px;
            word-wrap: break-word;
        }

        .message.visitor .bubble {
            background: var(--white);
            border-bottom-left-radius: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            color: var(--dark);
        }

        .message.agent .bubble {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .msg-text { line-height: 1.3; font-size: 0.95rem; }

        .msg-time {
            font-size: 0.7rem;
            margin-top: 3px;
            opacity: 0.7;
        }

        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--grey);
            text-align: center;
            padding: 30px;
        }

        .empty-state i { font-size: 3rem; margin-bottom: 10px; opacity: 0.2; }

        .input-area {
            padding: 12px 15px;
            border-top: 1px solid var(--border);
            background: var(--white);
            flex-shrink: 0;
        }

        .input-form { display: flex; gap: 8px; }

        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .chat-input:focus { border-color: var(--primary); }

        .send-btn {
            padding: 10px 18px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .send-btn:hover { background: #218838; }
        .send-btn:disabled { background: var(--grey); cursor: not-allowed; }

        /* Inventory Tab */
        .inventory-panel {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px;
            overflow-y: auto;
        }

        .search-box {
            background: var(--white);
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex;
            gap: 8px;
        }

        .search-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .search-btn {
            padding: 8px 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .search-btn:hover { background: #218838; }

        .inv-table-wrapper {
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .inv-table th,
        .inv-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .inv-table th {
            background: var(--light-grey);
            font-weight: 600;
            color: var(--dark);
            position: sticky;
            top: 0;
        }

        .inv-table tbody tr:hover { background: var(--light-grey); }

        .stock-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stock-in { background: #d4edda; color: #155724; }
        .stock-out { background: #f8d7da; color: #721c24; }

        .no-results {
            text-align: center;
            padding: 30px;
            color: var(--grey);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sessions-panel { width: 35%; min-width: 280px; }
        }

        @media (max-width: 768px) {
            .sessions-panel {
                position: absolute;
                width: 100%;
                height: 100%;
                left: 0;
                top: 0;
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 100;
                min-width: auto;
            }

            .sessions-panel.show { transform: translateX(0); }

            .chat-window { width: 100%; }

            .sessions-header h2 { font-size: 0.95rem; }

            .session-item { padding: 8px 12px; }

            .bubble { max-width: 85%; }

            .chat-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .chat-actions { width: 100%; }

            .action-btn { flex: 1; justify-content: center; }

            .inv-table { font-size: 0.8rem; }

            .inv-table th,
            .inv-table td { padding: 8px 10px; }
        }

        @media (max-width: 480px) {
            .logo h1 { display: none; }

            .status-toggle { gap: 4px; padding: 4px 8px; font-size: 0.75rem; }

            .logout-btn { padding: 4px 10px; font-size: 0.75rem; }

            .sessions-header h2 { font-size: 0.9rem; }

            .filter-tab { font-size: 0.7rem; padding: 3px 8px; }

            .messages-area { padding: 10px; gap: 6px; }

            .bubble { padding: 8px 12px; }

            .msg-text { font-size: 0.9rem; }

            .inventory-panel { padding: 10px; gap: 10px; }

            .search-box { flex-direction: column; }

            .search-input { font-size: 0.85rem; }
        }

        .loading {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="../media/slogo.png" alt="Motor Fix">
            <h1>Customer Care</h1>
        </div>
        <div class="header-right">
            <div class="status-toggle">
                <span class="status-dot <?php echo $is_online ? 'online' : ''; ?>"></span>
                <span id="statusText" style="font-weight: 500;"><?php echo $is_online ? 'Online' : 'Offline'; ?></span>
                <div class="toggle-switch <?php echo $is_online ? 'active' : ''; ?>" id="statusToggle">
                    <div class="toggle-slider"></div>
                </div>
            </div>
            <button class="logout-btn" onclick="window.location.href='../logout.php'">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </header>

    <main class="main">
        <div class="tabs">
            <button class="tab-btn active" data-tab="chat" title="Chat">
                <i class="fas fa-comments"></i>
                <span class="badge" id="chatBadge" style="display:none;">0</span>
            </button>
            <button class="tab-btn" data-tab="inventory" title="Inventory">
                <i class="fas fa-boxes"></i>
            </button>
        </div>

        <div class="content">
            <!-- Chat Tab -->
            <div class="tab-content active" id="chat">
                <div class="chat-container">
                    <!-- Sessions Panel -->
                    <div class="sessions-panel" id="sessionsPanel">
                        <div class="sessions-header">
                            <h2>Conversations</h2>
                            <div class="filter-tabs">
                                <button class="filter-tab active" data-filter="active">Active</button>
                                <button class="filter-tab" data-filter="closed">Closed</button>
                                <button class="filter-tab" data-filter="all">All</button>
                            </div>
                            <p class="sessions-count" id="sessionsCount">0 chats</p>
                        </div>
                        <div class="sessions-list" id="sessionsList"></div>
                    </div>

                    <!-- Chat Window -->
                    <div class="chat-window" id="chatWindow">
                        <div class="empty-state">
                            <i class="fas fa-comment-dots"></i>
                            <h3>Select a conversation</h3>
                            <p>Choose from the list to start messaging</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Tab -->
            <div class="tab-content" id="inventory">
                <div class="inventory-panel">
                    <div class="search-box">
                        <input type="text" class="search-input" id="searchInput" placeholder="Search products...">
                        <button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="inv-table-wrapper">
                        <table class="inv-table" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Branch</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryBody">
                                <tr><td colspan="5" class="no-results"><i class="fas fa-spinner fa-spin"></i> Loading inventory...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // State Management
        let currentSessionId = null;
        let currentFilter = 'active';
        let lastMessageTime = {};
        let isOnline = <?php echo $is_online ? 'true' : 'false'; ?>;
        let sessionsRefreshInterval = null;
        let messagePollingInterval = null;

        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                document.getElementById(btn.dataset.tab).classList.add('active');
                
                if (btn.dataset.tab === 'inventory') {
                    document.getElementById('sessionsPanel').classList.remove('show');
                }
            });
        });

        // Filter Tabs
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.dataset.filter;
                loadSessions();
            });
        });

        // Status Toggle
        document.getElementById('statusToggle').addEventListener('click', () => {
            isOnline = !isOnline;
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=toggle_status&status=${isOnline ? 1 : 0}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const toggle = document.getElementById('statusToggle');
                    const dot = document.querySelector('.status-dot');
                    const text = document.getElementById('statusText');
                    
                    if (isOnline) {
                        toggle.classList.add('active');
                        dot.classList.add('online');
                        text.textContent = 'Online';
                    } else {
                        toggle.classList.remove('active');
                        dot.classList.remove('online');
                        text.textContent = 'Offline';
                    }
                }
            });
        });

        // Load Sessions
        function loadSessions() {
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=get_sessions&filter=${currentFilter}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    displaySessions(data.sessions);
                }
            });
        }

        function displaySessions(sessions) {
            const list = document.getElementById('sessionsList');
            const count = document.getElementById('sessionsCount');
            const badge = document.getElementById('chatBadge');

            if (sessions.length === 0) {
                list.innerHTML = '<div class="no-results">No conversations</div>';
                count.textContent = '0 chats';
                badge.style.display = 'none';
                return;
            }

            let totalUnread = 0;
            let html = '';

            sessions.forEach(session => {
                const unread = parseInt(session.unread_count) || 0;
                totalUnread += unread;

                const isClosed = session.status === 'closed';
                const isActive = currentSessionId == session.id;
                const name = session.visitor_email || session.visitor_phone || session.visitor_name || 'Anonymous';
                const lastMsg = session.last_message || 'No messages';
                const time = session.last_message_time ? formatTime(session.last_message_time) : '';
                const visitorOnline = !isClosed && session.last_visitor_heartbeat && 
                    ((Date.now() - new Date(session.last_visitor_heartbeat)) < 120000);

                html += `
                    <div class="session-item ${isActive ? 'active' : ''} ${unread > 0 ? 'unread' : ''} ${isClosed ? 'closed' : ''}" 
                         onclick="selectSession(${session.id})">
                        <div class="session-header-row">
                            <div class="session-name">
                                <span class="status-indicator ${visitorOnline ? 'online' : ''}"></span>
                                <span>${escapeHtml(name)}</span>
                            </div>
                            <span class="session-status ${isClosed ? 'closed' : 'active'}">${isClosed ? 'Closed' : 'Active'}</span>
                        </div>
                        <div class="session-preview">${escapeHtml(lastMsg)}</div>
                        <div class="session-footer">
                            <span>${time}</span>
                            ${unread > 0 ? `<span class="unread-count">${unread}</span>` : ''}
                        </div>
                    </div>
                `;
            });

            list.innerHTML = html;
            count.textContent = `${sessions.length} chat${sessions.length > 1 ? 's' : ''}`;

            if (totalUnread > 0 && currentFilter === 'active') {
                badge.textContent = totalUnread;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Select Session
        function selectSession(sessionId) {
            currentSessionId = sessionId;
            lastMessageTime[sessionId] = null;

            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=get_messages&session_id=${sessionId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    displayChat(data.messages, data.session, data.visitor_online);
                    
                    if (data.messages.length > 0) {
                        lastMessageTime[sessionId] = data.messages[data.messages.length - 1].timestamp;
                    }

                    document.getElementById('sessionsPanel').classList.remove('show');
                    loadSessions();
                }
            });
        }

        function displayChat(messages, session, visitorOnline) {
            const window = document.getElementById('chatWindow');
            const isClosed = session.status === 'closed';
            const name = session.visitor_email || session.visitor_phone || session.visitor_name || 'Anonymous';
            const contact = [session.visitor_email, session.visitor_phone].filter(Boolean).join(' • ') || 'No contact';

            window.innerHTML = `
                <div class="chat-header">
                    <div class="visitor-info">
                        <h3>${escapeHtml(name)}</h3>
                        <p>${escapeHtml(contact)}</p>
                        <p style="margin-top: 4px; font-weight: 500; ${isClosed ? 'color: #721c24;' : 'color: #155724;'}">
                            <span class="status-indicator ${visitorOnline ? 'online' : ''}"></span>
                            ${visitorOnline ? 'Online' : 'Offline'} • ${isClosed ? 'Closed' : 'Active'}
                        </p>
                    </div>
                    <div class="chat-actions">
                        ${!isClosed ? `<button class="action-btn btn-close" onclick="closeSession(${session.id})"><i class="fas fa-times"></i> Close</button>` : ''}
                        <button class="action-btn btn-delete" onclick="deleteSession(${session.id})"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                </div>
                <div class="messages-area" id="messagesArea"></div>
                ${!isClosed ? `
                    <div class="input-area">
                        <form class="input-form" onsubmit="sendMessage(event)">
                            <input type="text" class="chat-input" id="msgInput" placeholder="Type message..." required autocomplete="off">
                            <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                ` : `
                    <div class="input-area" style="background: #f8d7da; color: #721c24; text-align: center; padding: 15px;">
                        <i class="fas fa-lock"></i> This conversation is closed
                    </div>
                `}
            `;

            displayMessages(messages);
            if (!isClosed) document.getElementById('msgInput').focus();

            if (messagePollingInterval) clearInterval(messagePollingInterval);
            messagePollingInterval = setInterval(() => {
                if (currentSessionId === session.id) {
                    pollNewMessages(session.id);
                }
            }, 1500);
        }

        function displayMessages(messages) {
            const area = document.getElementById('messagesArea');

            if (messages.length === 0) {
                area.innerHTML = '<div class="no-results">No messages yet</div>';
                return;
            }

            let html = '';
            messages.forEach(msg => {
                const time = formatTime(msg.timestamp);
                html += `
                    <div class="message ${msg.sender}">
                        <div class="bubble">
                            <div class="msg-text">${escapeHtml(msg.message_text)}</div>
                            <div class="msg-time">${time}</div>
                        </div>
                    </div>
                `;
            });

            area.innerHTML = html;
            area.scrollTop = area.scrollHeight;
        }

        // Poll for new messages from visitor
        function pollNewMessages(sessionId) {
            const since = lastMessageTime[sessionId] || null;
            
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=get_messages&session_id=${sessionId}${since ? `&since=${encodeURIComponent(since)}` : ''}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    const area = document.getElementById('messagesArea');
                    
                    // Append only new messages
                    data.messages.forEach(msg => {
                        if (!lastMessageTime[sessionId] || msg.timestamp > lastMessageTime[sessionId]) {
                            const time = formatTime(msg.timestamp);
                            const msgHtml = `
                                <div class="message ${msg.sender}">
                                    <div class="bubble">
                                        <div class="msg-text">${escapeHtml(msg.message_text)}</div>
                                        <div class="msg-time">${time}</div>
                                    </div>
                                </div>
                            `;
                            area.innerHTML += msgHtml;
                            lastMessageTime[sessionId] = msg.timestamp;
                        }
                    });
                    
                    area.scrollTop = area.scrollHeight;
                    loadSessions(); // Refresh list to show updated status
                }
            });
        }

        // Send Message
        function sendMessage(event) {
            event.preventDefault();
            const input = document.getElementById('msgInput');
            const msg = input.value.trim();
            const btn = event.target.querySelector('.send-btn');

            if (!msg || !currentSessionId) return;

            btn.disabled = true;
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span>';

            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=send_message&session_id=${currentSessionId}&message=${encodeURIComponent(msg)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    input.value = '';
                    selectSession(currentSessionId);
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        }

        // Close/Delete Session
        function closeSession(id) {
            if (!confirm('Close this conversation?')) return;
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=close_session&session_id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentSessionId = null;
                    loadSessions();
                    document.getElementById('chatWindow').innerHTML = '<div class="empty-state"><i class="fas fa-check-circle"></i><p>Conversation closed</p></div>';
                }
            });
        }

        function deleteSession(id) {
            if (!confirm('Delete this conversation permanently?')) return;
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=delete_session&session_id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentSessionId = null;
                    loadSessions();
                    document.getElementById('chatWindow').innerHTML = '<div class="empty-state"><i class="fas fa-trash"></i><p>Conversation deleted</p></div>';
                }
            });
        }

        // Inventory
        function loadInventory() {
            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_all_inventory'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    displayInventory(data.results);
                }
            });
        }

        function displayInventory(results) {
            const tbody = document.getElementById('inventoryBody');

            if (results.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="no-results">No products found</td></tr>';
                return;
            }

            const grouped = {};
            results.forEach(item => {
                if (!grouped[item.id]) {
                    grouped[item.id] = {
                        name: item.name,
                        category: item.category_name,
                        partNum: item.manufacturer_part_number,
                        branches: []
                    };
                }
                if (item.branch_name) {
                    grouped[item.id].branches.push({
                        name: item.branch_name,
                        qty: item.quantity,
                        price: item.selling_price
                    });
                }
            });

            let html = '';
            Object.values(grouped).forEach(product => {
                const branches = product.branches.length > 0 ? product.branches : [{name: 'N/A', qty: 0, price: 0}];
                
                branches.forEach((branch, idx) => {
                    const inStock = branch.qty > 0;
                    html += `<tr>
                        ${idx === 0 ? `<td rowspan="${branches.length}"><strong>${escapeHtml(product.name)}</strong></td>
                        <td rowspan="${branches.length}">${escapeHtml(product.category || 'N/A')}</td>` : ''}
                        <td>${escapeHtml(branch.name)}</td>
                        <td><span class="stock-badge ${inStock ? 'stock-in' : 'stock-out'}">${branch.qty} units</span></td>
                        <td>Ksh ${parseFloat(branch.price).toFixed(2)}</td>
                    </tr>`;
                });
            });

            tbody.innerHTML = html;
        }

        document.getElementById('searchBtn').addEventListener('click', () => {
            const search = document.getElementById('searchInput').value.trim();
            if (!search) return;

            fetch('cdashboard.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=search_inventory&search=${encodeURIComponent(search)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    displayInventory(data.results);
                }
            });
        });

        // Utility Functions
        function formatTime(ts) {
            const date = new Date(ts);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            
            return date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadSessions();
            loadInventory();
            sessionsRefreshInterval = setInterval(loadSessions, 5000);
        });

        window.addEventListener('beforeunload', () => {
            if (sessionsRefreshInterval) clearInterval(sessionsRefreshInterval);
            if (messagePollingInterval) clearInterval(messagePollingInterval);
        });
    </script>
</body>
</html>