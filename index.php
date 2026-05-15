<?php
/**
 * Motorfix Injection Services - Homepage v4.1
 *
 * @version 4.1
 * @date Thursday, October 30, 2025
 *
 * --- V4.1 CHANGELOG (Client Slider Adjustment) ---
 * 1. [MODIFY] Hero slider content positioning:
 * - Removed all CTA buttons from slider
 * - Product name moved to the top of the image
 * - Description moved to the bottom (hidden on mobile)
 * - Reduced overlay opacity for better image visibility
 * - Added text-shadow/glow effect to product name for readability
 * - Maintained slider navigation (prev/next buttons and pagination dots)
 */

require_once 'db.php';
require_once 'head.php';

// [NEW] Static Hero Slides for Redesign
$hero_slides = [
    [
        'name' => 'Inline Injector Pumps',
        'description' => 'Robust and reliable inline injector pumps for various applications.',
        'image_path' => 'https://dash.motorfix.co.ke/media/products/prod_68ffa2a322751_1761583779.jpeg',
        'product_link' => 'products'
    ],
    [
        'name' => 'Distributor Injector Pumps',
        'description' => 'High-precision distributor (VE) pumps for efficient fuel delivery.',
        'image_path' => 'https://dash.motorfix.co.ke/media/products/prod_68ffa5b5df806_1761584565.jpeg',
        'product_link' => 'products'
    ],
    [
        'name' => 'Common Rail Injector Pumps',
        'description' => 'Advanced common rail pumps for modern diesel engine performance.',
        'image_path' => 'https://dash.motorfix.co.ke/media/products/prod_68ffa5dd5400d_1761584605.jpeg',
        'product_link' => 'products'
    ],
    [
        'name' => 'Nozzles',
        'description' => 'Precision-engineered nozzles for optimal fuel atomization.',
        'image_path' => 'https://dash.motorfix.co.ke/media/products/prod_68ffa61fa3ea6_1761584671.jpeg',
        'product_link' => 'products'
    ],
    [
        'name' => 'Unit Pumps',
        'description' => 'Durable unit pumps and unit injectors for heavy-duty engines.',
        'image_path' => 'https://dash.motorfix.co.ke/media/products/prod_68ffa63bb789d_1761584699.jpeg',
        'product_link' => 'products'
    ]
];

// Fetch Products for Scrollable Showcase
$showcase_products = [];
try {
    $stmt_showcase = $pdo->query("
        SELECT
            p.id,
            p.name,
            (SELECT pi.image_path
             FROM product_images pi
             WHERE pi.product_id = p.id
             ORDER BY pi.is_primary DESC, pi.id ASC
             LIMIT 1) as image_path
        FROM products p
        WHERE p.is_active = 1
        ORDER BY p.created_at DESC
        LIMIT 12
    ");
    $showcase_products = $stmt_showcase->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching showcase products: " . $e->getMessage());
}

// Array for Key Strengths section
$strengths = [
    ["type" => "icon", "icon" => "fa-warehouse", "title" => "Wholesale and Retail"],
    ["type" => "icon", "icon" => "fa-check-circle", "title" => "Genuine and Quality Parts"],
    ["type" => "logo", "logo_src" => "/Bosch-logo.png", "title" => "Bosch Authorized Wholesaler"],
    ["type" => "icon", "icon" => "fa-calendar-alt", "title" => "26+ Years of Experience"],
    ["type" => "icon", "icon" => "fa-star", "title" => "Competitive Prices"],
    ["type" => "icon", "icon" => "fa-check-circle", "title" => "Fast and Reliable Delivery"],
];

// [NEW] Vehicle Images for About Marquee
$vehicle_images = [
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/isuzu2.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/hillux.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/toyota-hiace.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/toyota-landcruzer.png'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/bull.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/actros.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/tata.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/holland.jpg'],
    ['alt' => '', 'src' => 'https://motorfix.co.ke/media/deere.jpg'],
];
$vehicle_images_loop = array_merge($vehicle_images, $vehicle_images);

// [REDESIGNED] Brand Logos Array
$brand_logos_list = [
    ['alt' => 'Bosch', 'src' => '/Bosch-logo.png'],
    ['alt' => 'CAT', 'src' => '/logo/cat.jpg'],
    ['alt' => 'logo', 'src' => '/logo/benz.jpg'],
    ['alt' => 'logo', 'src' => '/logo/cnhtc.jpg'],
    ['alt' => 'logo', 'src' => '/logo/cummins.jpg'],
    ['alt' => 'logo', 'src' => '/logo/delphi.jpg'],
    ['alt' => 'logo', 'src' => '/logo/denso.jpg'],
    ['alt' => 'logo', 'src' => '/logo/deutz.jpg'],
    ['alt' => 'logo', 'src' => '/logo/doosan.jpg'],
    ['alt' => 'logo', 'src' => '/logo/hino.jpg'],
    ['alt' => 'logo', 'src' => '/logo/hyundai.jpg'],
    ['alt' => 'logo', 'src' => '/logo/ISUZU.jpg'],
    ['alt' => 'logo', 'src' => '/logo/iveco.jpg'],
    ['alt' => 'logo', 'src' => '/logo/jcb.jpg'],
    ['alt' => 'logo', 'src' => '/logo/kobelco.jpg'],
    ['alt' => 'logo', 'src' => '/logo/komatsu.jpg'],
    ['alt' => 'logo', 'src' => '/logo/kubota.jpg'],
    ['alt' => 'logo', 'src' => '/logo/MAN.jpg'],
    ['alt' => 'logo', 'src' => '/logo/mitsu.jpg'],
    ['alt' => 'logo', 'src' => '/logo/nissan.jpg'],
    ['alt' => 'logo', 'src' => '/logo/perkins.jpg'],
    ['alt' => 'logo', 'src' => '/logo/renault.jpg'],
    ['alt' => 'logo', 'src' => '/logo/scania.jpg'],
    ['alt' => 'logo', 'src' => '/logo/toyota.jpg'],
    ['alt' => 'logo', 'src' => '/logo/Volvo.jpg'],
    ['alt' => 'logo', 'src' => '/logo/vw.jpg'],
    ['alt' => 'logo', 'src' => '/logo/XCMG.jpg'],
    ['alt' => 'logo', 'src' => '/logo/yanmar.jpg'],
];

$brands = [];
foreach ($brand_logos_list as $logo) {
    $brands[] = ["type" => "image", "src" => $logo['src'], "alt" => $logo['alt']];
}

// --- SEO Meta Tags ---
$page_title = 'Motorfix Injection Services';
$meta_description = 'Your trusted source for genuine, high-quality replacement injector pump spare parts in Kenya. Bosch Authorized Wholesaler with 26+ years of experience.';
?>

<script>
    document.title = <?php echo json_encode($page_title); ?>;
    
    var metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) {
        metaDesc.content = <?php echo json_encode($meta_description); ?>;
    } else {
        metaDesc = document.createElement('meta');
        metaDesc.name = 'description';
        metaDesc.content = <?php echo json_encode($meta_description); ?>;
        document.head.appendChild(metaDesc);
    }
</script>

<style>
    html {
        scroll-behavior: smooth;
    }

    body, .mfix-home-wrapper *,
    .mfix-home-wrapper *::before,
    .mfix-home-wrapper *::after {
        box-sizing: border-box;
    }
    .mfix-body-content-spacer {
        font-family: 'Inter', sans-serif;
        background-color: var(--mfix-white);
    }

    :root {
        --mfix-dark-green: #0A4A2A;
        --mfix-green: #28a745;
        --mfix-black: #1a1a1a;
        --mfix-grey: #6c757d;
        --mfix-light-grey: #f8f9fa;
        --mfix-white: #ffffff;
        --mfix-border: #e5e5e5;
        --mfix-shadow: rgba(0, 0, 0, 0.08);
        --mfix-shadow-medium: rgba(0, 0, 0, 0.12);
    }

    .mfix-home-section {
        padding: 60px 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    .mfix-home-section.full-width {
        max-width: none;
        padding-left: 0;
        padding-right: 0;
    }
     .mfix-home-section.bg-light {
        background-color: var(--mfix-light-grey);
     }
     .mfix-home-section.bg-dark {
        background-color: var(--mfix-dark-green);
        color: var(--mfix-white);
     }
      .mfix-home-section.bg-dark .mfix-section-title,
      .mfix-home-section.bg-dark .mfix-section-subtitle {
         color: var(--mfix-white);
      }
      .mfix-home-section.bg-dark h3 {
          color: var(--mfix-white);
      }

    .mfix-section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--mfix-dark-green);
        margin-bottom: 20px;
        text-align: center;
    }
    .mfix-section-subtitle {
        font-size: 1.1rem;
        color: var(--mfix-grey);
        text-align: center;
        max-width: 700px;
        margin: 0 auto 40px auto;
        line-height: 1.7;
    }

     /* --- Hero Slider (v4.1 - MODIFIED LAYOUT) --- */
    .mfix-home-hero {
        padding: 0;
    }
    .mfix-hero-slider-container {
        position: relative;
        width: 100%;
        height: 550px;
    }
    
    #mfix-hero-slider {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .mfix-slider-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
    }
    .mfix-slider-slide {
        text-align: center;
        font-size: 18px;
        display: flex;
        justify-content: space-between; /* Changed from center to space-between */
        align-items: stretch; /* Changed to stretch */
        flex-direction: column; /* Added column direction */
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        color: var(--mfix-white);
        overflow: hidden; 
        background-color: var(--mfix-white);
        
        opacity: 0;
        transition: opacity 0.6s ease-in-out;
        z-index: 1;
    }
    .mfix-slider-slide.is-active {
        opacity: 1;
        z-index: 2;
    }
    
    .hero-slide-image {
        position: absolute;
        inset: 0;
        width: 100%;
        padding-top: 20px;
        height: 100%;
        object-fit: contain;
        z-index: 0;
    }
    
    .hero-slide-image.loading {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .hero-slide-image.loaded {
        opacity: 1;
    }

    /* [MODIFIED] Reduced overlay opacity from 0.5-0.7 to 0.15-0.25 */
    .mfix-slider-slide::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(rgba(0,0,0,0.15), rgba(0,0,0,0.25));
        z-index: 1;
    }
    
    /* [MODIFIED] New layout: Product name at top, description at bottom */
    .mfix-slide-content {
        position: relative;
        z-index: 2;
        padding: 20px 40px;
        max-width: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
    }
    
    /* [NEW] Product name styling - positioned at top with glow effect */
    .mfix-slide-content h2 {
        font-size: 2.8rem;
       
        font-weight: 700;
        margin: 0;
        text-align: center;
        /* Multiple text shadows for glow effect */
        text-shadow: 
            0 0 10px rgba(0,0,0,0.9),
            0 0 20px rgba(0,0,0,0.8),
            0 0 30px rgba(0,0,0,0.7),
            0 2px 5px rgba(0,0,0,0.6),
            2px 2px 8px rgba(0,0,0,0.5);
        align-self: flex-start; /* Align to top */
        width: 100%;
    }
    
    /* [NEW] Description styling - positioned at bottom, hidden on mobile */
    .mfix-slide-content p {
        font-size: 1.15rem;
        margin: 0;
        line-height: 1.7;
        opacity: 0.95;
        text-align: center;
        /* Text shadow for readability */
        text-shadow: 
            0 0 8px rgba(0,0,0,0.8),
            0 2px 4px rgba(0,0,0,0.6);
        align-self: flex-end; /* Align to bottom */
        width: 100%;
        display: none; /* Hidden by default (mobile) */
    }
    
    /* [REMOVED] All CTA button styles as requested */
    
    /* [MAINTAINED] Custom Slider Navigation/Pagination */
    .mfix-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
        color: var(--mfix-white);
        transition: transform 0.3s ease;
        cursor: pointer;
        padding: 10px;
        font-size: 1.5rem;
    }
    .mfix-slider-nav:hover {
        transform: translateY(-50%) scale(1.1);
    }
    .mfix-slider-prev {
        left: 15px;
    }
    .mfix-slider-next {
        right: 15px;
    }
    .mfix-slider-nav::before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        display: inline-block;
    }
    .mfix-slider-prev::before {
        content: "\f053";
    }
    .mfix-slider-next::before {
        content: "\f054";
    }
    
    .mfix-slider-pagination {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        gap: 10px;
    }
    .mfix-pagination-dot {
        width: 12px;
        height: 12px;
        background: rgba(255, 255, 255, 0.6);
        opacity: 1;
        transition: all 0.3s ease;
        border-radius: 50%;
        cursor: pointer;
        border: none;
        padding: 0;
    }
    .mfix-pagination-dot.is-active {
        background: var(--mfix-white);
        transform: scale(1.2);
    }

    /* --- About Intro Section --- */
    .mfix-about-intro-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
        align-items: center;
    }
    .mfix-about-intro-text h2 {
         font-size: 2rem;
         font-weight: 700;
         color: var(--mfix-dark-green);
         margin: 0 0 15px 0;
    }
    .mfix-about-intro-text p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--mfix-black);
        margin-bottom: 25px;
    }
    .mfix-about-intro-img {
        width: 100%;
        height: 400px;
        border-radius: 12px;
        box-shadow: 0 8px 25px var(--mfix-shadow-medium);
        overflow: hidden;
        position: relative;
        background-color: var(--mfix-light-grey);
    }
    
    .mfix-vehicle-marquee-container {
         padding: 0;
    }
    .mfix-vehicle-marquee-wrapper {
        display: flex;
        width: 100%;
        height: 100%;
    }
    .mfix-vehicle-marquee {
        display: flex;
        align-items: center;
        animation: marquee-scroll-vehicles 60s linear infinite;
        flex-shrink: 0;
        height: 100%;
    }
    .mfix-vehicle-marquee-wrapper:hover .mfix-vehicle-marquee {
        animation-play-state: paused;
    }
    .mfix-vehicle-slide {
        flex: 0 0 auto;
        width: 300px;
        height: 100%;
        margin: 0 10px;
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }
    .mfix-vehicle-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    .mfix-vehicle-slide:hover img {
        transform: scale(1.05);
    }
    .mfix-vehicle-slide span {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: var(--mfix-white);
        padding: 20px 15px 15px 15px;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    @keyframes marquee-scroll-vehicles {
        0% { transform: translateX(0%); }
        100% { transform: translateX(-100%); }
    }

    .mfix-about-intro-cta {
        margin-top: 15px;
    }

    /* --- Key Strengths Grid --- */
    .mfix-strengths-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 25px;
    }
    .mfix-strength-item {
        background: var(--mfix-white);
        border-radius: 10px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 15px var(--mfix-shadow);
        border: 1px solid var(--mfix-border);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .mfix-strength-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px var(--mfix-shadow-medium);
    }
    .mfix-strength-item-icon {
        font-size: 2.5rem;
        color: var(--mfix-dark-green);
        margin-bottom: 15px;
        height: 45px;
        line-height: 45px;
    }
    .mfix-strength-item-logo {
        max-height: 45px;
        width: auto;
        margin-bottom: 15px;
    }
    .mfix-strength-item h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--mfix-black);
        margin: 0;
        line-height: 1.4;
    }

    /* --- Product Showcase (Scrollable) --- */
    .mfix-product-showcase-wrapper {
        position: relative;
        padding: 0;
    }
     .mfix-product-scroll-container {
        display: flex;
        overflow-x: auto;
        padding: 10px 20px 30px 20px;
        gap: 20px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: var(--mfix-grey) var(--mfix-light-grey);
    }
    .mfix-product-scroll-container::-webkit-scrollbar { height: 8px; }
    .mfix-product-scroll-container::-webkit-scrollbar-track { background: var(--mfix-light-grey); border-radius: 4px; }
    .mfix-product-scroll-container::-webkit-scrollbar-thumb { background: var(--mfix-grey); border-radius: 4px; }
    .mfix-product-scroll-container::-webkit-scrollbar-thumb:hover { background: var(--mfix-dark-green); }

    .mfix-product-card {
        flex: 0 0 240px;
        scroll-snap-align: start;
        border: 1px solid var(--mfix-border);
        border-radius: 10px;
        overflow: hidden;
        background: var(--mfix-white);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .mfix-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px var(--mfix-shadow-medium);
    }
    .mfix-product-card-image-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        background-color: var(--mfix-white);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .mfix-product-card img {
        display: block;
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    
    .mfix-product-card img.loading {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .mfix-product-card img.loaded {
        opacity: 1;
    }
    
    .mfix-product-card h3 {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--mfix-black);
        margin: 0;
        padding: 12px 15px;
        text-align: center;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: calc(1.4em * 2 + 24px);
        flex-grow: 1;
    }
    .mfix-product-card a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .mfix-view-products-cta {
        text-align: center;
        margin-top: 40px;
        padding: 20px;
        background-color: var(--mfix-dark-green);
        border-radius: 12px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    .mfix-view-products-cta p {
        color: var(--mfix-light-grey);
        font-size: 1.1rem;
        margin: 0 0 20px 0;
    }
    .mfix-view-products-cta .mfx-btn {
        background-color: var(--mfix-white);
        color: var(--mfix-dark-green);
        padding: 14px 35px;
        font-size: 1.1rem;
    }
     .mfix-view-products-cta .mfx-btn:hover {
        background-color: var(--mfix-light-grey);
        color: var(--mfix-black);
        transform: scale(1.05);
     }

    /* --- Brand Marquee --- */
    .mfix-brands-section {
        overflow: hidden;
        padding: 50px 0;
    }
    .mfix-marquee-wrapper {
        display: flex;
        width: max-content;
    }
    .mfix-marquee {
        display: flex;
        align-items: center;
        animation: marquee-scroll 40s linear infinite;
        flex-shrink: 0;
    }
     .mfix-marquee-wrapper:hover .mfix-marquee {
        animation-play-state: paused;
     }
    .mfix-brand-logo {
        height: 150px;
        width: auto;
        max-width: 150px;
        margin: 0 40px;
        filter: grayscale(10%);
        opacity: 1;
        transition: filter 0.3s ease, opacity 0.3s ease;
        display: block;
    }
    .mfix-brand-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
    }
    @keyframes marquee-scroll {
        0% { transform: translateX(0%); }
        100% { transform: translateX(-100%); }
    }

    /* --- General Button Styles --- */
    .mfx-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        line-height: 1.5;
    }
    .mfx-btn-green {
        background: var(--mfix-dark-green);
        color: var(--mfix-white);
    }
    .mfx-btn-green:hover {
        background: var(--mfix-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    }

    /* --- Responsive Adjustments --- */
    @media (min-width: 768px) {
        .mfix-about-intro-grid {
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        .mfix-product-card {
             flex-basis: 260px;
        }
        /* [NEW] Show description on larger screens */
        .mfix-slide-content p {
            display: block;
        }
    }
    @media (max-width: 991px) {
        .mfix-hero-slider-container { height: 450px; }
        .mfix-slide-content h2 { font-size: 2.2rem; }
        .mfix-slide-content p { font-size: 1.05rem; }
        .mfix-section-title { font-size: 2rem; }
        .mfix-section-subtitle { font-size: 1rem; }
        .mfix-about-intro-text h2 { font-size: 1.8rem; }
    }
    @media (max-width: 767px) {
        .mfix-home-section { padding: 40px 15px; }
        .mfix-about-intro-img { 
            order: -1;
            height: 300px;
        } 
        .mfix-vehicle-slide {
            width: 240px;
        }
        .mfix-hero-slider-container { height: 400px; }
        .mfix-slide-content h2 { font-size: 1.8rem; }
        .mfix-slide-content { padding: 5px 20px; } /* Reduced padding */
        .mfix-strengths-grid { grid-template-columns: 1fr 1fr; gap: 15px; }
        .mfix-strength-item { padding: 20px 15px; }
        .mfix-product-scroll-container { padding: 10px 15px 20px 15px; gap: 15px; }
        .mfix-product-card { flex-basis: 180px; }
        .mfix-brand-logo { height: 40px; margin: 0 30px; }
    }
    @media (max-width: 480px) {
        .mfix-hero-slider-container { height: 350px; }
        .mfix-slide-content h2 { font-size: 1.6rem; }
        .mfix-section-title { font-size: 1.6rem; }
        .mfix-section-subtitle { font-size: 0.95rem; }
        .mfix-strength-item h3 { font-size: 1rem; }
        .mfix-product-card { flex-basis: 160px; }
        .mfix-brand-logo { height: 35px; margin: 0 20px; }
    }

</style>

<div class="mfix-body-content-spacer mfix-home-wrapper">

    <!-- Hero Slideshow -->
    <section class="mfix-home-section full-width mfix-home-hero">
        <div class="mfix-hero-slider-container">
            <div id="mfix-hero-slider">
                <div class="mfix-slider-wrapper">
                    <?php if (!empty($hero_slides)): ?>
                        <?php foreach ($hero_slides as $index => $slide): ?>
                            <?php
                                $image_url = $slide['image_path'];
                                $product_link = $slide['product_link'];
                                $escaped_image_url = htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="mfix-slider-slide"> 
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
                                     data-src="<?php echo $escaped_image_url; ?>"
                                     alt="<?php echo htmlspecialchars($slide['name']); ?>" 
                                     class="hero-slide-image loading"
                                     onerror="this.src='https://placehold.co/1920x550/eeeeee/555555?text=<?php echo urlencode($slide['name']); ?>'">
                                
                                <div class="mfix-slide-content">
                                    <h2><?php echo htmlspecialchars($slide['name']); ?></h2>
                                    <p><?php echo htmlspecialchars($slide['description']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="mfix-slider-slide">
                            <img src="media/motorfix.jpeg" alt="Motorfix Workshop" class="hero-slide-image loaded" 
                                 onerror="this.src='https://placehold.co/1920x550/0A4A2A/FFFFFF?text=Motorfix+Injection+Services'">
                             <div class="mfix-slide-content">
                                 <h2>Motorfix Injection Services</h2>
                                 <p>Your premier source for genuine injector pump spare parts in Kenya.</p>
                             </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mfix-slider-pagination"></div>
                <div class="mfix-slider-nav mfix-slider-prev" aria-label="Previous slide"></div>
                <div class="mfix-slider-nav mfix-slider-next" aria-label="Next slide"></div>
            </div>
        </div>
    </section>

    <!-- About Us Intro -->
    <section class="mfix-home-section">
        <div class="mfix-about-intro-grid">
            <div class="mfix-about-intro-text">
                <h2>Your Partner in Engine Performance</h2>
                <p>
                    Motorfix Injection Services is Kenya's leading importer of genuine and quality replacement injector pump spare parts. 
We strive to ensure that every engine receives the excellence it deserves through reliable and high performance components backed by 26+ years of expertise.
                </p>
                <div class="mfix-about-intro-cta">
                    <a href="about" class="mfx-btn mfx-btn-green">
                        <i class="fas fa-info-circle"></i> Learn More About Us
                    </a>
                </div>
            </div>
            <div class="mfix-about-intro-img mfix-vehicle-marquee-container">
                <div class="mfix-vehicle-marquee-wrapper">
                    <div class="mfix-vehicle-marquee">
                        <?php foreach ($vehicle_images_loop as $image): ?>
                            <div class="mfix-vehicle-slide">
                                <img src="<?php echo htmlspecialchars($image['src']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt']); ?>"
                                     onerror="this.src='https://placehold.co/300x225/eeeeee/555555?text=<?php echo urlencode($image['alt']); ?>'">
                                <span><?php echo htmlspecialchars($image['alt']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mfix-vehicle-marquee" aria-hidden="true">
                        <?php foreach ($vehicle_images_loop as $image): ?>
                            <div class="mfix-vehicle-slide">
                                <img src="<?php echo htmlspecialchars($image['src']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt']); ?>"
                                     onerror="this.src='https://placehold.co/300x225/eeeeee/555555?text=<?php echo urlencode($image['alt']); ?>'">
                                <span><?php echo htmlspecialchars($image['alt']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Strengths Section -->
    <section class="mfix-home-section bg-light">
        <h2 class="mfix-section-title">Why Choose Motorfix?</h2>
        <p class="mfix-section-subtitle">Decades of experience and commitment to quality make us the preferred choice.</p>
        <div class="mfix-strengths-grid">
             <?php foreach ($strengths as $item): ?>
                <div class="mfix-strength-item">
                    <?php if (isset($item['type']) && $item['type'] == 'logo'): ?>
                        <img src="<?php echo htmlspecialchars($item['logo_src']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="mfix-strength-item-logo" onerror="this.style.display='none'">
                    <?php else: ?>
                        <i class="fas <?php echo htmlspecialchars($item['icon']); ?> mfix-strength-item-icon"></i>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product Showcase -->
    <?php if (!empty($showcase_products)): ?>
    <section class="mfix-home-section">
         <h2 class="mfix-section-title">Featured Products</h2>
         <p class="mfix-section-subtitle">A selection from our extensive range of parts we deal with.</p>
         <div class="mfix-product-showcase-wrapper">
             <div class="mfix-product-scroll-container">
                 <?php foreach ($showcase_products as $product): ?>
                      <?php
                          $image_url = '/u/media/products/' . ($product['image_path'] ?: 'placeholder-product.png');
                          $product_link = '/product?id=' . $product['id'];
                          $escaped_image_url_showcase = htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8');
                      ?>
                      <div class="mfix-product-card">
                          <a href="<?php echo htmlspecialchars($product_link); ?>">
                              <div class="mfix-product-card-image-box">
                                   <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
                                        data-src="<?php echo $escaped_image_url_showcase; ?>" 
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="loading">
                              </div>
                              <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                          </a>
                      </div>
                 <?php endforeach; ?>
             </div>
         </div>
         <div class="mfix-view-products-cta">
            <p>Explore our full catalog for all your injector pump needs.</p>
             <a href="products" class="mfx-btn">
                 <i class="fas fa-th-large"></i> View All Products
             </a>
         </div>
    </section>
    <?php endif; ?>

    <!-- Brand Marquee -->
    <section class="mfix-home-section bg-light mfix-brands-section">
         <h2 class="mfix-section-title">Brands We Deal In</h2>
         <div class="mfix-marquee-wrapper">
             <div class="mfix-marquee">
                 <?php foreach ($brands as $brand): ?>
                    <?php if ($brand['type'] == 'image'): ?>
                       <img src="<?php echo htmlspecialchars($brand['src']); ?>" alt="<?php echo htmlspecialchars($brand['alt']); ?>" class="mfix-brand-logo"
                            onerror="this.src='https://placehold.co/150x60/f8f9fa/6c757d?text=<?php echo urlencode($brand['alt']); ?>'">
                    <?php endif; ?>
                 <?php endforeach; ?>
             </div>
             <div class="mfix-marquee" aria-hidden="true">
                 <?php foreach ($brands as $brand): ?>
                     <?php if ($brand['type'] == 'image'): ?>
                        <img src="<?php echo htmlspecialchars($brand['src']); ?>" alt="<?php echo htmlspecialchars($brand['alt']); ?>" class="mfix-brand-logo"
                             onerror="this.src='https://placehold.co/150x60/f8f9fa/6c757d?text=<?php echo urlencode($brand['alt']); ?>'">
                     <?php endif; ?>
                 <?php endforeach; ?>
             </div>
         </div>
    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        function loadImage(imgElement, placeholderSrc) {
            if (imgElement.classList.contains('loaded') || !imgElement.dataset.src) {
                imgElement.classList.remove('loading');
                return;
            }
            
            const realSrc = imgElement.dataset.src;
            
            if (realSrc.startsWith('/path/to/your/')) {
                 imgElement.src = placeholderSrc;
                 imgElement.classList.remove('loading');
                 imgElement.classList.add('loaded');
                 if (imgElement.closest('.mfix-product-card-image-box')) {
                    imgElement.parentElement.style.backgroundColor = '#eee';
                 }
                 if (imgElement.classList.contains('hero-slide-image')) {
                     const altText = imgElement.alt || 'Placeholder';
                     imgElement.src = 'https://placehold.co/1920x550/eeeeee/555555?text=' + encodeURIComponent(altText);
                 }
                 return;
            }
            
            if (realSrc.includes('placeholder-product.png')) {
                 imgElement.src = placeholderSrc;
                 imgElement.classList.remove('loading');
                 imgElement.classList.add('loaded');
                 if (imgElement.closest('.mfix-product-card-image-box')) {
                    imgElement.parentElement.style.backgroundColor = '#eee';
                 }
                 return;
            }

            const tempImage = new Image();
            
            tempImage.onload = function() {
                imgElement.src = realSrc;
                imgElement.classList.remove('loading');
                imgElement.classList.add('loaded');
            };
            
            tempImage.onerror = function() {
                imgElement.src = placeholderSrc;
                imgElement.classList.remove('loading');
                imgElement.classList.add('loaded');
                
                if (imgElement.closest('.mfix-product-card-image-box')) {
                    imgElement.parentElement.style.backgroundColor = '#eee';
                }
                
                 if (imgElement.classList.contains('hero-slide-image')) {
                     const altText = imgElement.alt || 'Image Not Found';
                     imgElement.src = 'https://placehold.co/1920x550/eeeeee/555555?text=' + encodeURIComponent(altText);
                 }
                
                console.error('Failed to load image:', realSrc, 'Falling back to placeholder.');
            };
            
            tempImage.src = realSrc;
        }

        const imagesToLoad = document.querySelectorAll('img.loading[data-src]');
        
        imagesToLoad.forEach(function(img) {
            let placeholder = '/media/placeholder-product.png';
            loadImage(img, placeholder);
        });

        const slider = document.getElementById('mfix-hero-slider');
        if (slider) {
            const slides = slider.querySelectorAll('.mfix-slider-slide');
            const nextBtn = slider.querySelector('.mfix-slider-next');
            const prevBtn = slider.querySelector('.mfix-slider-prev');
            const paginationContainer = slider.querySelector('.mfix-slider-pagination');
            let paginationDots = [];

            let currentSlide = 0;
            let slideInterval;
            const autoplayDelay = 5000;

            function createPagination() {
                if (!paginationContainer) return;
                paginationContainer.innerHTML = '';
                slides.forEach((slide, index) => {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'mfix-pagination-dot';
                    dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
                    dot.addEventListener('click', () => {
                        goToSlide(index);
                        resetAutoplay();
                    });
                    paginationContainer.appendChild(dot);
                    paginationDots.push(dot);
                });
            }

            function goToSlide(slideIndex) {
                if (slides.length === 0) return;
                
                if (slideIndex >= slides.length) {
                    slideIndex = 0;
                }
                if (slideIndex < 0) {
                    slideIndex = slides.length - 1;
                }

                slides[currentSlide].classList.remove('is-active');
                if (paginationDots[currentSlide]) {
                    paginationDots[currentSlide].classList.remove('is-active');
                }

                currentSlide = slideIndex;
                slides[currentSlide].classList.add('is-active');
                if (paginationDots[currentSlide]) {
                    paginationDots[currentSlide].classList.add('is-active');
                }
            }

            function nextSlide() {
                goToSlide(currentSlide + 1);
            }

            function prevSlide() {
                goToSlide(currentSlide - 1);
            }

            function startAutoplay() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, autoplayDelay);
            }

            function resetAutoplay() {
                clearInterval(slideInterval);
                startAutoplay();
            }

            if (slides.length > 1) {
                createPagination();

                if(nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        nextSlide();
                        resetAutoplay();
                    });
                }
                if(prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        prevSlide();
                        resetAutoplay();
                    });
                }
                
                let scrollTimer;
                document.addEventListener('scroll', () => {
                    clearInterval(slideInterval);
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => {
                        startAutoplay();
                    }, 3000);
                }, { passive: true });

                startAutoplay();
            }
            
            if (slides.length > 0) {
                 goToSlide(0);
            }
        }
    });
</script>

<?php
require_once 'foot.php';
?>