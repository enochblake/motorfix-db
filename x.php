<?php
/**
 * Motor Fix Injection Services - Redesigned Landing Page
 * * Features:
 * - Compact hero section with simple search
 * - Dynamic product grid with share functionality
 * - Full-size images with fixed aspect ratio
 * - Mobile-first responsive design
 * * @version 2.1
 * @date Wednesday, October 22, 2025
 */

require_once 'db.php';
require_once 'head.php';

// Fetch active categories for potential future use
$categories_stmt = $pdo->query("SELECT * FROM product_categories ORDER BY name ASC");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch car makes
$makes_stmt = $pdo->query("SELECT * FROM car_makes ORDER BY name ASC");
$car_makes = $makes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch car models grouped by make
$models_stmt = $pdo->query("
    SELECT cm.id, cm.name, cm.make_id, mk.name as make_name 
    FROM car_models cm 
    JOIN car_makes mk ON cm.make_id = mk.id 
    ORDER BY mk.name ASC, cm.name ASC
");
$car_models = $models_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group models by make for JavaScript
$models_by_make = [];
foreach ($car_models as $model) {
    $models_by_make[$model['make_id']][] = $model;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Motor Fix Injection Services - Kenya's trusted source for genuine automotive parts. BOSCH Authorized Wholesaler offering quality spare parts across multiple branches.">
    <title>Motor Fix Injection Services - Genuine Quality Automotive Parts in Kenya</title>
    
    <style>
        :root {
            --mfix-dark-green: #0A4A2A;
            --mfix-green: #28a745;
            --mfix-black: #1a1a1a;
            --mfix-grey: #6c757d;
            --mfix-light-grey: #f8f9fa;
            --mfix-white: #ffffff;
            --mfix-border: #e5e5e5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--mfix-white);
            color: var(--mfix-black);
        }

        /* Hero Section - Compact & Impactful */
        .hero-landing {
            background: linear-gradient(135deg, var(--mfix-dark-green) 0%, #0d5f38 100%);
            padding: 25px 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-landing::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><path d="M10 10 L90 10 L90 90 L10 90 Z" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></svg>');
            opacity: 0.5;
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-text {
            text-align: center;
            margin-bottom: 20px;
        }

        .hero-text h1 {
            color: var(--mfix-white);
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 6px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .hero-text p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.95rem;
            font-weight: 400;
        }

        /* Search Tool - Simple Search Bar */
        .search-tool {
            background: var(--mfix-white);
            border-radius: 50px;
            padding: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            color: var(--mfix-black);
            outline: none;
        }

        .search-input::placeholder {
            color: var(--mfix-grey);
        }

        .search-btn {
            width: 44px;
            height: 44px;
            background: var(--mfix-dark-green);
            color: var(--mfix-white);
            border: none;
            border-radius: 50%;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .search-btn:hover {
            background: var(--mfix-green);
            transform: scale(1.05);
        }

        /* Products Section */
        .products-showcase {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        /* Product Grid - Fixed Size Containers */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: var(--mfix-white);
            border: 2px solid var(--mfix-border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            border-color: var(--mfix-dark-green);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        /* Fixed Image Container */
        .product-image-box {
            width: 100%;
            height: 200px;
            background: var(--mfix-light-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .product-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Share Button */
        .share-btn {
            position: absolute;
            /* top: 10px; */ /* MOVED */
            bottom: 10px;    /* ADDED */
            right: 10px;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
            z-index: 10;
            /* opacity: 0; */ /* REMOVED - Button is now always visible */
        }

        /* REMOVED - This hover rule is no longer needed
        .product-card:hover .share-btn {
            opacity: 1;
        }
        */

        .share-btn:hover {
            background: var(--mfix-dark-green);
            color: var(--mfix-white);
            transform: scale(1.1);
        }

        .share-btn i {
            font-size: 0.9rem;
        }

        /* Product Info */
        .product-info {
            padding: 15px;
            position: relative; /* ADDED for share button positioning */
        }

        .product-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--mfix-black);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 42px;
            padding-right: 40px; /* ADDED to make space for share button */
        }

        /* Loading & Empty States */
        .loading-state, .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--mfix-grey);
        }

        .loading-spinner {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 4px solid var(--mfix-light-grey);
            border-top-color: var(--mfix-dark-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        /* FAB */
        .floating-action {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 56px;
            height: 56px;
            background: var(--mfix-dark-green);
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--mfix-white);
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 999;
            text-decoration: none;
            animation: pulse 2s infinite;
        }

        .floating-action:hover {
            background: var(--mfix-green);
            transform: scale(1.1);
        }

        .floating-action.whatsapp {
            background: #25D366;
        }

        .floating-action.whatsapp:hover {
            background: #20ba5a;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
            50% { box-shadow: 0 4px 30px rgba(10, 74, 42, 0.6); }
        }

        /* Copy Notification Toast */
        .copy-toast {
            position: fixed;
            bottom: 100px;
            right: 25px;
            background: var(--mfix-dark-green);
            color: var(--mfix-white);
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 1001;
            animation: slideIn 0.3s ease-out;
        }

        .copy-toast.show {
            display: flex;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 991px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .hero-text h1 {
                font-size: 1.3rem;
            }

            .hero-text p {
                font-size: 0.85rem;
            }

            .search-tool {
                max-width: 100%;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .product-image-box {
                height: 160px;
            }
        }

        @media (max-width: 480px) {
            .hero-landing {
                padding: 20px 15px;
            }

            .search-input {
                font-size: 0.85rem;
                padding: 10px 15px;
            }

            .search-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .product-name {
                font-size: 0.85rem;
            }

            .floating-action {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="mfix-body-content-spacer">
        
        <!-- Hero Section -->
        <section class="hero-landing">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Genuine Automotive Parts</h1>
                    <p>Trusted quality across Kenya | BOSCH Authorized Wholesaler</p>
                </div>
                
                <div class="search-tool">
                    <input type="text" 
                           class="search-input" 
                           id="searchInput" 
                           placeholder="Search..."
                           oninput="triggerSearch()"> <!-- MODIFIED: Changed onkeypress to oninput -->
                    <button class="search-btn" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="products-showcase">
            <div id="productsContainer">
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>Loading products...</p>
                </div>
            </div>
        </section>

    </div>

    <!-- Floating Action Button -->
    <a href="#" id="fabButton" class="floating-action" title="Contact Us">
        <i class="fas fa-comments"></i>
    </a>

    <script>
        // ORIGINAL WORKING CODE FROM index (7).php - DO NOT CHANGE
        const modelsByMake = <?php echo json_encode($models_by_make); ?>;
        
        let currentFilters = {
            category: '',
            make: '',
            model: ''
        };

        let searchTimeout = null; // ADDED: For debouncing the search

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            checkCustomerCareStatus();
            setInterval(checkCustomerCareStatus, 30000);
        });

        // MODIFIED loadProducts function
        function loadProducts() {
            const container = document.getElementById('productsContainer');
            
            // ADDED: Show loading state before every fetch
            container.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>Loading products...</p>
                </div>
            `;
            
            const params = new URLSearchParams(currentFilters);
            
            fetch('/api/get_products.php?' + params.toString())
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displayProducts(data.products);
                    } else {
                        console.error('API Error:', data);
                        showError(data.message || 'Failed to load products');
                    }
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    showError('An error occurred while loading products. Please check console for details.');
                });
        }

        // Display products with new design
        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            
            if (products.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
                        <h3>No products found</h3>
                        <p>Try a different search term</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="products-grid">';
            products.forEach(product => {
                const imageUrl = product.image_path 
                    ? `u/media/products/${product.image_path}` 
                    : '/media/placeholder-product.png';
                
                const productLink = `/product?id=${product.id}`;
                
                // MODIFIED: Moved share-btn from product-image-box to product-info
                html += `
                    <div class="product-card">
                        <div class="product-image-box">
                            <a href="${productLink}">
                                <img src="${imageUrl}" 
                                     alt="${escapeHtml(product.name)}" 
                                     onerror="this.src='/media/placeholder-product.png'">
                            </a>
                        </div>
                        <div class="product-info">
                            <a href="${productLink}" style="text-decoration: none; color: inherit;">
                                <h3 class="product-name">${escapeHtml(product.name)}</h3>
                            </a>
                            <button class="share-btn" onclick="copyProductLink('${productLink}', event)" title="Copy Link">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        }

        // ADDED: Debounce function for live search
        function triggerSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300); // Wait 300ms after user stops typing
        }

        // Search functionality with API integration
        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value; // No .trim() for live search
            
            if (searchTerm) {
                // Add search parameter to filters
                currentFilters.search = searchTerm;
            } else {
                // Remove search parameter if empty
                delete currentFilters.search;
            }
            
            loadProducts(); // This will now show the loading spinner
        }

        // Direct copy without modal
        function copyProductLink(productPath, event) {
            event.preventDefault();
            event.stopPropagation();
            
            const fullUrl = window.location.origin + productPath;
            
            // Create temporary input to copy
            const tempInput = document.createElement('input');
            tempInput.value = fullUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Visual feedback on button
            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.background = 'var(--mfix-green)';
            btn.style.color = 'white';
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        }

        // Share modal functions (kept for compatibility)
        function openShareModal(productPath, event) {
            event.preventDefault();
            event.stopPropagation();
            const fullUrl = window.location.origin + productPath;
            document.getElementById('shareLink').value = fullUrl;
            document.getElementById('shareModal').style.display = 'flex';
        }

        function closeShareModal() {
            document.getElementById('shareModal').style.display = 'none';
        }

        function copyShareLink() {
            const input = document.getElementById('shareLink');
            input.select();
            document.execCommand('copy');
            
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.style.background = 'var(--mfix-green)';
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
            }, 2000);
        }

        function closeShareModal() {
            document.getElementById('shareModal').style.display = 'none';
        }

        function copyShareLink() {
            const input = document.getElementById('shareLink');
            input.select();
            document.execCommand('copy');
            
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.style.background = 'var(--mfix-green)';
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
            }, 2000);
        }

        // ORIGINAL WORKING checkCustomerCareStatus function
        function checkCustomerCareStatus() {
            fetch('/api/check_cc_status.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const fab = document.getElementById('fabButton');
                    
                    if (data.online) {
                        fab.href = '/chat';
                        fab.className = 'floating-action';
                        fab.innerHTML = '<i class="fas fa-comments"></i>';
                        fab.title = 'Chat with us';
                    } else {
                        fab.href = 'https://wa.me/254725044914';
                        fab.className = 'floating-action whatsapp';
                        fab.innerHTML = '<i class="fab fa-whatsapp"></i>';
                        fab.title = 'Message us on WhatsApp';
                        fab.target = '_blank';
                    }
                })
                .catch(error => {
                    console.error('Error checking CC status:', error);
                    const fab = document.getElementById('fabButton');
                    fab.href = 'https://wa.me/254725044914';
                    fab.className = 'floating-action whatsapp';
                    fab.innerHTML = '<i class="fab fa-whatsapp"></i>';
                    fab.title = 'Message us on WhatsApp';
                    fab.target = '_blank';
                });
        }

        function showError(message) {
            const container = document.getElementById('productsContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3>Error Loading Products</h3>
                    <p>${escapeHtml(message)}</p>
                </div>
            `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>

<?php require_once 'foot.php'; ?>

