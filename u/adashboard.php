<?php 
// Now using aheader.php from the correct relative path
require_once __DIR__ . '/ic/aheader.php'; // Includes security check and branch data

// --- DATA FETCHING for Dashboard ---
// $branch_id = $_SESSION['branch_id']; // Branch ID is available if needed later
$most_viewed_product = null;
$error_message = '';

// Define base URL for the main site (where images and product pages are)
// Assuming the dashboard is at dash.motorfix.co.ke and main site at motorfix.co.ke
$main_site_url = 'https://motorfix.co.ke'; // Use http if not using SSL

try {
    // Get This Week's Most Viewed Product (site-wide)
    $stmt_views = $pdo->prepare("
        SELECT 
            p.id,
            p.name,
            COUNT(pv.id) as view_count,
            (SELECT pi.image_path 
             FROM product_images pi 
             WHERE pi.product_id = p.id 
             ORDER BY pi.is_primary DESC, pi.id ASC 
             LIMIT 1) as image_path
        FROM page_views pv
        JOIN products p ON pv.product_id = p.id
        WHERE pv.view_timestamp >= CURDATE() - INTERVAL 6 DAY  -- Assuming 'view_timestamp' column exists
        AND pv.product_id IS NOT NULL
        GROUP BY p.id, p.name
        ORDER BY view_count DESC
        LIMIT 1
    ");
    $stmt_views->execute();
    $most_viewed_product = $stmt_views->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error_message = "Error fetching dashboard data: " . htmlspecialchars($e.getMessage());
    // Log the detailed error for debugging
    error_log("Dashboard PDO Error: " . $e.getMessage()); 
}

?>

<main class="mfa-main-content">
    <div class="mfa-page-title">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Quick access to manage site content.</p> 
        <!-- Removed branch name as it might not be relevant for these new actions -->
    </div>

    <?php if ($error_message): ?>
        <div class="mfa-feedback-message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Quick Actions Grid -->
    <div class="mfa-content-panel">
        <h2>Quick Actions</h2>
        <div class="mfa-actions-grid">
            <a href="products" class="mfa-action-card"> <!-- Link to existing inventory page -->
                <i class="fas fa-boxes"></i>
                <span>Manage Products</span>
            </a>
            <a href="account" class="mfa-action-card"> <!-- Link to placeholder account page -->
                <i class="fas fa-user-cog"></i>
                <span>My Account</span>
            </a>
            <a href="posts" class="mfa-action-card"> <!-- Link to placeholder posts page -->
                <i class="fas fa-newspaper"></i>
                <span>Manage Blog Posts</span>
            </a>
            <!-- Removed Record Sale, Expense, Branch Reports as requested -->
        </div>
    </div>

    <!-- Most Viewed Product -->
    <div class="mfa-content-panel">
        <h2>This Week's Most Viewed Product</h2>
        <?php if ($most_viewed_product): ?>
            <div class="mfa-most-viewed-card">
                <?php 
                // Construct ABSOLUTE URL for the image on the main domain
                $image_url = $most_viewed_product['image_path'] 
                    ? rtrim($main_site_url, '/') . '/u/media/products/' . htmlspecialchars($most_viewed_product['image_path']) 
                    : rtrim($main_site_url, '/') . '/media/placeholder-product.png'; // Absolute path for placeholder too
                
                // Construct ABSOLUTE URL for the product link on the main domain
                $product_link = rtrim($main_site_url, '/') . '/product?id=' . $most_viewed_product['id'];
                ?>
                <img src="<?php echo $image_url; ?>" 
                     alt="<?php echo htmlspecialchars($most_viewed_product['name']); ?>" 
                     class="mfa-mv-image" 
                     onerror="this.onerror=null; this.src='<?php echo rtrim($main_site_url, '/'); ?>/media/placeholder-product.png';"> <!-- Absolute fallback path -->
                <div class="mfa-mv-info">
                    <h3 class="mfa-mv-name"><?php echo htmlspecialchars($most_viewed_product['name']); ?></h3>
                    <p class="mfa-mv-views"><?php echo $most_viewed_product['view_count']; ?> Views This Week</p>
                    <a href="<?php echo $product_link; ?>" class="mfa-mv-link" target="_blank">View Product <i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>
        <?php elseif (!$error_message): // Only show 'no data' if there wasn't a DB error ?>
            <p>No product view data available for this week.</p>
        <?php endif; ?>
    </div>
</main>

<style>
    /* Using mfa- prefix from aheader.php */
    .mfa-main-content { padding: 20px; max-width: 1200px; margin: 0 auto; }
    .mfa-page-title h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 5px 0; color: var(--mfa-dark); }
    .mfa-page-title p { font-size: 1rem; color: var(--mfa-text-light); margin-top: 0; }
    .mfa-feedback-message.error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    
    .mfa-content-panel { 
        background-color: var(--mfa-white); 
        padding: 20px 25px; 
        border-radius: 12px; 
        border: 1px solid var(--mfa-grey-border); 
        margin-top: 25px;
        box-shadow: 0 4px 6px var(--mfa-shadow);
    }
    .mfa-content-panel h2 { 
        margin: 0 0 20px 0; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        font-size: 1.3rem;
        color: var(--mfa-dark);
        border-bottom: 1px solid var(--mfa-grey-border);
        padding-bottom: 10px;
    }

    /* Quick Actions Grid */
    .mfa-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 columns on mobile */
        gap: 15px;
    }
    .mfa-action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px 15px;
        background-color: var(--mfa-grey-light);
        border: 1px solid var(--mfa-grey-border);
        border-radius: 10px;
        text-decoration: none;
        color: var(--mfa-dark);
        font-size: 0.9rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
        min-height: 120px; /* Ensure cards have some height */
    }
    .mfa-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        border-color: var(--mfa-blue);
        color: var(--mfa-blue);
    }
    .mfa-action-card i {
        font-size: 2rem;
        color: var(--mfa-blue);
        margin-bottom: 12px;
        transition: color 0.2s ease;
    }
    .mfa-action-card:hover i {
        color: var(--mfa-blue);
    }

    /* Most Viewed Card */
    .mfa-most-viewed-card {
        display: flex;
        flex-direction: column; /* Start stacked on mobile */
        align-items: flex-start; /* Align content left */
        gap: 15px; /* Adjust gap */
    }
    .mfa-mv-image {
        width: 100%; /* Full width on mobile */
        max-width: 150px; /* Limit size */
        height: auto; /* Maintain aspect ratio */
        aspect-ratio: 1 / 1; /* Make it square */
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--mfa-grey-border);
        background-color: var(--mfa-grey-light);
        flex-shrink: 0;
    }
    .mfa-mv-info {
        flex: 1;
    }
    .mfa-mv-name {
        margin: 0 0 5px 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--mfa-dark);
    }
    .mfa-mv-views {
        margin: 0 0 12px 0;
        font-size: 1rem;
        font-weight: 500;
        color: var(--mfa-green);
    }
    .mfa-mv-link {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--mfa-blue);
        text-decoration: none;
    }
    .mfa-mv-link:hover {
        text-decoration: underline;
    }
    .mfa-mv-link i {
        font-size: 0.8rem;
        margin-left: 4px;
    }

    /* Responsive */
    @media (min-width: 576px) { /* Small devices (landscape phones, etc.) */
         .mfa-most-viewed-card {
            flex-direction: row; /* Side-by-side layout */
            align-items: center; /* Center vertically */
            gap: 20px; 
         }
         .mfa-mv-image {
             width: 100px; /* Fixed width */
             height: 100px;
             max-width: none; /* Remove max-width */
         }
    }

    @media (min-width: 768px) { /* Medium devices (tablets, small desktops) */
        .mfa-main-content { padding: 30px; }
        .mfa-actions-grid {
            grid-template-columns: repeat(3, 1fr); /* 3 columns for the 3 links */
            gap: 20px;
        }
        .mfa-action-card {
            font-size: 1rem;
            padding: 30px 20px;
        }
    }
    
</style>

<?php 
// Now using afooter.php from the correct relative path
require_once __DIR__ . '/ic/afooter.php'; 
?>

