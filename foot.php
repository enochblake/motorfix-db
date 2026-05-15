<?php
/**
 * Motorfix Injection Services - Professional Footer v1.1
 *
 * A clean, responsive, and self-contained footer for the Motorfix website.
 *
 * Features:
 * - Minimalistic design with Motorfix branding and color scheme.
 * - 3-column grid on all screen sizes, including mobile.
 * - Scoped CSS classes (prefixed with `mfix-footer-`) to prevent conflicts.
 * - Includes quick links, branch location (clickable to Google Maps), contact info (clickable tel/mail).
 * - Bosch Authorized Wholesaler section with logo.
 * - Tagline, legal links, and dynamic copyright year.
 * - No dependencies beyond what's in header.
 *
 * @version 1.1
 * @date Wednesday, October 22, 2025
 */
?>
<footer class="mfix-footer-main">
    <!-- Bosch Authorized Wholesaler Section -->
    <div class="mfix-footer-bosch">
        <img src="/Bosch-logo.png" alt="BOSCH Logo" class="mfix-footer-bosch-logo">
        <span class="mfix-footer-bosch-text">BOSCH Authorized Wholesaler (Automotive)</span>
    </div>

    <!-- 3-Column Grid: Quick Links, Branch, Contact -->
    <div class="mfix-footer-grid">
        <!-- Quick Links -->
        <div class="mfix-footer-grid-item">
            <h3 class="mfix-footer-grid-title">
                <i class="fas fa-th-large mfix-footer-icon"></i>
                Quick Links
            </h3>
            <ul class="mfix-footer-list">
                <li><a href="/" class="mfix-footer-link"><i class="fas fa-home mfix-footer-small-icon"></i>Home</a></li>
                <li><a href="about" class="mfix-footer-link"><i class="fas fa-info-circle mfix-footer-small-icon"></i>About Us</a></li>
                <li><a href="products" class="mfix-footer-link"><i class="fas fa-newspaper mfix-footer-small-icon"></i>Products</a></li>
                <li><a href="contact" class="mfix-footer-link"><i class="fas fa-phone-alt mfix-footer-small-icon"></i>Contact</a></li>
            </ul>
        </div>

        <!-- Main Branch Location -->
        <div class="mfix-footer-grid-item">
            <h3 class="mfix-footer-grid-title">
                <i class="fas fa-map-marker-alt mfix-footer-icon"></i>
                Main Branch
            </h3>
            <p class="mfix-footer-branch">
                <a href="https://maps.app.goo.gl/bR8g1knF9K6K4GHY8" target="_blank" rel="noopener" class="mfix-footer-branch-link">
                    Jekima Plaza,<br><br>Jogoo Road,<br><br>Nairobi - Kenya
                </a>
            </p>
        </div>

        <!-- Contact Info -->
        <div class="mfix-footer-grid-item">
            <h3 class="mfix-footer-grid-title">
                <i class="fas fa-envelope mfix-footer-icon"></i>
                Contact Info
            </h3>
            <ul class="mfix-footer-list">
                <li><a href="tel:+254725044914" class="mfix-footer-link"><i class="fas fa-phone mfix-footer-small-icon"></i>+254 725 044 914</a></li>
                <li><a href="tel:+254716959975" class="mfix-footer-link"><i class="fas fa-phone mfix-footer-small-icon"></i>+254 716 959 975</a></li>
                <li><a href="tel:+254727437207" class="mfix-footer-link"><i class="fas fa-phone mfix-footer-small-icon"></i>+254 727 437 207</a></li>
                <li><a href="tel:+254782654041" class="mfix-footer-link"><i class="fas fa-phone mfix-footer-small-icon"></i>+254 705 346 201</a></li>
                <li><a href="mailto:info@motorfix.co.ke" class="mfix-footer-link"><i class="fas fa-envelope mfix-footer-small-icon"></i>info@motorfix.co.ke</a></li>
            </ul>
        </div>
    </div>

    <!-- Tagline -->
    <div class="mfix-footer-tagline">
        maintaining injection systems for proper performance
    </div>

    <!-- Legal Links Row -->

    <!-- Copyright -->
    <div class="mfix-footer-copyright">
        &copy; <?php echo date('Y'); ?> Motorfix Injection Services
    </div>
</footer>

<style>
    /* --- Motorfix Footer Styles v1.1 --- */
    /* All classes prefixed with 'mfix-footer-' to avoid conflicts. Uses header's :root vars. */

    :root {
        --mfix-dark-green: #0A4A2A;
        --mfix-black: #1a1a1a;
        --mfix-grey: #6c757d;
        --mfix-light-grey: #f8f9fa;
        --mfix-white: #ffffff;
        --mfix-border: #e5e5e5;
    }

    /* Main Footer Container */
    .mfix-footer-main {
        background: var(--mfix-white);
        color: var(--mfix-black);
        font-family: 'Inter', sans-serif;
        padding: 30px 20px 20px;
        border-top: 1px solid var(--mfix-border);
        margin-top: auto; /* Pushes to bottom if in flex body */
    }

    /* Bosch Section */
    .mfix-footer-bosch {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--mfix-border);
        margin-bottom: 20px;
    }

    .mfix-footer-bosch-logo {
        height: 70px;
        width: auto;
    }

    .mfix-footer-bosch-text {
        color: var(--mfix-dark-green);
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Grid Layout */
    .mfix-footer-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin: 0 auto 20px;
        max-width: 1200px;
        width: 100%;
    }

    /* Grid Items */
    .mfix-footer-grid-item {
        text-align: left;
    }

    .mfix-footer-grid-title {
        color: var(--mfix-dark-green);
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mfix-footer-icon {
        font-size: 1rem;
        color: var(--mfix-grey);
    }

    .mfix-footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mfix-footer-list li {
        margin-bottom: 8px;
    }

    .mfix-footer-link {
        color: var(--mfix-black);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s ease;
    }

    .mfix-footer-link:hover {
        color: var(--mfix-dark-green);
    }

    .mfix-footer-small-icon {
        font-size: 0.8rem;
        color: var(--mfix-grey);
        width: 12px;
    }

    .mfix-footer-branch {
        margin: 0;
        font-size: 0.85rem;
    }

    .mfix-footer-branch-link {
        color: var(--mfix-black);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .mfix-footer-branch-link:hover {
        color: var(--mfix-dark-green);
    }

    /* Tagline */
    .mfix-footer-tagline {
        text-align: center;
        color: var(--mfix-grey);
        font-size: 0.85rem;
        font-style: italic;
        margin: 0 auto 15px;
        max-width: 1200px;
        padding: 0 20px;
    }

    /* Legal Links */
    .mfix-footer-legal {
        text-align: center;
        margin: 15px auto;
        max-width: 1200px;
        padding: 0 20px;
    }

    .mfix-footer-legal-link {
        color: var(--mfix-grey);
        text-decoration: none;
        font-size: 0.8rem;
        transition: color 0.2s ease;
    }

    .mfix-footer-legal-link:hover {
        color: var(--mfix-dark-green);
    }

    .mfix-footer-separator {
        color: var(--mfix-grey);
        font-size: 0.8rem;
        margin: 0 8px;
    }

    /* Copyright */
    .mfix-footer-copyright {
        text-align: center;
        color: var(--mfix-grey);
        font-size: 0.8rem;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--mfix-border);
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Responsive: MODIFIED to keep 3 columns on mobile */
    @media (max-width: 991px) {
        .mfix-footer-grid {
            /* grid-template-columns: 1fr; */ /* REMOVED to keep 3 columns */
            gap: 15px; /* REDUCED gap */
            padding: 0 5px; /* REDUCED padding */
        }

        .mfix-footer-main {
            padding: 20px 10px 10px; /* REDUCED padding */
        }

        .mfix-footer-bosch {
            flex-direction: column;
            gap: 6px;
            padding-bottom: 15px;
        }

        .mfix-footer-tagline,
        .mfix-footer-legal {
            padding: 0 10px;
        }

        .mfix-footer-copyright {
            padding: 0 10px;
            margin: 15px auto 0; /* Centering copyright */
        }
    }

    @media (max-width: 480px) {
         .mfix-footer-grid {
             gap: 10px; /* Further reduce gap */
        }
        
        .mfix-footer-grid-title {
            font-size: 0.8rem; /* REDUCED */
            margin-bottom: 8px; /* REDUCED */
            gap: 4px; /* REDUCED */
        }
        
        .mfix-footer-icon {
            font-size: 0.85rem; /* REDUCED */
        }

        .mfix-footer-link,
        .mfix-footer-branch {
            font-size: 0.75rem; /* REDUCED */
            line-height: 1.3; /* ADDED for stacking text */
            word-break: break-word; /* ADDED to break long text like emails */
        }
        
        .mfix-footer-link {
            gap: 4px; /* REDUCED */
        }

        .mfix-footer-list li {
            margin-bottom: 5px; /* REDUCED */
        }
        
        .mfix-footer-small-icon {
            font-size: 0.7rem; /* REDUCED */
            width: 10px; /* REDUCED */
        }

        .mfix-footer-tagline {
            font-size: 0.8rem;
        }
        
        /* Reduce font size for Bosch text on small screens */
        .mfix-footer-bosch-text {
             font-size: 0.85rem;
             text-align: center;
        }
    }
</style>
