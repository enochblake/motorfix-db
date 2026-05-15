<?php



require_once 'head.php';


$strengths = [
    ["type" => "icon", "icon" => "fa-check-circle", "title" => "Genuine Parts"],
    ["type" => "icon", "icon" => "fa-star", "title" => "Quality Parts"],
    ["type" => "logo", "logo_src" => "/Bosch-logo.png", "title" => "Bosch Authorized Wholesaler"], // Uses logo
    ["type" => "icon", "icon" => "fa-calendar-alt", "title" => "26+ Years of Experience"] // Updated
];


$page_title = 'About Us - Motorfix Injection Services';
$meta_description = 'Learn about Motorfix Injection Services. With 26 years of experience, we are importers of genuine, high-quality injector pump spare parts for trucks, tractors, and vehicles in Kenya.';
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
    
    body, .mfx-about-v2-wrapper *,
    .mfx-about-v2-wrapper *::before,
    .mfx-about-v2-wrapper *::after {
        box-sizing: border-box;
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
    }

    
    .mfix-body-content-spacer {
        font-family: 'Inter', sans-serif;
    }

    
    .mfx-about-hero {
        /*  */
        background: linear-gradient(rgba(10, 74, 42, 0.6), rgba(10, 74, 42, 0.6)), url('media/eimage.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; 
        padding: 80px 20px;
        text-align: center;
        color: var(--mfix-white);
    }
    .mfx-about-hero h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .mfx-about-hero p {
        font-size: 1.15rem;
        font-weight: 400;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* --- Main Content Wrapper --- */
    .mfx-about-v2-wrapper {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px; /* Page padding */
    }

    /* --- Standard Section --- */
    .mfx-about-section {
        padding: 50px 0; /* Vertical spacing */
    }

    .mfx-section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--mfix-black);
        margin-bottom: 30px;
        text-align: center;
    }

    /* --- Key Strengths Grid --- */
    .mfx-strengths-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .mfx-strength-item {
        background: var(--mfix-white);
        border: 1px solid var(--mfix-border);
        border-radius: 8px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 15px var(--mfix-shadow);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 160px;
    }
    .mfx-strength-item-icon {
        font-size: 2.5rem;
        color: var(--mfix-dark-green);
        margin-bottom: 15px;
        height: 45px;
    }
    .mfx-strength-item-logo {
        max-height: 45px;
        width: auto;
        margin-bottom: 15px;
    }
    .mfx-strength-item h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--mfix-black);
        margin: 0;
    }

    /* --- Our Mission Section --- */
    .mfx-mission-section {
        position: relative;
        overflow: hidden; /* Clips the pseudo-elements */
        padding: 60px 20px;
    }
    .mfx-mission-section::before {
        /* The blurred background image */
        content: '';
        position: absolute;
        top: -10px; left: -10px; right: -10px; bottom: -10px;
        background: url('media/isuzu.jpg') center/cover;
        filter: blur(1px);
        z-index: 1;
    }
     .mfx-mission-section::after {
        /* The translucent overlay */
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(248, 249, 250, 0.77); /* Light grey with 92% opacity */
        z-index: 2;
    }
    .mfx-mission-content {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        position: relative; /* Sits on top of the overlays */
        z-index: 3;
    }
    .mfx-mission-content p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--mfix-black);
        margin: 0;
    }

    
    .mfx-about-cta {
        background: var(--mfix-dark-green);
        padding: 50px 20px;
        border-radius: 12px;
        text-align: center;
        margin: 30px 0; /* Margin inside wrapper */
    }
    .mfx-about-cta h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--mfix-white);
        margin: 0 0 15px 0;
    }
    .mfx-about-cta p {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0 0 30px 0;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .mfx-cta-buttons {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
    .mfx-about-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 30px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid var(--mfix-white);
        width: 100%;
        max-width: 300px;
    }
    .mfx-about-btn-primary {
        background: var(--mfix-white);
        color: var(--mfix-dark-green);
    }
    .mfx-about-btn-primary:hover {
        background: var(--mfix-light-grey);
    }
    .mfx-about-btn-secondary {
        background: transparent;
        color: var(--mfix-white);
    }
    .mfx-about-btn-secondary:hover {
        background: rgba(255,255,255,0.1);
    }
    /* Instagram brand icon color */
    .mfx-about-btn-secondary .fa-instagram {
        color: #E1306C;
    }


  
    @media (min-width: 768px) {
        .mfx-strengths-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        .mfx-cta-buttons {
            flex-direction: row;
            justify-content: center;
            flex-wrap: wrap; /* Allow wrapping if needed */
        }
        .mfx-about-btn {
            width: auto;
        }
    }

    @media (max-width: 480px) {
        .mfx-about-hero h1 {
            font-size: 2.2rem;
        }
        .mfx-about-hero p {
            font-size: 1.05rem;
        }
        .mfx-section-title {
            font-size: 1.8rem;
        }
    }

</style>


<div class="mfix-body-content-spacer">

    <!-- Hero Section -->
    <section class="mfx-about-hero">
        <h1>Welcome to Motorfix Injection Services</h1>
        <p>
            Your trusted importers of genuine and high-quality replacement injector pump spare parts. 
            We are specialists dedicated to delivering reliability and performance, part by part.
        </p>
    </section>

    <!-- Main Content -->
    <div class="mfx-about-v2-wrapper">

        <!-- Key Strengths Section -->
        <section class="mfx-about-section">
            <div class="mfx-strengths-grid">
                <?php foreach ($strengths as $item): ?>
                    <div class="mfx-strength-item">
                        <?php if ($item['type'] == 'icon'): ?>
                            <i class="fas <?php echo htmlspecialchars($item['icon']); ?> mfx-strength-item-icon"></i>
                        <?php elseif ($item['type'] == 'logo'): ?>
                            <img src="<?php echo htmlspecialchars($item['logo_src']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="mfx-strength-item-logo"
                                 onerror="this.style.display='none'">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Our Mission Section -->
        <section class="mfx-mission-section">
            <div class="mfx-mission-content">
                <h2 class="mfx-section-title">Our Mission</h2>
                <p>
                   ‎To keep diesel systems moving and maximize operational efficiency.
                </p> <br>
                
                     <h2 class="mfx-section-title">Our Vision</h2>
                <p>
                   ‎To contribute to a better world by powering economic progress through superior diesel solutions.
                </p>
            </div>
        </section>
    
    </div> 

    
    <section class="mfx-about-cta">
        <h2>Ready to Find Your Part?</h2>
        <p>Our experts are ready to help you find the exact part you need. Browse our products or get in touch today.</p>
        <div class="mfx-cta-buttons">
            <a href="products" class="mfx-about-btn mfx-about-btn-primary">
                <i class="fas fa-cogs"></i> Browse Products
            </a>
            <a href="contact" class="mfx-about-btn mfx-about-btn-secondary">
                <i class="fas fa-phone-alt"></i> Contact Us Now
            </a>
            <a href="https://www.instagram.com/motorfix_injector.services?utm_source=qr&igsh=MTNqNnhhenlrZ21oOQ==" class="mfx-about-btn mfx-about-btn-secondary" target="_blank" rel="noopener">
                <i class="fab fa-instagram"></i> Follow us
            </a>
        </div>
    </section>

</div> 

<?php 

require_once 'foot.php'; 
?>

