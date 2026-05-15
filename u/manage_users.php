<?php
/**
 * Motor Fix - Super Admin User Management (Upgraded)
 *
 * Adds "Delete User" functionality with password confirmation.
 * Corrects the user invitation link for the live domain structure.
 *
 * @version 2.0
 * @date Tuesday, October 14, 2025
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'ic/sheader.php'; // Includes session, db, error reporting

// Initialize variables
$feedback_message = '';
$feedback_type = '';
$users = [];
$active_sessions = [];
$roles = [];
$branches = [];
$current_superadmin_email = $_SESSION['user_email'] ?? ''; // Fetch from session

// Handle session feedback messages
if (isset($_SESSION['feedback_message'])) {
    $feedback_message = $_SESSION['feedback_message'];
    $feedback_type = $_SESSION['feedback_type'] ?? 'error';
    unset($_SESSION['feedback_message'], $_SESSION['feedback_type']);
}

// --- FORM PROCESSING for User Invitation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_user'])) {
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    $branch_id = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;

    if (empty($email) || empty($role_id) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['feedback_message'] = "Invalid input. Please provide a valid email and role.";
        $_SESSION['feedback_type'] = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['feedback_message'] = "A user with this email already exists or is pending setup.";
                $_SESSION['feedback_type'] = 'error';
            } else {
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $pdo->prepare("INSERT INTO password_resets (email, token, purpose, expires_at) VALUES (?, ?, 'setup', ?)")->execute([$email, $token, $expires_at]);
                $pdo->prepare("INSERT INTO users (email, role_id, branch_id, is_active, name) VALUES (?, ?, ?, 0, 'Invited User')")->execute([$email, $role_id, $branch_id]);
                
                // CORRECTED LINK: Points to the subdomain root.
                $setup_link = "https://dash.motorfix.co.ke/setup.php?token=" . $token;
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port = $_ENV['SMTP_PORT'];
                $mail->setFrom('admin@motorfix.co.ke', 'Motor Fix Admin');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Invitation to Join Motor Fix Dashboard';
                $mail->Body = "<p>You have been invited to the Motor Fix Dashboard. Click the link below to set up your account. This link expires in 1 hour.</p><p><a href='{$setup_link}'>Set Up Your Account</a></p>";
                $mail->send();

                $_SESSION['feedback_message'] = "Invitation sent successfully to {$email}.";
                $_SESSION['feedback_type'] = 'success';
            }
        } catch (Exception $e) {
            error_log("User Invite Error: " . $e->getMessage());
            $_SESSION['feedback_message'] = "An error occurred. Could not send invitation.";
            $_SESSION['feedback_type'] = 'error';
        }
    }
    header("Location: manage_users.php");
    exit();
}

// --- DATA FETCHING for Page Display ---
try {
    $users = $pdo->query("SELECT u.id, u.name, u.email, u.is_active, u.online_status, r.role_name, b.name as branch_name FROM users u JOIN roles r ON u.role_id = r.id LEFT JOIN branches b ON u.branch_id = b.id ORDER BY u.id ASC")->fetchAll();
    $active_sessions = $pdo->query("SELECT l.id, l.user_id, l.ip_address, l.user_agent, l.login_time, u.name as user_name, u.email as user_email FROM logged_in_devices l JOIN users u ON l.user_id = u.id WHERE l.is_active = 1 ORDER BY l.login_time DESC")->fetchAll();
    $roles = $pdo->query("SELECT id, role_name FROM roles ORDER BY role_name")->fetchAll();
    $branches = $pdo->query("SELECT id, name FROM branches ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    $feedback_message = "Error fetching page data: " . $e->getMessage();
    $feedback_type = 'error';
}
?>

<main class="mf-main-content">
    <div class="mf-page-title">
        <h1>User & Session Management</h1>
        <p>Create new users, manage existing accounts, and monitor all active login sessions.</p>
    </div>

    <?php if ($feedback_message): ?>
    <div class="mf-feedback-message <?php echo $feedback_type; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
    <?php endif; ?>

    <!-- Invite New User Section -->
    <div class="mf-content-panel">
        <h2><i class="fas fa-user-plus"></i> Invite New User</h2>
        <form action="manage_users.php" method="POST" class="mf-form-grid">
            <div class="form-group"><label for="email">User Email</label><input type="email" id="email" name="email" required></div>
            <div class="form-group"><label for="role_id">Assign Role</label><select id="role_id" name="role_id" required><option value="">Select a Role...</option><?php foreach ($roles as $role): ?><option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $role['role_name']))); ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="branch_id">Assign Branch (for Admins)</label><select id="branch_id" name="branch_id"><option value="">None</option><?php foreach ($branches as $branch): ?><option value="<?php echo $branch['id']; ?>"><?php echo htmlspecialchars($branch['name']); ?></option><?php endforeach; ?></select></div>
            <div class="form-group form-group-full"><button type="submit" name="invite_user" class="mf-button-primary">Send Invitation</button></div>
        </form>
    </div>

    <!-- All Users Section -->
    <div class="mf-content-panel">
        <h2><i class="fas fa-users"></i> All Registered Users</h2>
        <div class="mf-table-responsive">
            <table class="mf-table">
                <thead><tr><th>Name</th><th>Email / Role</th><th>Branch</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($user['email']); ?></strong><br><small><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role_name']))); ?></small></td>
                        <td><?php echo htmlspecialchars($user['branch_name'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($user['is_active']): ?><span class="mf-status-active"><?php echo $user['online_status'] ? '<i class="fas fa-circle"></i> Online' : '<i class="far fa-circle"></i> Offline'; ?></span>
                            <?php else: ?><span class="mf-status-inactive">Pending Activation</span><?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): // Can't delete self ?>
                            <button class="mf-button-danger" onclick="openDeleteModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars(addslashes($user['email']), ENT_QUOTES); ?>')">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Active Sessions Section -->
    <div class="mf-content-panel">
        <h2><i class="fas fa-desktop"></i> Active Login Sessions</h2>
        <div class="mf-table-responsive">
             <table class="mf-table">
                <thead><tr><th>User</th><th>IP Address</th><th>Device / Browser</th><th>Login Time</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (empty($active_sessions)): ?>
                    <tr><td colspan="5">No active sessions found.</td></tr>
                    <?php else: foreach ($active_sessions as $session): ?>
                    <tr <?php if($session['user_id'] == $_SESSION['user_id']) echo 'class="current-user-session"'; ?>>
                        <td><strong><?php echo htmlspecialchars($session['user_name']); ?></strong><br><small><?php echo htmlspecialchars($session['user_email']); ?></small></td>
                        <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
                        <td><small><?php echo htmlspecialchars($session['user_agent']); ?></small></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($session['login_time'])); ?></td>
                        <td>
                            <form action="user_actions.php" method="POST" onsubmit="return confirm('Are you sure you want to force logout this session?');">
                                <input type="hidden" name="action" value="force_logout">
                                <input type="hidden" name="session_id_to_logout" value="<?php echo $session['id']; ?>">
                                <button type="submit" class="mf-button-secondary">Force Logout</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Delete User Confirmation Modal -->
<div id="deleteUserModal" class="mf-modal">
    <div class="mf-modal-content">
        <span class="mf-modal-close" onclick="closeDeleteModal()">&times;</span>
        <h2>Confirm User Deletion</h2>
        <p>To permanently delete the user <strong id="deleteUserEmail"></strong>, please enter your password.</p>
        <p class="mf-modal-warning"><strong>Warning:</strong> This action cannot be undone.</p>
        <form action="user_actions.php" method="POST">
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" id="userIdToDelete" name="user_id_to_delete">
            <div class="form-group">
                <label for="superAdminPassword">Your Password</label>
                <input type="password" id="superAdminPassword" name="super_admin_password" required>
            </div>
            <button type="submit" class="mf-button-danger">Confirm & Delete User</button>
        </form>
    </div>
</div>

<style>
/* General, Form, Table, Status Styles (from previous version) */
.mf-main-content { padding: 20px 30px; max-width: 1800px; margin: 0 auto; } .mf-page-title h1 { font-size: 1.8rem; } .mf-page-title p { font-size: 1rem; color: #6c757d; } .mf-content-panel { background-color: #fff; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; margin-top: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); } .mf-content-panel h2 { margin: 0 0 20px 0; font-size: 1.2rem; } .mf-feedback-message { padding: 15px; border-radius: 8px; margin-bottom: 20px; } .mf-feedback-message.success { background-color: #d4edda; color: #155724; } .mf-feedback-message.error { background-color: #f8d7da; color: #721c24; } .mf-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; } .form-group label { display: block; margin-bottom: 8px; font-weight: 600; } .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 8px; box-sizing: border-box; } .form-group-full { grid-column: 1 / -1; } .mf-button-primary { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; } .mf-button-danger { background-color: #dc3545; color: white; padding: 6px 12px; font-size: 0.8rem; border: none; border-radius: 6px; cursor: pointer; } .mf-button-secondary { background-color: #6c757d; color: white; padding: 6px 12px; font-size: 0.8rem; border: none; border-radius: 6px; cursor: pointer; } .mf-table-responsive { overflow-x: auto; } .mf-table { width: 100%; border-collapse: collapse; } .mf-table th, .mf-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #dee2e6; white-space: nowrap; } .mf-table th { background-color: #f8f9fa; } .mf-table tbody tr:hover { background-color: #f1f3f5; } .current-user-session { background-color: #e6f7ff; } .mf-status-active, .mf-status-inactive { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; } .mf-status-active { background-color: #d4edda; color: #155724; } .mf-status-active .fa-circle { color: #28a745; } .mf-status-inactive { background-color: #fff3cd; color: #856404; }
/* Modal Styles */
.mf-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); } .mf-modal-content { background-color: #fefefe; margin: 15% auto; padding: 30px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 12px; text-align: center; } .mf-modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; } .mf-modal-warning { color: #dc3545; background-color: #f8d7da; padding: 10px; border-radius: 8px; margin: 15px 0; }
</style>

<script>
function openDeleteModal(userId, userEmail) {
    document.getElementById('userIdToDelete').value = userId;
    document.getElementById('deleteUserEmail').innerText = userEmail;
    document.getElementById('deleteUserModal').style.display = 'block';
}
function closeDeleteModal() {
    document.getElementById('deleteUserModal').style.display = 'none';
    document.getElementById('superAdminPassword').value = ''; // Clear password field
}
window.onclick = function(event) {
    if (event.target == document.getElementById('deleteUserModal')) {
        closeDeleteModal();
    }
}
</script>

<?php require_once 'ic/sfooter.php'; ?>

