<?php
/**
 * Motor Fix - Admin Dashboard Header (Mobile-Optimized Include Component)
 *
 * PROFESSIONAL FEATURES:
 * - Added viewport meta tag for proper mobile rendering
 * - Made logout button visible on mobile
 * - Uses unique CSS prefix (mfa-) to prevent conflicts
 * - No global font styling to avoid conflicts
 * - Session security check for forced logouts
 * - Branch-specific display
 *
 * @version 3.0
 * @date Sunday, October 19, 2025
 */

// --- Smart Error Reporting ---
require_once __DIR__ . '/../../db.php';
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// --- Session & Security ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- SECURITY CHECK 1: Ensure user is a logged-in 'admin' ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// --- SECURITY CHECK 2: Verify this session is still active in the database ---
// This prevents access if a super admin has forced a logout.
try {
    $session_check_stmt = $pdo->prepare("SELECT is_active FROM logged_in_devices WHERE session_id = ? AND user_id = ?");
    $session_check_stmt->execute([session_id(), $_SESSION['user_id']]);
    $session_status = $session_check_stmt->fetchColumn();

    if ($session_status != 1) { // If session is not active (or doesn't exist)
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=forced_logout");
        exit();
    }
} catch (PDOException $e) {
    error_log("Session validation failed: " . $e->getMessage());
    die("A critical security error occurred. Please try logging in again.");
}

// --- Fetch Branch Info for the Logged-in Admin ---
// Store it in the session to avoid re-querying on every page load.
if (!isset($_SESSION['branch_id']) || !isset($_SESSION['branch_name'])) {
    try {
        $stmt = $pdo->prepare("SELECT b.id, b.name FROM users u JOIN branches b ON u.branch_id = b.id WHERE u.id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $branch = $stmt->fetch();
        
        if ($branch) {
            $_SESSION['branch_id'] = $branch['id'];
            $_SESSION['branch_name'] = $branch['name'];
        } else {
            session_destroy();
            header("Location: ../login.php?error=no_branch");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch admin branch info: " . $e->getMessage());
        die("A critical error occurred while loading your branch data.");
    }
}

/**
 * Helper function to apply an 'active' class to the current page's navigation link.
 */
if (!function_exists('is_active_page')) {
    function is_active_page($page_name) {
        if (basename($_SERVER['PHP_SELF']) == $page_name) {
            return 'mfa-nav-active';
        }
        return '';
    }
}
?>

<!-- CRITICAL: Viewport Meta Tag for Mobile Rendering -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

<!-- Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Styles for the Motor Fix Admin Header Component (Unique Prefix: mfa-) -->
<style>
    /* Scoped CSS Variables with unique prefix */
    :root {
        --mfa-green: #28a745; 
        --mfa-blue: #007bff; 
        --mfa-dark: #343a40;
        --mfa-grey-light: #f8f9fa; 
        --mfa-grey-med: #e9ecef; 
        --mfa-grey-border: #dee2e6;
        --mfa-white: #ffffff; 
        --mfa-text: #212529; 
        --mfa-text-light: #6c757d;
        --mfa-shadow: rgba(0, 0, 0, 0.08);
        --mfa-red: #dc3545;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--mfx-grey-light, #f8f9fa); /* Set a consistent admin bg */
        color: var(--mfx-text, #212529);
        margin: 0; /* Ensure no default margin */
    }
    
    /* Header container - NO font-family to avoid conflicts */
    .mfa-header-main {
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%;
        background: var(--mfa-white); 
        box-shadow: 0 2px 10px var(--mfa-shadow);
        z-index: 1000; 
        border-bottom: 1px solid var(--mfa-grey-border);
    }
    
    /* Top Row: Logo, Desktop Nav, and Logout */
    .mfa-header-top-row {
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        padding: 10px 12px; 
        max-width: 1600px; 
        margin: 0 auto; 
        min-height: 60px;
    }
    
    /* Logo Section */
    .mfa-logo-section { 
        display: flex; 
        align-items: center; 
        text-decoration: none; 
        gap: 10px;
    }
    
    .mfa-logo-icon img { 
        height: 38px; 
        width: auto; 
    }
    
    .mfa-logo-text h1 { 
        font-size: 1.1rem; 
        color: var(--mfa-dark); 
        margin: 0; 
        font-weight: 700; 
        line-height: 1.2;
    }
    
    .mfa-logo-text p { 
        font-size: 0.7rem; 
        color: var(--mfa-text-light); 
        margin: 2px 0 0 0; 
        line-height: 1;
    }
    
    /* User Section - VISIBLE on all screens */
    .mfa-user-section { 
        display: flex; 
        align-items: center; 
        gap: 8px;
    }
    
    /* Welcome text - hidden on mobile */
    .mfa-welcome-text {
        display: none;
        font-size: 0.85rem;
        color: var(--mfa-text-light);
        font-weight: 500;
        text-align: right;
        line-height: 1.3;
    }
    
    /* Logout Button - ALWAYS VISIBLE */
    .mfa-logout-btn {
        display: flex; 
        align-items: center; 
        gap: 6px; 
        padding: 8px 12px;
        background-color: var(--mfa-grey-med); 
        border: 1px solid var(--mfa-grey-border);
        border-radius: 8px; 
        text-decoration: none; 
        color: var(--mfa-dark);
        font-weight: 600; 
        font-size: 0.85rem; 
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .mfa-logout-btn:hover { 
        background-color: #d3d9df; 
    }
    
    .mfa-logout-btn i { 
        color: var(--mfa-red); 
        font-size: 1rem;
    }
    
    /* Desktop Navigation - Hidden on mobile */
    .mfa-desktop-nav { 
        display: none; 
        flex: 1; 
        justify-content: center; 
        margin: 0 15px; 
    }
    
    .mfa-desktop-nav-container { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .mfa-desktop-nav-item {
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        text-decoration: none;
        padding: 8px 12px; 
        border-radius: 10px; 
        transition: background-color 0.2s ease;
    }
    
    .mfa-desktop-nav-item:hover { 
        background-color: var(--mfa-grey-light); 
    }
    
    .mfa-desktop-nav-icon { 
        font-size: 1.2rem; 
        color: var(--mfa-text-light); 
        margin-bottom: 3px; 
    }
    
    .mfa-desktop-nav-label { 
        font-size: 0.75rem; 
        color: var(--mfa-text); 
        font-weight: 500; 
    }
    
    .mfa-desktop-nav-item.mfa-nav-active .mfa-desktop-nav-icon,
    .mfa-desktop-nav-item.mfa-nav-active .mfa-desktop-nav-label { 
        color: var(--mfa-blue); 
        font-weight: 600; 
    }
    
    /* Bottom Row: Mobile Navigation */
    .mfa-header-bottom-row { 
        border-top: 1px solid var(--mfa-grey-border); 
        padding: 8px 8px 10px; 
    }
    
    .mfa-mobile-nav-container { 
        display: flex; 
        justify-content: space-around; 
        align-items: flex-start; 
    }
    
    .mfa-mobile-nav-item {
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        text-decoration: none;
        padding: 6px 4px; 
        border-radius: 12px; 
        transition: all 0.2s ease; 
        flex: 1;
        max-width: 80px;
    }
    
    .mfa-mobile-nav-item:hover { 
        background-color: var(--mfa-grey-light); 
    }
    
    .mfa-mobile-nav-icon { 
        font-size: 1.4rem; 
        color: var(--mfa-text-light); 
        margin-bottom: 4px; 
    }
    
    .mfa-mobile-nav-label { 
        font-size: 0.7rem; 
        color: var(--mfa-text); 
        font-weight: 500; 
        text-align: center; 
        line-height: 1.2; 
    }
    
    .mfa-mobile-nav-item.mfa-nav-active .mfa-mobile-nav-icon,
    .mfa-mobile-nav-item.mfa-nav-active .mfa-mobile-nav-label { 
        color: var(--mfa-blue); 
        font-weight: 600; 
    }
    
    .mfa-mobile-nav-item.mfa-nav-active { 
        background: rgba(0, 123, 255, 0.08); 
    }

    /* Very small screens - hide logo text and branch name */
    @media (max-width: 360px) { 
        .mfa-logo-text { 
            display: none; 
        }
        .mfa-logout-btn {
            padding: 8px 10px;
            font-size: 0.8rem;
        }
    }
    
    /* Tablet and Desktop - Show desktop nav, hide mobile nav */
    @media (min-width: 768px) {
        .mfa-header-bottom-row { 
            display: none; 
        }
        .mfa-desktop-nav { 
            display: flex; 
        }
        .mfa-welcome-text {
            display: block;
        }
        .mfa-user-section {
            gap: 12px;
        }
        .mfa-header-top-row { 
            padding: 12px 25px; 
            min-height: 65px; 
        }
        .mfa-logo-icon img { 
            height: 42px; 
        }
        .mfa-logo-text h1 { 
            font-size: 1.3rem; 
        }
        .mfa-logo-text p { 
            font-size: 0.8rem; 
        }
        .mfa-logout-btn {
            font-size: 0.9rem;
            padding: 9px 14px;
        }
    }
    
    /* Large Desktop */
    @media (min-width: 1200px) {
        .mfa-desktop-nav-container { 
            gap: 20px; 
        }
        .mfa-header-top-row { 
            padding: 12px 35px; 
        }
    }
</style>

<!-- Motor Fix Admin Header HTML -->
<header class="mfa-header-main" id="mfa-header-main">
    <!-- Top Row: Contains Logo, Desktop Nav, and Logout -->
    <div class="mfa-header-top-row">
        <a href="../adashboard.php" class="mfa-logo-section">
            <div class="mfa-logo-icon"><img src="../../media/slogo.png" alt="Motor Fix Logo"></div>
            <div class="mfa-logo-text">
                <h1>Admin Panel</h1>
                <p><?php echo htmlspecialchars($_SESSION['branch_name']); ?></p>
            </div>
        </a>
        
        <!-- Desktop Navigation (Hidden on mobile) -->
        <nav class="mfa-desktop-nav">
            <div class="mfa-desktop-nav-container">
                <a href="../adashboard.php" class="mfa-desktop-nav-item <?php echo is_active_page('adashboard.php'); ?>" title="Dashboard">
                    <i class="mfa-desktop-nav-icon fas fa-tachometer-alt"></i>
                    <span class="mfa-desktop-nav-label">Dashboard</span>
                </a>
                <a href="../manage_inventory.php" class="mfa-desktop-nav-item <?php echo is_active_page('manage_inventory.php'); ?>" title="Manage Inventory">
                    <i class="mfa-desktop-nav-icon fas fa-boxes"></i>
                    <span class="mfa-desktop-nav-label">Inventory</span>
                </a>
                <a href="../record_sale.php" class="mfa-desktop-nav-item <?php echo is_active_page('record_sale.php'); ?>" title="Record Sale">
                    <i class="mfa-desktop-nav-icon fas fa-cash-register"></i>
                    <span class="mfa-desktop-nav-label">Record Sale</span>
                </a>
                <a href="../record_expense.php" class="mfa-desktop-nav-item <?php echo is_active_page('record_expense.php'); ?>" title="Record Expense">
                    <i class="mfa-desktop-nav-icon fas fa-receipt"></i>
                    <span class="mfa-desktop-nav-label">Expenses</span>
                </a>
                <a href="../branch_reports.php" class="mfa-desktop-nav-item <?php echo is_active_page('branch_reports.php'); ?>" title="Branch Reports">
                    <i class="mfa-desktop-nav-icon fas fa-chart-bar"></i>
                    <span class="mfa-desktop-nav-label">Reports</span>
                </a>
            </div>
        </nav>
        
        <!-- User Section & Logout Button (ALWAYS VISIBLE) -->
        <div class="mfa-user-section">
            <div class="mfa-welcome-text">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?><br>
                <span>(<?php echo htmlspecialchars($_SESSION['branch_name']); ?>)</span>
            </div>
            <a href="../logout.php" class="mfa-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Bottom Row: Mobile Navigation (Hidden on Desktop) -->
    <div class="mfa-header-bottom-row">
        <nav class="mfa-mobile-nav-container">
            <a href="../adashboard.php" class="mfa-mobile-nav-item <?php echo is_active_page('adashboard.php'); ?>">
                <i class="mfa-mobile-nav-icon fas fa-tachometer-alt"></i>
                <span class="mfa-mobile-nav-label">Dashboard</span>
            </a>
            <a href="../manage_inventory.php" class="mfa-mobile-nav-item <?php echo is_active_page('manage_inventory.php'); ?>">
                <i class="mfa-mobile-nav-icon fas fa-boxes"></i>
                <span class="mfa-mobile-nav-label">Inventory</span>
            </a>
            <a href="../record_sale.php" class="mfa-mobile-nav-item <?php echo is_active_page('record_sale.php'); ?>">
                <i class="mfa-mobile-nav-icon fas fa-cash-register"></i>
                <span class="mfa-mobile-nav-label">Sales</span>
            </a>
            <a href="../record_expense.php" class="mfa-mobile-nav-item <?php echo is_active_page('record_expense.php'); ?>">
                <i class="mfa-mobile-nav-icon fas fa-receipt"></i>
                <span class="mfa-mobile-nav-label">Expenses</span>
            </a>
            <a href="../branch_reports.php" class="mfa-mobile-nav-item <?php echo is_active_page('branch_reports.php'); ?>">
                <i class="mfa-mobile-nav-icon fas fa-chart-bar"></i>
                <span class="mfa-mobile-nav-label">Reports</span>
            </a>
        </nav>
    </div>
</header>

<!-- Self-contained script to dynamically adjust body padding -->
<script>
    (function() {
        function setBodyPadding() {
            const header = document.getElementById('mfa-header-main');
            if (header) {
                const headerHeight = header.offsetHeight;
                document.body.style.paddingTop = headerHeight + 'px';
            }
        }
        document.addEventListener('DOMContentLoaded', setBodyPadding);
        window.addEventListener('resize', setBodyPadding);
    })();
</script>