<?php
/**
 * Motor Fix - Super Admin Dashboard Header (Mobile-Optimized Include Component)
 *
 * CRITICAL FIXES:
 * - Added viewport meta tag for proper mobile rendering
 * - Made logout button visible on mobile
 * - Uses unique CSS prefix (mfx-) to prevent conflicts
 * - No global font styling to avoid conflicts
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
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    header("Location: ../login.php");
    exit();
}

/**
 * Helper function to apply an 'active' class to the current page's navigation link.
 */
if (!function_exists('is_active_page')) {
    function is_active_page($page_name) {
        if (basename($_SERVER['PHP_SELF']) == $page_name) {
            return 'mfx-nav-active';
        }
        return '';
    }
}
?>

<!-- CRITICAL: Viewport Meta Tag for Mobile Rendering -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

<!-- Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Styles for the Motor Fix Header Component (Unique Prefix: mfx-) -->
<style>
    /* Scoped CSS Variables with unique prefix */
    :root {
        --mfx-green: #28a745; 
        --mfx-blue: #007bff; 
        --mfx-dark: #343a40;
        --mfx-grey-light: #f8f9fa; 
        --mfx-grey-med: #e9ecef; 
        --mfx-grey-border: #dee2e6;
        --mfx-white: #ffffff; 
        --mfx-text: #212529; 
        --mfx-text-light: #6c757d;
        --mfx-shadow: rgba(0, 0, 0, 0.08);
        --mfx-red: #dc3545;
    }
    
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--mfx-grey-light, #f8f9fa); /* Set a consistent admin bg */
        color: var(--mfx-text, #212529);
        margin: 0; /* Ensure no default margin */
    }
    
    /* Header container - NO font-family to avoid conflicts */
    .mfx-header-main {
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%;
        background: var(--mfx-white); 
        box-shadow: 0 2px 10px var(--mfx-shadow);
        z-index: 1000; 
        border-bottom: 1px solid var(--mfx-grey-border);
    }
    
    /* Top Row: Logo, Desktop Nav, and Logout */
    .mfx-header-top-row {
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        padding: 10px 12px; 
        max-width: 1600px; 
        margin: 0 auto; 
        min-height: 60px;
    }
    
    /* Logo Section */
    .mfx-logo-section { 
        display: flex; 
        align-items: center; 
        text-decoration: none; 
        gap: 10px;
    }
    
    .mfx-logo-icon img { 
        height: 38px; 
        width: auto; 
    }
    
    .mfx-logo-text h1 { 
        font-size: 1.1rem; 
        color: var(--mfx-dark); 
        margin: 0; 
        font-weight: 700; 
    }
    
    /* User Section - VISIBLE on all screens */
    .mfx-user-section { 
        display: flex; 
        align-items: center; 
    }
    
    /* Logout Button - ALWAYS VISIBLE */
    .mfx-logout-btn {
        display: flex; 
        align-items: center; 
        gap: 6px; 
        padding: 8px 12px;
        background-color: var(--mfx-grey-med); 
        border: 1px solid var(--mfx-grey-border);
        border-radius: 8px; 
        text-decoration: none; 
        color: var(--mfx-dark);
        font-weight: 600; 
        font-size: 0.85rem; 
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .mfx-logout-btn:hover { 
        background-color: #d3d9df; 
    }
    
    .mfx-logout-btn i { 
        color: var(--mfx-red); 
        font-size: 1rem;
    }
    
    /* Desktop Navigation - Hidden on mobile */
    .mfx-desktop-nav { 
        display: none; 
        flex: 1; 
        justify-content: center; 
        margin: 0 15px; 
    }
    
    .mfx-desktop-nav-container { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .mfx-desktop-nav-item {
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        text-decoration: none;
        padding: 8px 12px; 
        border-radius: 10px; 
        transition: background-color 0.2s ease;
    }
    
    .mfx-desktop-nav-item:hover { 
        background-color: var(--mfx-grey-light); 
    }
    
    .mfx-desktop-nav-icon { 
        font-size: 1.2rem; 
        color: var(--mfx-text-light); 
        margin-bottom: 3px; 
    }
    
    .mfx-desktop-nav-label { 
        font-size: 0.75rem; 
        color: var(--mfx-text); 
        font-weight: 500; 
    }
    
    .mfx-desktop-nav-item.mfx-nav-active .mfx-desktop-nav-icon,
    .mfx-desktop-nav-item.mfx-nav-active .mfx-desktop-nav-label { 
        color: var(--mfx-green); 
        font-weight: 600; 
    }
    
    /* Bottom Row: Mobile Navigation */
    .mfx-header-bottom-row { 
        border-top: 1px solid var(--mfx-grey-border); 
        padding: 8px 8px 10px; 
    }
    
    .mfx-mobile-nav-container { 
        display: flex; 
        justify-content: space-around; 
        align-items: flex-start; 
    }
    
    .mfx-mobile-nav-item {
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
    
    .mfx-mobile-nav-item:hover { 
        background-color: var(--mfx-grey-light); 
    }
    
    .mfx-mobile-nav-icon { 
        font-size: 1.4rem; 
        color: var(--mfx-text-light); 
        margin-bottom: 4px; 
    }
    
    .mfx-mobile-nav-label { 
        font-size: 0.7rem; 
        color: var(--mfx-text); 
        font-weight: 500; 
        text-align: center; 
        line-height: 1.2; 
    }
    
    .mfx-mobile-nav-item.mfx-nav-active .mfx-mobile-nav-icon,
    .mfx-mobile-nav-item.mfx-nav-active .mfx-mobile-nav-label { 
        color: var(--mfx-green); 
        font-weight: 600; 
    }
    
    .mfx-mobile-nav-item.mfx-nav-active { 
        background: rgba(40, 167, 69, 0.08); 
    }

    /* Very small screens - hide logo text */
    @media (max-width: 360px) { 
        .mfx-logo-text { 
            display: none; 
        }
        .mfx-logout-btn {
            padding: 8px 10px;
            font-size: 0.8rem;
        }
    }
    
    /* Tablet and Desktop - Show desktop nav, hide mobile nav */
    @media (min-width: 768px) {
        .mfx-header-bottom-row { 
            display: none; 
        }
        .mfx-desktop-nav { 
            display: flex; 
        }
        .mfx-header-top-row { 
            padding: 12px 25px; 
            min-height: 65px; 
        }
        .mfx-logo-icon img { 
            height: 42px; 
        }
        .mfx-logo-text h1 { 
            font-size: 1.3rem; 
        }
        .mfx-logout-btn {
            font-size: 0.9rem;
            padding: 9px 14px;
        }
    }
    
    /* Large Desktop */
    @media (min-width: 1200px) {
        .mfx-desktop-nav-container { 
            gap: 20px; 
        }
        .mfx-header-top-row { 
            padding: 12px 35px; 
        }
    }
</style>

<!-- Motor Fix Header HTML -->
<header class="mfx-header-main" id="mfx-header-main">
    <!-- Top Row: Contains Logo, Desktop Nav, and Logout -->
    <div class="mfx-header-top-row">
        <a href="../sdashboard.php" class="mfx-logo-section">
            <div class="mfx-logo-icon"><img src="../media/slogo.png" alt="Motor Fix Logo"></div>
            <div class="mfx-logo-text"><h1>Super Admin</h1></div>
        </a>
        
        <!-- Desktop Navigation (Hidden on mobile) -->
        <nav class="mfx-desktop-nav">
            <div class="mfx-desktop-nav-container">
                <a href="../sdashboard.php" class="mfx-desktop-nav-item <?php echo is_active_page('sdashboard.php'); ?>" title="Dashboard">
                    <i class="mfx-desktop-nav-icon fas fa-chart-pie"></i>
                    <span class="mfx-desktop-nav-label">Dashboard</span>
                </a>
                <a href="../manage_users.php" class="mfx-desktop-nav-item <?php echo is_active_page('manage_users.php'); ?>" title="Manage Users">
                    <i class="mfx-desktop-nav-icon fas fa-users-cog"></i>
                    <span class="mfx-desktop-nav-label">Users</span>
                </a>
                <a href="../manage_branches.php" class="mfx-desktop-nav-item <?php echo is_active_page('manage_branches.php'); ?>" title="Manage Branches">
                    <i class="mfx-desktop-nav-icon fas fa-store-alt"></i>
                    <span class="mfx-desktop-nav-label">Branches</span>
                </a>
                <a href="../reports.php" class="mfx-desktop-nav-item <?php echo is_active_page('reports.php'); ?>" title="View Reports">
                    <i class="mfx-desktop-nav-icon fas fa-file-invoice-dollar"></i>
                    <span class="mfx-desktop-nav-label">Reports</span>
                </a>
                <a href="../site_settings.php" class="mfx-desktop-nav-item <?php echo is_active_page('site_settings.php'); ?>" title="Site Settings">
                    <i class="mfx-desktop-nav-icon fas fa-cogs"></i>
                    <span class="mfx-desktop-nav-label">Settings</span>
                </a>
            </div>
        </nav>
        
        <!-- Logout Button (ALWAYS VISIBLE) -->
        <div class="mfx-user-section">
            <a href="../logout.php" class="mfx-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Bottom Row: Mobile Navigation (Hidden on Desktop) -->
    <div class="mfx-header-bottom-row">
        <nav class="mfx-mobile-nav-container">
            <a href="../sdashboard.php" class="mfx-mobile-nav-item <?php echo is_active_page('sdashboard.php'); ?>">
                <i class="mfx-mobile-nav-icon fas fa-chart-pie"></i>
                <span class="mfx-mobile-nav-label">Dashboard</span>
            </a>
            <a href="../manage_users.php" class="mfx-mobile-nav-item <?php echo is_active_page('manage_users.php'); ?>">
                <i class="mfx-mobile-nav-icon fas fa-users-cog"></i>
                <span class="mfx-mobile-nav-label">Users</span>
            </a>
            <a href="../manage_branches.php" class="mfx-mobile-nav-item <?php echo is_active_page('manage_branches.php'); ?>">
                <i class="mfx-mobile-nav-icon fas fa-store-alt"></i>
                <span class="mfx-mobile-nav-label">Branches</span>
            </a>
            <a href="../reports.php" class="mfx-mobile-nav-item <?php echo is_active_page('reports.php'); ?>">
                <i class="mfx-mobile-nav-icon fas fa-file-invoice-dollar"></i>
                <span class="mfx-mobile-nav-label">Reports</span>
            </a>
            <a href="../site_settings.php" class="mfx-mobile-nav-item <?php echo is_active_page('site_settings.php'); ?>">
                <i class="mfx-mobile-nav-icon fas fa-cogs"></i>
                <span class="mfx-mobile-nav-label">Settings</span>
            </a>
        </nav>
    </div>
</header>

<!-- Self-contained script to dynamically adjust body padding -->
<script>
    (function() {
        function setBodyPadding() {
            const header = document.getElementById('mfx-header-main');
            if (header) {
                const headerHeight = header.offsetHeight;
                document.body.style.paddingTop = headerHeight + 'px';
            }
        }
        document.addEventListener('DOMContentLoaded', setBodyPadding);
        window.addEventListener('resize', setBodyPadding);
    })();
</script>