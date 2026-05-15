<?php

ob_start(); 
session_start(); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';


if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}




$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$images = [];
$compatible_vehicles = '';
$error_message = '';
$page_title = 'Motorfix Injection Services'; // Default title
$meta_description = 'Find genuine auto parts at Motorfix Injection Services.';
$og_tags_html = '';
$form_message = '';
$form_message_type = ''; 

$kenyan_counties = [
    1 => 'Mombasa', 2 => 'Kwale', 3 => 'Kilifi', 4 => 'Tana River', 5 => 'Lamu',
    6 => 'Taita-Taveta', 7 => 'Garissa', 8 => 'Wajir', 9 => 'Mandera', 10 => 'Marsabit',
    11 => 'Isiolo', 12 => 'Meru', 13 => 'Tharaka-Nithi', 14 => 'Embu', 15 => 'Kitui',
    16 => 'Machakos', 17 => 'Makueni', 18 => 'Nyandarua', 19 => 'Nyeri', 20 => 'Kirinyaga',
    21 => 'Murang\'a', 22 => 'Kiambu', 23 => 'Turkana', 24 => 'West Pokot', 25 => 'Samburu',
    26 => 'Trans Nzoia', 27 => 'Uasin Gishu', 28 => 'Elgeyo-Marakwet', 29 => 'Nandi',
    30 => 'Baringo', 31 => 'Laikipia', 32 => 'Nakuru', 33 => 'Narok', 34 => 'Kajiado',
    35 => 'Kericho', 36 => 'Bomet', 37 => 'Kakamega', 38 => 'Vihiga', 39 => 'Bungoma',
    40 => 'Busia', 41 => 'Siaya', 42 => 'Kisumu', 43 => 'Homa Bay', 44 => 'Migori',
    45 => 'Kisii', 46 => 'Nyamira', 47 => 'Nairobi'
];

// --- Pre-Order Form Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['preorder_submit'])) {
    
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $county = htmlspecialchars(trim($_POST['county']));
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $more_parts = htmlspecialchars(trim($_POST['more_parts']));
    $vehicle_brand = htmlspecialchars(trim($_POST['vehicle_brand']));
    $requirement = htmlspecialchars(trim($_POST['requirement']));

    // Simple Validation
    if (empty($name) || empty($phone)) {
        $form_message = 'Please fill in your name and phone number.';
        $form_message_type = 'error';
    } elseif (!preg_match('/^\+254[17]\d{8}$/', $phone)) {
         $form_message = 'Please enter a valid Kenyan phone number (e.g., +2547... or +2541...).';
         $form_message_type = 'error';
    } else {
        
        // Send Email via PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Server settings from .env
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port       = $_ENV['SMTP_PORT'];

            //Recipients
            $mail->setFrom($_ENV['SMTP_USER'], 'Motorfix Pre-Order');
            $mail->addAddress('admin@motorfix.co.ke', 'Motorfix Admin'); // Send to admin
            $mail->addCC('info@motorfix.co.ke', 'Motorfix Info');      // CC info
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Spare Part Pre-Order: ' . $product_name;
            
            $email_body = "
                <p>You have received a new pre-order from the website.</p>
                <hr>
                <p><strong>Customer Name:</strong> {$name}</p>
                <p><strong>Customer Phone:</strong> {$phone}</p>
                <p><strong>County:</strong> " . ($county ?: 'Not provided') . "</p>
                <hr>
                <p><strong>Product Requested:</strong> {$product_name}</p>
                <p><strong>Other Parts:</strong> " . (nl2br($more_parts) ?: 'None') . "</p>
                <p><strong>Vehicle Brand/Model:</strong> " . ($vehicle_brand ?: 'Not provided') . "</p>
                <p><strong>Other Requirements:</strong> " . (nl2br($requirement) ?: 'None') . "</p>
                <hr>
                <p>Please follow up with the customer via their phone number.</p>
            ";
            
            $mail->Body = $email_body;
            $mail->AltBody = strip_tags($email_body); // Plain text version

            $mail->send();
            $form_message = 'Thank you! Your pre-order has been sent. We will contact you shortly.';
            $form_message_type = 'success';

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo); 
            $form_message = 'Message could not be sent. Please try again later or contact us on WhatsApp.';
            $form_message_type = 'error';
        }
    }
}

// --- Product Data Fetching ---
if ($product_id > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                p.id, p.name, p.description, p.manufacturer_part_number,
                pc.id as category_id, pc.name as category_name,
                GROUP_CONCAT(DISTINCT pi.image_path ORDER BY pi.is_primary DESC, pi.id ASC SEPARATOR '||') as images,
                GROUP_CONCAT(DISTINCT CONCAT(cmk.name, ' ', cm.name) ORDER BY cmk.name, cm.name SEPARATOR ', ') as compatible_vehicles
            FROM products p
            JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN product_images pi ON p.id = pi.product_id
            LEFT JOIN product_vehicle_compatibility pvc ON p.id = pvc.product_id
            LEFT JOIN car_models cm ON pvc.model_id = cm.id
            LEFT JOIN car_makes cmk ON cm.make_id = cmk.id
            WHERE p.id = :product_id AND p.is_active = 1
            GROUP BY p.id
        ");
        $stmt->execute(['product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Process images
            if (!empty($product['images'])) {
                $images = explode('||', $product['images']);
            }
            if (empty($images) || !$images[0]) {
                $images = ['placeholder-product.png'];
            }

            $compatible_vehicles = $product['compatible_vehicles'];
            
            // --- Set SEO and Meta Tags ---
            $page_title = htmlspecialchars($product['name']) . ' - Motorfix';
            
            if (!empty($product['description'])) {
                $meta_description = htmlspecialchars(substr(strip_tags($product['description']), 0, 160)) . '...';
            } else {
                $meta_description = 'Find ' . htmlspecialchars($product['name']) . ' at Motorfix Injection Services.';
            }
            
            $current_url = "https://" . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']);
            $og_tags_html .= '<meta property="og:title" content="' . htmlspecialchars($product['name']) . '">';
            $og_tags_html .= '<meta property="og:url" content="' . $current_url . '">';
            $og_tags_html .= '<meta property="og:type" content="product">';
            $og_tags_html .= '<meta property="og:site_name" content="Motorfix Injection Services">';
            $og_tags_html .= '<meta property="og:description" content="' . $meta_description . '">';
            if ($images[0] !== 'placeholder-product.png') {
                 $og_tags_html .= '<meta property="og:image" content="https://' . $_SERVER['HTTP_HOST'] . '/u/media/products/' . htmlspecialchars($images[0]) . '">';
            }

            // Record the page view
            try {
                $visitor_identifier = hash('sha256', $_SERVER['REMOTE_ADDR']);
                $view_stmt = $pdo->prepare("
                    INSERT INTO page_views (product_id, url, visitor_identifier) 
                    VALUES (:product_id, :url, :visitor_identifier)
                ");
                $view_stmt->execute([
                    'product_id' => $product_id,
                    'url' => $current_url,
                    'visitor_identifier' => $visitor_identifier
                ]);
            } catch (PDOException $e) {
                error_log("Failed to record page view: " . $e->getMessage());
            }

        } else {
            $error_message = "The product you are looking for could not be found.";
            $page_title = 'Product Not Found - Motorfix';
            $meta_description = 'This product could not be found.';
        }
    } catch (PDOException $e) {
        error_log("Product page database error: " . $e->getMessage());
        $error_message = "An error occurred while trying to load the product.";
        $page_title = 'Error - Motorfix';
        $meta_description = 'An error occurred while loading this page.';
    }
} else {
    $error_message = "No product ID was specified.";
    $page_title = 'Error - Motorfix';
    $meta_description = 'No product was specified.';
}

// --- STEP 3: OUTPUT HTML ---

// Include the standard site header
require_once 'head.php'; 
?>

<!-- Inject dynamic meta tags into <head> -->
<script>
    document.title = <?php echo json_encode($page_title); ?>;
    
    var metaDesc = document.createElement('meta');
    metaDesc.name = 'description';
    metaDesc.content = <?php echo json_encode($meta_description); ?>;
    document.head.appendChild(metaDesc);
    
    var ogTagsContainer = document.createElement('div');
    ogTagsContainer.innerHTML = <?php echo json_encode($og_tags_html); ?>;
    
    Array.from(ogTagsContainer.children).forEach(function(meta) {
        document.head.appendChild(meta);
    });
</script>

<!-- Page-specific styles -->
<style>
    /* --- Responsive Overflow Fix v3 (The Correct One) --- */
    /* This ensures all elements include padding and borders
       in their width/height, not as extra space. This
       was missing and caused the overflow.
    */
    .mfx-prod-page-wrapper *,
    .mfx-prod-page-wrapper *::before,
    .mfx-prod-page-wrapper *::after {
        box-sizing: border-box;
    }

    /* REMOVED the body and footer overrides as they were
       incorrect. The problem was box-sizing.
    */

    :root {
        --mfix-dark-green: #0A4A2A;
        --mfix-black: #1a1a1a;
        --mfix-grey: #6c757d;
        --mfix-light-grey: #f8f9fa;
        --mfix-white: #ffffff;
        --mfix-border: #e5e5e5;
        --mfix-shadow: rgba(0, 0, 0, 0.08);
        --mfix-whatsapp: #25D366;
        --mfix-success-bg: #e8f5e9;
        --mfix-success-text: #155724;
        --mfix-error-bg: #fee;
        --mfix-error-text: #c33;
    }

    .mfx-prod-page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 20px 60px; /* Mobile padding FIXED (was 15px) */
    }

    /* Breadcrumbs */
    .mfx-prod-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 25px;
        font-size: 0.9rem;
        color: var(--mfix-grey);
        flex-wrap: wrap;
    }
    .mfx-prod-breadcrumbs a {
        color: var(--mfix-dark-green);
        text-decoration: none;
        font-weight: 500;
    }
    .mfx-prod-breadcrumbs a:hover {
        text-decoration: underline;
    }
    .mfx-prod-breadcrumbs i {
        font-size: 0.7rem;
    }
    .mfx-prod-breadcrumbs span {
        font-weight: 500;
        color: var(--mfix-black);
    }

    /* Main Container */
    .mfx-prod-container {
        display: grid;
        grid-template-columns: 1fr; /* Mobile-first: single column */
        gap: 30px;
    }
    
    /* Desktop: 2-column grid */
    @media (min-width: 768px) {
        .mfx-prod-container {
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .mfx-prod-page-wrapper {
             padding: 20px 20px 60px; /* Desktop padding */
        }
    }

    /* Image Column */
    .mfx-prod-gallery { width: 100%; }
    .mfx-prod-main-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        height: auto;
        max-height: 550px;
        background: var(--mfix-light-grey);
        border: 1px solid var(--mfix-border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 15px;
        cursor: zoom-in;
    }
    .mfx-prod-main-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    /* Info & Form Column */
    .mfx-prod-info { padding-top: 10px; }
    .mfx-prod-category {
        display: inline-block;
        padding: 6px 14px;
        background: var(--mfix-success-bg);
        color: var(--mfix-dark-green);
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .mfx-prod-title-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        margin: 0 0 15px;
    }
    
    .mfx-prod-title {
        font-size: 1.8rem; /* Mobile title size */
        font-weight: 700;
        color: var(--mfix-black);
        line-height: 1.2;
        margin: 0;
        flex: 1;
        /* Add word break for safety */
        overflow-wrap: break-word;
        word-break: break-word; /* PRECAUTION FIX */
    }
    
    .mfx-prod-btn-share-icon {
        width: 44px;
        height: 44px;
        background: var(--mfix-light-grey);
        color: var(--mfix-black);
        border: 1px solid var(--mfix-border);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
        margin-top: 5px;
    }
    .mfx-prod-btn-share-icon:hover { 
        background: #e9ecef;
        transform: scale(1.05);
    }
    .mfx-prod-btn-share-icon.copied {
        background: var(--mfix-dark-green);
        color: var(--mfix-white);
        border-color: var(--mfix-dark-green);
    }
    
    .mfx-prod-part-number {
        font-size: 1rem;
        color: var(--mfix-grey);
        font-weight: 500;
        margin-bottom: 30px;
    }

    .mfx-prod-cta-bar {
        display: flex;
        flex-direction: column; /* Stack buttons on mobile */
        gap: 15px;
        margin-top: 30px;
    }
    
    .mfx-prod-btn {
        padding: 14px 20px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
    }
    .mfx-prod-btn-whatsapp {
        background: var(--mfix-whatsapp);
        color: var(--mfix-white);
        width: 100%;
    }
    .mfx-prod-btn-whatsapp:hover {
        background: #20ba5a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    }

    /* Pre-Order Form Styles (from product 4) */
    .mfx-prod-preorder-form {
        margin: 30px 0 0 0; /* Updated margin */
        padding: 25px 20px;
        background: var(--mfix-light-grey);
        border-radius: 12px;
        border: 1px solid var(--mfix-border);
        max-width: 100%; /* Updated from 600px */
    }
    .mfx-prod-preorder-form h3 {
        text-align: center;
        font-size: 1.2rem; /* Updated font size */
        color: var(--mfix-black);
        margin-bottom: 20px; /* Updated margin */
    }
    .mfx-form-group {
        margin-bottom: 18px; /* Updated margin */
    }
    .mfx-form-group label {
        display: block;
        margin-bottom: 6px; /* Updated margin */
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }
    .mfx-form-group input[type="text"],
    .mfx-form-group input[type="tel"],
    .mfx-form-group select,
    .mfx-form-group textarea {
        width: 100%;
        padding: 12px 15px; /* Updated padding */
        border: 1px solid var(--mfix-border);
        border-radius: 8px;
        font-size: 0.9rem; /* Updated font size */
        font-family: 'Inter', sans-serif;
        background: var(--mfix-white);
        /* box-sizing: border-box;  <-- This is now handled by the global rule */
    }
    .mfx-form-group input:focus,
    .mfx-form-group select:focus,
    .mfx-form-group textarea:focus {
        outline: none;
        border-color: var(--mfix-dark-green);
        box-shadow: 0 0 0 3px rgba(10, 74, 42, 0.1);
    }
    .mfx-form-group input[readonly] {
        background: #eee;
        color: #777;
    }
    .mfx-form-group small {
        font-size: 0.8rem;
        color: var(--mfix-grey);
        margin-top: 5px;
    }
    .mfx-form-submit-btn {
        width: 100%;
        padding: 14px; /* Updated padding */
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        background: var(--mfix-dark-green);
        color: var(--mfix-white);
    }
    .mfx-form-submit-btn:hover {
        background: #083820;
        transform: translateY(-2px);
    }
    .mfx-form-message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-size: 0.95rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .mfx-form-message.success {
        background: var(--mfix-success-bg);
        color: var(--mfix-success-text);
        border: 1px solid var(--mfix-success-text);
    }
    .mfx-form-message.error {
        background: var(--mfix-error-bg);
        color: var(--mfix-error-text);
        border: 1px solid var(--mfix-error-text);
    }

    /* Error State */
    .mfx-prod-error {
        text-align: center;
        padding: 80px 20px;
        color: var(--mfix-grey);
    }
    .mfx-prod-error-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    .mfx-prod-error-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--mfix-black);
    }

    /* Image Lightbox Styles */
    .mfx-prod-lightbox {
        display: none;
        position: fixed;
        z-index: 2000;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box; /* Add box-sizing to the lightbox itself */
        cursor: pointer;
    }
    .mfx-prod-lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 3rem;
        color: var(--mfix-white);
        font-weight: 300;
        cursor: pointer;
        line-height: 1;
        transition: color 0.3s ease;
    }
    .mfx-prod-lightbox-close:hover {
        color: var(--mfix-grey);
    }
    .mfx-prod-lightbox-content {
        display: block;
        max-width: 90%;
        max-height: 90vh;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
        cursor: default;
    }
    
    /* Responsive */
    @media (min-width: 480px) {
        /* On larger phones, show WhatsApp and Share side-by-side */
        .mfx-prod-cta-bar {
            flex-direction: row;
        }
        .mfx-prod-btn-whatsapp {
            flex: 1; /* Takes up most space */
        }
    }

    @media (min-width: 768px) {
        .mfx-prod-title { 
            font-size: 2.5rem; /* Desktop title size */
        }
    }
</style>

<!-- This div is part of head.php -->
<div class="mfix-body-content-spacer">
    <div class="mfx-prod-page-wrapper">

        <?php if ($product): ?>
            
            <!-- Breadcrumbs -->
            <nav class="mfx-prod-breadcrumbs">
                <a href="/">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="/products">Products</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>

            <!-- Main Product Content -->
            <div class="mfx-prod-container">
                
                <!-- Gallery Column -->
                <div class="mfx-prod-gallery">
                    <div class="mfx-prod-main-image" id="mainProductImageContainer" title="Click to zoom">
                        <img src="/u/media/products/<?php echo htmlspecialchars($images[0]); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             id="mainProductImage"
                             onerror="this.src='/media/placeholder-product.png'">
                    </div>
                </div>

                <!-- Info Column -->
                <div class="mfx-prod-info">
                    <span class="mfx-prod-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    
                    <div class="mfx-prod-title-wrapper">
                        <h1 class="mfx-prod-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <button class="mfx-prod-btn-share-icon" id="shareButton" title="Share">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                    
                    <?php if (!empty($product['manufacturer_part_number'])): ?>
                        <p class="mfx-prod-part-number">
                            Part #: <strong><?php echo htmlspecialchars($product['manufacturer_part_number']); ?></strong>
                        </p>
                    <?php endif; ?>
                    
                    <!-- CTA Buttons -->
                    <div class="mfx-prod-cta-bar">
                        <?php
                            $product_name_encoded = urlencode($product['name']);
                            $message = "I am interested in this product ({$product_name_encoded}), tell me more about this product....";
                            $whatsapp_number = "254725044914"; 
                            $whatsapp_link = "https://wa.me/{$whatsapp_number}?text={$message}";
                        ?>
                        <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="mfx-prod-btn mfx-prod-btn-whatsapp">
                            <i class="fab fa-whatsapp"></i> Enquire via WhatsApp
                        </a>
                    </div>
                    
                    <!-- Form Toggle Link REMOVED -->
                    
                    <!-- Pre-Order Form MOVED HERE and made visible -->
                    <div class="mfx-prod-preorder-form" id="preorder-form">
                        <h3>Pre-Order This Spare Part</h3>
                        
                        <?php if ($form_message): ?>
                            <div class="mfx-form-message <?php echo $form_message_type; ?>">
                                <?php if ($form_message_type == 'success'): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                <?php endif; ?>
                                <span><?php echo $form_message; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>#preorder-form" method="POST">
                            
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                            
                            <div class="mfx-form-group">
                                <label for="name">Your Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="mfx-form-group">
                                <label for="phone">Phone Number (+254) *</label>
                                <input type="tel" id="phone" name="phone" required pattern="\+254[17]\d{8}" title="e.g., +2547..." placeholder="+254700000000">
                                <small>Format: +2547... or +2541...</small>
                            </div>
                            
                            <div class="mfx-form-group">
                                <label for="county">County (Optional)</label>
                                <select id="county" name="county">
                                    <option value="">Select your county</option>
                                    <?php foreach ($kenyan_counties as $code => $name): ?>
                                        <option value="<?php echo htmlspecialchars($name); ?>"><?php echo "$code. " . htmlspecialchars($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mfx-form-group">
                                <label for="spare_part">Spare Part (Auto-filled)</label>
                                <input type="text" id="spare_part" value="<?php echo htmlspecialchars($product['name']); ?>" readonly>
                            </div>
                            
                            <!-- Updated to text input to match product (4).php -->
                            <div class="mfx-form-group">
                                <label for="more_parts">Other Parts You Need (Optional)</label>
                                <input type="text" id="more_parts" name="more_parts" placeholder="e.g., Camplate, Nozzle testers...">
                            </div>
                            
                            <!-- Updated placeholder -->
                            <div class="mfx-form-group">
                                <label for="vehicle_brand">Vehicle Brand (Optional)</label>
                                <input type="text" id="vehicle_brand" name="vehicle_brand" placeholder="">
                            </div>
                            
                            <!-- Updated label -->
                            <div class="mfx-form-group">
                                <label for="requirement">Special Requirements (Optional)</label>
                                <textarea id="requirement" name="requirement" rows="3" placeholder="Any specific requirements or notes"></textarea>
                            </div>
                            
                            <!-- Updated button text/icon -->
                            <button type="submit" name="preorder_submit" class="mfx-form-submit-btn">
                                <i class="fas fa-paper-plane"></i> Submit Pre-Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Error State -->
            <div class="mfx-prod-error">
                <div class="mfx-prod-error-icon"><i class="fas fa-box-open"></i></div>
                <h3 class="mfx-prod-error-title">Product Not Found</h3>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <a href="/" class="mfx-prod-btn mfx-prod-btn-whatsapp" style="margin-top: 20px; background: var(--mfix-dark-green);">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        <?php endif; ?>

    </div>
</div> <!-- End of .mfix-body-content-spacer -->

<!-- Image Lightbox Modal -->
<div id="imageLightbox" class="mfx-prod-lightbox">
    <span class="mfx-prod-lightbox-close" id="lightboxClose">&times;</span>
    <img class="mfx-prod-lightbox-content" id="lightboxImage">
</div>

<!-- Page-specific JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Image Lightbox Logic ---
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    const lightboxClose = document.getElementById('lightboxClose');
    const mainImageContainer = document.getElementById('mainProductImageContainer');
    const mainImage = document.getElementById('mainProductImage');

    if (mainImageContainer && lightbox && lightboxImg && lightboxClose) {
        mainImageContainer.addEventListener('click', function() {
            lightboxImg.src = mainImage.src;
            lightbox.style.display = 'flex';
        });
        lightboxClose.addEventListener('click', function() {
            lightbox.style.display = 'none';
        });
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                lightbox.style.display = 'none';
            }
        });
    }

    // --- Share Button Logic ---
    const shareButton = document.getElementById('shareButton');
    if (shareButton) {
        const shareIcon = shareButton.querySelector('i');
        const originalIconClass = shareIcon.className;
        
        shareButton.addEventListener('click', function() {
            const urlToCopy = window.location.href;
            
            navigator.clipboard.writeText(urlToCopy).then(() => {
                shareIcon.className = 'fas fa-check';
                shareButton.classList.add('copied');
                setTimeout(() => {
                    shareIcon.className = originalIconClass;
                    shareButton.classList.remove('copied');
                }, 2000);
            }).catch(err => {
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = urlToCopy;
                    textArea.style.position = 'fixed';
                    textArea.style.opacity = 0;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand('copy');
                    shareIcon.className = 'fas fa-check';
                    shareButton.classList.add('copied');
                    setTimeout(() => {
                        shareIcon.className = originalIconClass;
                        shareButton.classList.remove('copied');
                    }, 2000);
                    document.body.removeChild(textArea);
                } catch (fallbackErr) {
                    console.error('Share fallback failed:', fallbackErr);
                    shareIcon.className = 'fas fa-times';
                    setTimeout(() => {
                        shareIcon.className = originalIconClass;
                    }, 2000);
                }
            });
        });
    }

    // --- Form Toggle Logic REMOVED ---

    // --- Scroll to form if message exists ---
    <?php if (!empty($form_message)): ?>
    const preOrderForm = document.getElementById('preorder-form');
    if (preOrderForm) {
        preOrderForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    <?php endif; ?>
    
    // --- NEW: Phone number formatting (from product 4) ---
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
            
            if (value.startsWith('254')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+254' + value.substring(1);
            } else if (value.length > 0 && !value.startsWith('+')) {
                value = '+254' + value;
            } else if (value.length > 0 && !value.startsWith('+254')) {
                 value = '+254' + value.substring(1);
            }
            
            // Limit to 13 characters (+254xxxxxxxxx)
            if (value.length > 13) {
                value = value.substring(0, 13);
            }
            
            // Set the value, but only if it's different to avoid cursor jumps
            if (e.target.value !== value) {
                 e.target.value = value;
            }
        });
        
        // Ensure pattern validity check works with the JS formatter
         phoneInput.addEventListener('blur', function(e) {
             const pattern = new RegExp(e.target.pattern);
             if (e.target.value.length > 0 && !pattern.test(e.target.value)) {
                 // You could add a custom error message here if needed
                 console.warn('Phone number may not be valid');
             }
         });
    }

});
</script>

<?php 

require_once 'foot.php'; 
ob_end_flush(); 
?>




