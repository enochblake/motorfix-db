<?php
/**
 * Motorfix Injection Services - Professional Header v1.0
 *
 * A clean, responsive, and self-contained header for the Motorfix website.
 *
 * Features:
 * - Professional design with Motorfix branding and color scheme.
 * - Fully responsive layout: Converts from a two-row mobile view to a single-row desktop view.
 * - Scoped CSS classes (prefixed with `mfix-`) to prevent conflicts with other page styles.
 * - No dependency on databases or sessions.
 * - Includes important navigation links with icons.
 *
 * @version 1.0
 * @date Monday, October 20, 2025
 */

// Helper function to apply an 'active' class to the current page's navigation link.
function is_active_link($page_name) {
    // Sanitize the current script name to get just the filename.
    $current_page = basename($_SERVER['PHP_SELF']);

    // The .htaccess rewrites URLs, so 'index.php' might be requested as 'index'.
    // We compare against both with and without the .php extension.
    if ($current_page == $page_name || $current_page == $page_name . '.php') {
        return 'mfix-nav-active';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website">
<meta property="og:site_name" content="MOTORFIX INJECTION SERVICES">
<meta property="og:title" content="MOTORFIX INJECTION SERVICES">
<meta property="og:description" content="Kenya's leading importer of genuine injector pump spare parts. Bosch Authorized Wholesaler.">
<meta property="og:image" content="https://motorfix.co.ke/media/slogo.png">
<meta property="og:url" content="https://motorfix.co.ke">
    <title>Motorfix Injection Services</title>

    <!-- Site Favicon -->
    <link rel="icon" href="/media/slogo.png" type="image/png">
<link rel="icon" href="/media/slogo.png" sizes="32x32" type="image/png">
<link rel="icon" href="/media/slogo.png" sizes="192x192" type="image/png">
<link rel="apple-touch-icon" href="/media/slogo.png">

<!-- CRITICAL: Add this for Google Search -->
<link rel="shortcut icon" href="/media/slogo.png">
    <!-- SEO & CRAWLER ENHANCEMENTS START -->
    
    <!-- 1. General SEO Description and Keywords -->
    <!-- This is hidden from the UI but essential for crawlers -->
    <meta name="description" content="Motorfix Injection Services provides genuine and quality replacement injector pump spare parts, specializing in diesel engine fuel injection system components.">
    <meta name="keywords" content="injector pump parts, diesel engine spares, fuel injection systems, injector pump service, Motorfix, slogo.png">

    <!-- 2. Structured Data (Schema.org) for Organization and Logo (THE FIX FOR YOUR LOGO) -->
    <!-- This JSON-LD script explicitly defines your organization, main website, and official logo URL. -->
    <!-- Structured Data for Sitelinks -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "MOTORFIX INJECTION SERVICES",
  "url": "https://motorfix.co.ke",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://motorfix.co.ke/products?search={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>

<!-- Organization Schema - ENHANCED -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "MOTORFIX INJECTION SERVICES",
  "alternateName": "Motorfix Injection Services",
  "url": "https://motorfix.co.ke",
  "logo": "https://motorfix.co.ke/media/slogo.png",
  "image": "https://motorfix.co.ke/media/slogo.png",
  "description": "Kenya's leading importer of genuine and quality injector pump spare parts",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Jekima Plaza, Jogoo Road",
    "addressLocality": "Nairobi",
    "addressCountry": "Kenya"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+254725044914",
    "contactType": "Sales and Support",
    "areaServed": "KE",
    "availableLanguage": "English, Swahili"
  },
  "sameAs": [
    "https://www.facebook.com/motorfixinjection",
    "https://www.instagram.com/motorfix_injector.services?utm_source=qr&igsh=MTNqNnhhenlrZ21oOQ=="
  ]
}
</script>

<!-- Breadcrumb Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://motorfix.co.ke/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Products",
      "item": "https://motorfix.co.ke/products"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "About Us",
      "item": "https://motorfix.co.ke/about"
    },
    {
      "@type": "ListItem",
      "position": 4,
      "name": "Contact",
      "item": "https://motorfix.co.ke/contact"
    }
  ]
}
</script>
    
    
    
    <!-- 3. Open Graph / Social Media Tags (For better sharing on platforms like Facebook/Twitter) -->
    <!-- These are also hidden from the site UI but important for SEO and sharing -->
    <meta property="og:title" content="Motorfix Injection Services - Genuine Injector Pump Parts">
    <meta property="og:description" content="Kenya's leading importer of genuine and quality replacement injector pump spare parts.">
    <meta property="og:image" content="https://motorfix.co.ke/media/slogo.png">
    <meta property="og:url" content="https://motorfix.co.ke">
    <meta property="og:site_name" content="Motorfix Injection Services">
    
    <!-- SEO & CRAWLER ENHANCEMENTS END -->

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts: Inter for a clean, professional look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Motorfix Header Styles v1.0 --- */
        /* All classes are prefixed with 'mfix-' to avoid conflicts. */

        :root {
            --mfix-dark-green: #0A4A2A; /* A deep, professional green */
            --mfix-black: #1a1a1a;
            --mfix-grey: #6c757d;
            --mfix-light-grey: #f8f9fa;
            --mfix-white: #ffffff;
            --mfix-border: #e5e5e5;
            --mfix-shadow: rgba(0, 0, 0, 0.08);
        }

        /* --- Main Header Container --- */
        .mfix-header-main {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: var(--mfix-white);
            box-shadow: 0 2px 10px var(--mfix-shadow);
            z-index: 1000;
            border-bottom: 1px solid var(--mfix-border);
            font-family: 'Inter', sans-serif;
        }

        /* --- Top Row: Logo & Search --- */
        .mfix-header-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            max-width: 1600px;
            margin: 0 auto;
            min-height: 60px;
        }

        /* Logo Section */
        .mfix-logo-section {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .mfix-logo-section:hover {
            opacity: 0.85;
        }

        .mfix-logo-icon img {
            height: 48px;
            width: auto;
            margin-right: 12px;
        }

        .mfix-logo-text h1 {
            font-size: 1.3rem;
            color: var(--mfix-black);
            margin: 0;
            font-weight: 700;
        }

        .mfix-logo-text p {
            font-size: 0.75rem;
            color: var(--mfix-grey);
            margin: 0;
            font-weight: 500;
        }

        /* --- Desktop Navigation (Hidden on Mobile) --- */
        .mfix-desktop-nav {
            display: none; /* Hidden by default, shown on larger screens */
            flex: 1;
            justify-content: center;
            margin: 0 40px;
        }

        .mfix-desktop-nav-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .mfix-desktop-nav-item {
            text-decoration: none;
            color: var(--mfix-black);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        
        .mfix-desktop-nav-item i {
            margin-right: 6px;
        }

        .mfix-desktop-nav-item:hover {
            background-color: var(--mfix-light-grey);
            color: var(--mfix-dark-green);
        }

        .mfix-desktop-nav-item.mfix-nav-active {
            color: var(--mfix-dark-green);
        }

        .mfix-desktop-nav-item.mfix-nav-active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 15px;
            right: 15px;
            height: 3px;
            background: var(--mfix-dark-green);
            border-radius: 2px;
        }

        /* Search & Actions */
        .mfix-action-icons {
            display: flex;
            align-items: center;
        }

        .mfix-search-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: var(--mfix-light-grey);
            border-radius: 50%;
            color: var(--mfix-black);
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            border: none;
        }

        .mfix-search-button:hover {
            background: var(--mfix-dark-green);
            color: var(--mfix-white);
            transform: scale(1.05);
        }

        /* --- Bottom Row: Mobile Navigation --- */
        .mfix-header-bottom-row {
            border-top: 1px solid var(--mfix-border);
            padding: 8px 10px;
            background: var(--mfix-white);
        }

        .mfix-mobile-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
        }

        .mfix-mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            padding: 6px 4px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            flex: 1;
            max-width: 90px;
        }
        
        .mfix-mobile-nav-item:hover {
             background: var(--mfix-light-grey);
        }

        .mfix-mobile-nav-icon {
            font-size: 1.2rem;
            color: var(--mfix-grey);
            margin-bottom: 5px;
            transition: color 0.2s ease;
        }

        .mfix-mobile-nav-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            color: var(--mfix-black);
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
        }

        /* Active Mobile Item Styles */
        .mfix-mobile-nav-item.mfix-nav-active .mfix-mobile-nav-icon,
        .mfix-mobile-nav-item.mfix-nav-active .mfix-mobile-nav-label {
            color: var(--mfix-dark-green);
        }
        
        .mfix-mobile-nav-item.mfix-nav-active .mfix-mobile-nav-label {
             font-weight: 700;
        }

        /* --- Responsive Design --- */

        /* Tablet and Small Desktop: Transition to single row */
        @media (min-width: 992px) {
            .mfix-desktop-nav {
                display: flex; /* Show desktop nav */
            }

            .mfix-header-bottom-row {
                display: none; /* Hide mobile nav */
            }

            .mfix-header-top-row {
                padding: 10px 30px;
                min-height: 70px;
            }
        }
        
        /* Larger Desktops */
        @media (min-width: 1200px) {
             .mfix-header-top-row {
                padding: 10px 40px;
            }
            .mfix-desktop-nav-container {
                gap: 30px;
            }
        }

        /* --- Body Spacing --- */
        /* This class should be added to your main content container */
        .mfix-body-content-spacer {
            padding-top: 135px; /* Space for mobile header (top row + bottom row) */
        }

        @media (min-width: 992px) {
            .mfix-body-content-spacer {
                padding-top: 90px; /* Space for single-row desktop header */
            }
        }
    </style>
</head>
<body>

<header class="mfix-header-main">
    <!-- Top Row: Contains logo, desktop navigation (hidden on mobile), and search -->
    <div class="mfix-header-top-row">
        <a href="index" class="mfix-logo-section">
            <div class="mfix-logo-icon">
                <!-- Using absolute path from web root for consistency -->
                <img src="/media/slogo.png" alt="Motorfix Logo" onerror="this.style.display='none'">
            </div>
            <div class="mfix-logo-text">
                <h1 style="font-weight: 900; font-family: Arial, Helvetica, sans-serif; color: #4ab247;">
  MOTORFIX INJECTION SERVICES
</h1>
                <p> Genuine & Quality Replacement Injector Pump Spare Parts</p>
            </div>
        </a>

        <!-- Desktop Navigation (becomes visible on screens > 992px) -->
        <nav class="mfix-desktop-nav">
            <div class="mfix-desktop-nav-container">
                <a href="/" class="mfix-desktop-nav-item <?php echo is_active_link('index'); ?>">
                    <i class="fas fa-home"></i>Home
                </a>
                <a href="products" class="mfix-desktop-nav-item <?php echo is_active_link('products'); ?>">
                    <i class="fas fa-cogs"></i>Products
                </a>
                <a href="about" class="mfix-desktop-nav-item <?php echo is_active_link('about'); ?>">
                    <i class="fas fa-info-circle"></i>About Us
                </a>
                 
                <a href="contact" class="mfix-desktop-nav-item <?php echo is_active_link('contact'); ?>">
                    <i class="fas fa-phone-alt"></i>Contact
                </a>
            </div>
        </nav>

        
    </div>

    <!-- Bottom Row: Mobile Navigation (visible on screens < 992px) -->
    <div class="mfix-header-bottom-row">
        <nav class="mfix-mobile-nav-container">
            <a href="/" class="mfix-mobile-nav-item <?php echo is_active_link('index'); ?>">
                <i class="mfix-mobile-nav-icon fas fa-home"></i>
                <span class="mfix-mobile-nav-label">Home</span>
            </a>
            <a href="products" class="mfix-mobile-nav-item <?php echo is_active_link('products'); ?>">
                <i class="mfix-mobile-nav-icon fas fa-cogs"></i>
                <span class="mfix-mobile-nav-label">Products</span>
            </a>
            <a href="about" class="mfix-mobile-nav-item <?php echo is_active_link('about'); ?>">
                <i class="mfix-mobile-nav-icon fas fa-info-circle"></i>
                <span class="mfix-mobile-nav-label">About Us</span>
            </a>
             
            <a href="contact" class="mfix-mobile-nav-item <?php echo is_active_link('contact'); ?>">
                <i class="mfix-mobile-nav-icon fas fa-phone-alt"></i>
                <span class="mfix-mobile-nav-label">Contact</span>
            </a>
        </nav>
    </div>
</header>

<!--
    IMPORTANT: To prevent your page content from being hidden behind the fixed header,
    add the class "mfix-body-content-spacer" to your main content wrapper div.
    Example: <div class="mfix-body-content-spacer"> ... your page content here ... </div>
-->

</body>
</html>
