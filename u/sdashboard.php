<?php 
// Corrected Path: Include header from the 'ic' subfolder
require_once 'ic/sheader.php'; 
?>

<main class="mf-main-content">
    <div class="mf-page-title">
        <h1>Dashboard Overview</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Here's a summary of your site's activity.</p>
    </div>

    <div class="mf-widgets-grid">
        <div class="mf-widget">
            <h3>Today's Sales</h3>
            <p class="mf-widget-value">Ksh 0.00</p>
        </div>
        <div class="mf-widget">
            <h3>Today's Profit</h3>
            <p class="mf-widget-value">Ksh 0.00</p>
        </div>
        <div class="mf-widget">
            <h3>Expenses Today</h3>
            <p class="mf-widget-value">Ksh 0.00</p>
        </div>
        <div class="mf-widget">
            <h3>Active Chats</h3>
            <p class="mf-widget-value">0</p>
        </div>
    </div>
    
    <div class="mf-content-panel">
        <h2>Recent Activity</h2>
        <p>A live feed of sales, stock updates, and user logins will be displayed here.</p>
    </div>
</main>

<style>
    .mf-main-content { padding: 20px 40px; max-width: 1600px; margin: 0 auto; }
    .mf-page-title h1 { font-size: 2rem; font-weight: 700; margin: 0 0 5px 0; color: var(--mf-dark); }
    .mf-page-title p { font-size: 1rem; color: #6c757d; margin-top: 0; }
    .mf-widgets-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-top: 30px; }
    .mf-widget { background-color: var(--mf-white); padding: 25px; border-radius: 12px; border: 1px solid var(--mf-grey-border); box-shadow: 0 4px 6px var(--mf-shadow); }
    .mf-widget h3 { margin: 0 0 10px 0; font-size: 1rem; font-weight: 600; color: #495057; }
    .mf-widget-value { margin: 0; font-size: 2.2rem; font-weight: 700; color: var(--mf-green); }
    .mf-content-panel { background-color: var(--mf-white); padding: 25px; border-radius: 12px; border: 1px solid var(--mf-grey-border); margin-top: 30px; }
    .mf-content-panel h2 { margin: 0 0 15px 0; }
</style>

<?php 
// Corrected Path: Include footer from the 'ic' subfolder
require_once 'ic/sfooter.php'; 
?>

