<?php
/**
 * Motor Fix - Products Management System
 * 
 * Features:
 * - Create new products with multiple images
 * - Edit existing products
 * - Activate/Deactivate products (soft delete)
 * - Permanent deletion with cascade handling
 * - Featured image selection
 * - Inline category creation
 * - Fast image uploads with client-side preview
 * - No page reloads on actions
 * 
 * @version 1.0
 * @date October 23, 2025
 */

require_once 'ic/aheader.php';

$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';
$active_view = isset($_POST['active_view']) ? $_POST['active_view'] : 'create';

// Helper function for unique image filename
function generateUniqueImageName($original_filename) {
    $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    return 'prod_' . uniqid() . '_' . time() . '.' . $extension;
}

// =============================================================================
// CATEGORY MANAGEMENT (Quick Create)
// =============================================================================
if (isset($_POST['quick_create_category'])) {
    $category_name = trim($_POST['quick_category_name']);
    $category_desc = trim($_POST['quick_category_description'] ?? '');
    
    if (!empty($category_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO product_categories (name, description) VALUES (?, ?)");
            $stmt->execute([$category_name, $category_desc]);
            $new_category_id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'category_id' => $new_category_id,
                'category_name' => $category_name
            ]);
            exit;
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

// =============================================================================
// PRODUCT CREATION
// =============================================================================
if (isset($_POST['create_product'])) {
    $category_id = intval($_POST['product_category']);
    $product_name = trim($_POST['product_name']);
    $product_desc = trim($_POST['product_description'] ?? '');
    
    if ($category_id > 0 && !empty($product_name)) {
        try {
            $pdo->beginTransaction();
            
            // Create product
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute([$category_id, $product_name, $product_desc]);
            $product_id = $pdo->lastInsertId();
            
            // Handle image uploads
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/media/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/svg+xml'];
                $featured_index = intval($_POST['featured_image_index'] ?? 0);
                
                foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                        $file_type = $_FILES['product_images']['type'][$key];
                        
                        if (in_array($file_type, $allowed_types)) {
                            $unique_name = generateUniqueImageName($_FILES['product_images']['name'][$key]);
                            
                            if (move_uploaded_file($tmp_name, $upload_dir . $unique_name)) {
                                $is_primary = ($key === $featured_index) ? 1 : 0;
                                $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)");
                                $stmt_img->execute([$product_id, $unique_name, $is_primary]);
                            }
                        }
                    }
                }
            }
            
            $pdo->commit();
            $feedback_message = "Product '{$product_name}' created successfully!";
            $feedback_type = "success";
            $active_view = 'create';
        } catch(Exception $e) {
            $pdo->rollBack();
            $feedback_message = "Error creating product: " . $e->getMessage();
            $feedback_type = "error";
        }
    } else {
        $feedback_message = "Please provide a category and product name.";
        $feedback_type = "error";
    }
}

// =============================================================================
// PRODUCT UPDATE
// =============================================================================
if (isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $category_id = intval($_POST['edit_product_category']);
    $product_name = trim($_POST['edit_product_name']);
    $product_desc = trim($_POST['edit_product_description'] ?? '');
    
    if ($product_id > 0 && $category_id > 0 && !empty($product_name)) {
        try {
            $pdo->beginTransaction();
            
            // Update product details
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ? WHERE id = ?");
            $stmt->execute([$category_id, $product_name, $product_desc, $product_id]);
            
            // Handle new image uploads
            if (isset($_FILES['edit_product_images']) && !empty($_FILES['edit_product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/media/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/svg+xml'];
                
                foreach ($_FILES['edit_product_images']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                        $file_type = $_FILES['edit_product_images']['type'][$key];
                        
                        if (in_array($file_type, $allowed_types)) {
                            $unique_name = generateUniqueImageName($_FILES['edit_product_images']['name'][$key]);
                            
                            if (move_uploaded_file($tmp_name, $upload_dir . $unique_name)) {
                                $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, 0)");
                                $stmt_img->execute([$product_id, $unique_name]);
                            }
                        }
                    }
                }
            }
            
            $pdo->commit();
            $feedback_message = "Product updated successfully!";
            $feedback_type = "success";
            $active_view = 'manage';
        } catch(Exception $e) {
            $pdo->rollBack();
            $feedback_message = "Error updating product: " . $e->getMessage();
            $feedback_type = "error";
        }
    } else {
        $feedback_message = "Invalid product data provided.";
        $feedback_type = "error";
    }
}

// =============================================================================
// SET FEATURED IMAGE
// =============================================================================
if (isset($_POST['set_featured_image'])) {
    $image_id = intval($_POST['image_id']);
    $product_id = intval($_POST['product_id']);
    
    try {
        $pdo->beginTransaction();
        
        // Remove primary flag from all images of this product
        $pdo->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?")->execute([$product_id]);
        
        // Set new primary image
        $pdo->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?")->execute([$image_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Featured image updated']);
        exit;
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

// =============================================================================
// DELETE IMAGE
// =============================================================================
if (isset($_POST['delete_image'])) {
    $image_id = intval($_POST['image_id']);
    
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            $file_path = __DIR__ . '/media/products/' . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$image_id]);
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
            exit;
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

// =============================================================================
// ACTIVATE/DEACTIVATE PRODUCT
// =============================================================================
if (isset($_POST['toggle_product_status'])) {
    $product_id = intval($_POST['product_id']);
    $new_status = intval($_POST['new_status']);
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $product_id]);
        
        $status_text = $new_status ? 'activated' : 'deactivated';
        $feedback_message = "Product {$status_text} successfully!";
        $feedback_type = "success";
        $active_view = 'manage';
    } catch(PDOException $e) {
        $feedback_message = "Error updating product status: " . $e->getMessage();
        $feedback_type = "error";
    }
}

// =============================================================================
// PERMANENT DELETE PRODUCT
// =============================================================================
if (isset($_POST['permanent_delete_product'])) {
    $product_id = intval($_POST['product_id']);
    
    try {
        $pdo->beginTransaction();
        
        // Delete related records first (cascade will handle most, but we'll be explicit)
        // Delete from activity_logs if they reference this product
        $pdo->prepare("DELETE FROM activity_logs WHERE details LIKE ?")->execute(['%"product_id":' . $product_id . '%']);
        
        // Delete stock movements
        $pdo->prepare("DELETE FROM stock_movements WHERE product_id = ?")->execute([$product_id]);
        
        // Delete sale items (will fail if there are sales - intentional)
        $sale_check = $pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
        $sale_check->execute([$product_id]);
        if ($sale_check->fetchColumn() > 0) {
            throw new Exception("Cannot delete product: It has associated sales records.");
        }
        
        // Delete inventory records
        $pdo->prepare("DELETE FROM inventory WHERE product_id = ?")->execute([$product_id]);
        
        // Delete images from filesystem
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($images as $img) {
            $file_path = __DIR__ . '/media/products/' . $img['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Delete image records (cascade should handle, but being explicit)
        $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$product_id]);
        
        // Delete vehicle compatibility
        $pdo->prepare("DELETE FROM product_vehicle_compatibility WHERE product_id = ?")->execute([$product_id]);
        
        // Finally, delete the product
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
        
        $pdo->commit();
        $feedback_message = "Product permanently deleted successfully!";
        $feedback_type = "success";
        $active_view = 'manage';
    } catch(Exception $e) {
        $pdo->rollBack();
        $feedback_message = "Error deleting product: " . $e->getMessage();
        $feedback_type = "error";
    }
}

// =============================================================================
// DATA FETCHING
// =============================================================================
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all products (active and inactive)
$products_stmt = $pdo->query("
    SELECT p.*, pc.name as category_name,
        (SELECT GROUP_CONCAT(pi.id, '::', pi.image_path, '::', pi.is_primary ORDER BY pi.is_primary DESC, pi.id ASC SEPARATOR '||') 
         FROM product_images pi WHERE pi.product_id = p.id) as images,
        (SELECT COUNT(*) FROM inventory WHERE product_id = p.id) as has_inventory,
        (SELECT COUNT(*) FROM sale_items WHERE product_id = p.id) as has_sales
    FROM products p 
    JOIN product_categories pc ON p.category_id = pc.id 
    ORDER BY p.is_active DESC, p.name ASC
");
$all_products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="pmp-main-content">
    <div class="pmp-page-header">
        <h1><i class="fas fa-box-open"></i> Product Management</h1>
        <p>Create new products or manage existing ones</p>
    </div>
    
    <?php if ($feedback_message): ?>
        <div class="pmp-alert pmp-alert-<?php echo $feedback_type; ?>" id="feedback-alert">
            <i class="fas fa-<?php echo $feedback_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <!-- View Toggle -->
    <div class="pmp-view-toggle">
        <button class="pmp-toggle-btn <?php echo $active_view === 'create' ? 'active' : ''; ?>" data-view="create">
            <i class="fas fa-plus-circle"></i> Create Product
        </button>
        <button class="pmp-toggle-btn <?php echo $active_view === 'manage' ? 'active' : ''; ?>" data-view="manage">
            <i class="fas fa-edit"></i> Manage Products
        </button>
    </div>

    <!-- CREATE PRODUCT VIEW -->
    <div class="pmp-view-container <?php echo $active_view === 'create' ? 'active' : ''; ?>" id="create-view">
        <div class="pmp-card">
            <h2><i class="fas fa-sparkles"></i> Create New Product</h2>
            <form method="POST" enctype="multipart/form-data" id="create-product-form" class="pmp-form">
                <input type="hidden" name="active_view" value="create">
                <input type="hidden" name="featured_image_index" id="featured_image_index" value="0">
                
                <div class="pmp-form-row">
                    <div class="pmp-form-group">
                        <label for="product_category">Category *</label>
                        <div class="pmp-input-group">
                            <select name="product_category" id="product_category" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="pmp-btn-icon" id="quick-category-btn" title="Quick Create Category">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="pmp-form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" name="product_name" id="product_name" required placeholder="e.g., Brake Pads - Toyota Corolla">
                    </div>
                </div>
                
                <div class="pmp-form-group">
                    <label for="product_description">Product Description</label>
                    <textarea name="product_description" id="product_description" rows="4" placeholder="Detailed product description..."></textarea>
                </div>
                
                <div class="pmp-form-group">
                    <label for="product_images">Product Images</label>
                    <div class="pmp-upload-info">
                        <i class="fas fa-info-circle"></i> Upload square images for best display. Supported formats: JPG, PNG, GIF, WebP, BMP, SVG
                    </div>
                    <input type="file" name="product_images[]" id="product_images" multiple accept="image/*" class="pmp-file-input">
                    <div id="image-preview-container" class="pmp-image-grid"></div>
                </div>
                
                <button type="submit" name="create_product" class="pmp-btn-primary">
                    <i class="fas fa-save"></i> Create Product
                </button>
            </form>
        </div>
    </div>

    <!-- MANAGE PRODUCTS VIEW -->
    <div class="pmp-view-container <?php echo $active_view === 'manage' ? 'active' : ''; ?>" id="manage-view">
        <div class="pmp-card">
            <h2><i class="fas fa-list"></i> All Products</h2>
            
            <?php if (empty($all_products)): ?>
                <div class="pmp-empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No products found. Create your first product!</p>
                </div>
            <?php else: ?>
                <div class="pmp-products-grid">
                    <?php foreach ($all_products as $product): ?>
                        <div class="pmp-product-card <?php echo $product['is_active'] ? '' : 'inactive'; ?>">
                            <div class="pmp-product-image">
                                <?php
                                $images = $product['images'] ? explode('||', $product['images']) : [];
                                if (!empty($images)) {
                                    $first_image = explode('::', $images[0]);
                                    $image_path = 'media/products/' . $first_image[1];
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($product['name']) . '" onerror="this.src=\'media/placeholder-product.png\'">';
                                } else {
                                    echo '<img src="media/placeholder-product.png" alt="No image">';
                                }
                                ?>
                                <?php if (!$product['is_active']): ?>
                                    <div class="pmp-status-badge inactive">Inactive</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="pmp-product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="pmp-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                
                                <div class="pmp-product-actions">
                                    <button class="pmp-btn-edit" onclick="openEditModal(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('<?php echo $product['is_active'] ? 'Deactivate' : 'Activate'; ?> this product?');">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="new_status" value="<?php echo $product['is_active'] ? 0 : 1; ?>">
                                        <input type="hidden" name="active_view" value="manage">
                                        <button type="submit" name="toggle_product_status" class="pmp-btn-toggle">
                                            <i class="fas fa-<?php echo $product['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                            <?php echo $product['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                    
                                    <?php if (!$product['is_active']): ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('PERMANENTLY DELETE this product? This cannot be undone!');">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="active_view" value="manage">
                                            <button type="submit" name="permanent_delete_product" class="pmp-btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Quick Category Creation Modal -->
<div class="pmp-modal" id="quick-category-modal">
    <div class="pmp-modal-content">
        <span class="pmp-modal-close">&times;</span>
        <h3><i class="fas fa-tags"></i> Quick Create Category</h3>
        <form id="quick-category-form">
            <div class="pmp-form-group">
                <label>Category Name *</label>
                <input type="text" name="quick_category_name" id="quick_category_name" required>
            </div>
            <div class="pmp-form-group">
                <label>Description (Optional)</label>
                <textarea name="quick_category_description" id="quick_category_description" rows="3"></textarea>
            </div>
            <button type="submit" class="pmp-btn-primary">
                <i class="fas fa-plus"></i> Create Category
            </button>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="pmp-modal" id="edit-product-modal">
    <div class="pmp-modal-content large">
        <span class="pmp-modal-close">&times;</span>
        <h3><i class="fas fa-edit"></i> Edit Product</h3>
        <form method="POST" enctype="multipart/form-data" id="edit-product-form">
            <input type="hidden" name="product_id" id="edit_product_id">
            <input type="hidden" name="active_view" value="manage">
            
            <div class="pmp-form-row">
                <div class="pmp-form-group">
                    <label>Category *</label>
                    <select name="edit_product_category" id="edit_product_category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="pmp-form-group">
                    <label>Product Name *</label>
                    <input type="text" name="edit_product_name" id="edit_product_name" required>
                </div>
            </div>
            
            <div class="pmp-form-group">
                <label>Description</label>
                <textarea name="edit_product_description" id="edit_product_description" rows="4"></textarea>
            </div>
            
            <div class="pmp-form-group">
                <label>Current Images</label>
                <div id="edit_images_container" class="pmp-image-grid"></div>
            </div>
            
            <div class="pmp-form-group">
                <label>Add New Images</label>
                <input type="file" name="edit_product_images[]" multiple accept="image/*" class="pmp-file-input">
            </div>
            
            <button type="submit" name="update_product" class="pmp-btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</div>

<style>
:root {
    --pmp-primary: #007bff;
    --pmp-success: #28a745;
    --pmp-danger: #dc3545;
    --pmp-warning: #ffc107;
    --pmp-dark: #343a40;
    --pmp-light: #f8f9fa;
    --pmp-border: #dee2e6;
    --pmp-shadow: rgba(0,0,0,0.1);
}

.pmp-main-content {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.pmp-page-header {
    margin-bottom: 25px;
}

.pmp-page-header h1 {
    font-size: 1.8rem;
    margin: 0 0 5px 0;
    color: var(--pmp-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.pmp-page-header p {
    margin: 0;
    color: #6c757d;
}

.pmp-alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease;
}

.pmp-alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid var(--pmp-success);
}

.pmp-alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid var(--pmp-danger);
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.pmp-view-toggle {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.pmp-toggle-btn {
    flex: 1;
    padding: 12px 20px;
    background: white;
    border: 2px solid var(--pmp-border);
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    color: var(--pmp-dark);
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.pmp-toggle-btn:hover {
    border-color: var(--pmp-primary);
    color: var(--pmp-primary);
}

.pmp-toggle-btn.active {
    background: var(--pmp-primary);
    color: white;
    border-color: var(--pmp-primary);
}

.pmp-view-container {
    display: none;
}

.pmp-view-container.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.pmp-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px var(--pmp-shadow);
}

.pmp-card h2 {
    margin: 0 0 20px 0;
    color: var(--pmp-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.pmp-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.pmp-form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.pmp-form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--pmp-dark);
}

.pmp-input-group {
    display: flex;
    gap: 8px;
}

.pmp-input-group select {
    flex: 1;
}

.pmp-form-group input,
.pmp-form-group select,
.pmp-form-group textarea {
    padding: 10px 15px;
    border: 1px solid var(--pmp-border);
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.pmp-form-group input:focus,
.pmp-form-group select:focus,
.pmp-form-group textarea:focus {
    outline: none;
    border-color: var(--pmp-primary);
}

.pmp-btn-icon {
    padding: 10px 15px;
    background: var(--pmp-primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.pmp-btn-icon:hover {
    background: #0056b3;
}

.pmp-upload-info {
    padding: 10px 15px;
    background: #e7f3ff;
    border-left: 3px solid var(--pmp-primary);
    border-radius: 4px;
    font-size: 0.9rem;
    color: #004085;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pmp-file-input {
    padding: 10px;
    border: 2px dashed var(--pmp-border);
    border-radius: 8px;
    cursor: pointer;
}

.pmp-file-input:hover {
    border-color: var(--pmp-primary);
}

.pmp-image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.pmp-image-preview,
.pmp-existing-image {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid var(--pmp-border);
    background: var(--pmp-light);
}

.pmp-image-preview img,
.pmp-existing-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.pmp-image-preview.featured,
.pmp-existing-image.featured {
    border-color: var(--pmp-success);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
}

.pmp-featured-badge {
    position: absolute;
    top: 5px;
    left: 5px;
    background: var(--pmp-success);
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.pmp-image-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    padding: 5px;
    display: flex;
    justify-content: center;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.3s;
}

.pmp-image-preview:hover .pmp-image-actions,
.pmp-existing-image:hover .pmp-image-actions {
    opacity: 1;
}

.pmp-img-btn {
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.3s;
}

.pmp-img-btn-star {
    background: var(--pmp-warning);
    color: #000;
}

.pmp-img-btn-delete {
    background: var(--pmp-danger);
    color: white;
}

.pmp-btn-primary {
    padding: 12px 28px;
    background: var(--pmp-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.pmp-btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}

.pmp-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.pmp-product-card {
    background: white;
    border: 1px solid var(--pmp-border);
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s;
}

.pmp-product-card:hover {
    box-shadow: 0 4px 12px var(--pmp-shadow);
    transform: translateY(-3px);
}

.pmp-product-card.inactive {
    opacity: 0.7;
}

.pmp-product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: var(--pmp-light);
}

.pmp-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pmp-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.pmp-status-badge.inactive {
    background: var(--pmp-danger);
    color: white;
}

.pmp-product-info {
    padding: 15px;
}

.pmp-product-info h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: var(--pmp-dark);
}

.pmp-category {
    color: #6c757d;
    font-size: 0.85rem;
    margin: 0 0 15px 0;
}

.pmp-product-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.pmp-btn-edit,
.pmp-btn-toggle,
.pmp-btn-delete {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.pmp-btn-edit {
    background: var(--pmp-primary);
    color: white;
}

.pmp-btn-edit:hover {
    background: #0056b3;
}

.pmp-btn-toggle {
    background: var(--pmp-warning);
    color: #000;
}

.pmp-btn-toggle:hover {
    background: #e0a800;
}

.pmp-btn-delete {
    background: var(--pmp-danger);
    color: white;
}

.pmp-btn-delete:hover {
    background: #c82333;
}

.pmp-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.pmp-empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.pmp-empty-state p {
    font-size: 1.2rem;
    margin: 0;
}

.pmp-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    align-items: center;
    justify-content: center;
}

.pmp-modal-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.pmp-modal-content.large {
    max-width: 800px;
}

.pmp-modal-close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 28px;
    color: #aaa;
    cursor: pointer;
    transition: color 0.3s;
}

.pmp-modal-close:hover {
    color: var(--pmp-danger);
}

.pmp-modal h3 {
    margin: 0 0 20px 0;
    color: var(--pmp-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

@media (max-width: 768px) {
    .pmp-form-row {
        grid-template-columns: 1fr;
    }
    
    .pmp-products-grid {
        grid-template-columns: 1fr;
    }
    
    .pmp-toggle-btn {
        font-size: 0.9rem;
        padding: 10px 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Toggle
    document.querySelectorAll('.pmp-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            document.querySelectorAll('.pmp-toggle-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.pmp-view-container').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(view + '-view').classList.add('active');
        });
    });
    
    // Image Preview for Create Product
    const imageInput = document.getElementById('product_images');
    const previewContainer = document.getElementById('image-preview-container');
    let selectedFiles = [];
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            selectedFiles = Array.from(e.target.files);
            previewContainer.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'pmp-image-preview' + (index === 0 ? ' featured' : '');
                        previewDiv.dataset.index = index;
                        
                        previewDiv.innerHTML = `
                            <img src="${event.target.result}" alt="Preview">
                            ${index === 0 ? '<span class="pmp-featured-badge">Featured</span>' : ''}
                            <div class="pmp-image-actions">
                                <button type="button" class="pmp-img-btn pmp-img-btn-star" onclick="setFeaturedImage(${index})">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="pmp-img-btn pmp-img-btn-delete" onclick="removePreviewImage(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
    
    // Quick Category Modal
    const quickCategoryBtn = document.getElementById('quick-category-btn');
    const quickCategoryModal = document.getElementById('quick-category-modal');
    const quickCategoryForm = document.getElementById('quick-category-form');
    
    if (quickCategoryBtn) {
        quickCategoryBtn.addEventListener('click', function() {
            quickCategoryModal.style.display = 'flex';
        });
    }
    
    if (quickCategoryForm) {
        quickCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('quick_create_category', '1');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('product_category');
                    const option = new Option(data.category_name, data.category_id, true, true);
                    select.add(option);
                    quickCategoryModal.style.display = 'none';
                    this.reset();
                    showFeedback('Category created successfully!', 'success');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error creating category: ' + error);
            });
        });
    }
    
    // Modal Close Handlers
    document.querySelectorAll('.pmp-modal-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.pmp-modal').style.display = 'none';
        });
    });
    
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('pmp-modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Auto-hide feedback
    const feedbackAlert = document.getElementById('feedback-alert');
    if (feedbackAlert) {
        setTimeout(() => {
            feedbackAlert.style.transition = 'opacity 0.5s';
            feedbackAlert.style.opacity = '0';
            setTimeout(() => feedbackAlert.remove(), 500);
        }, 5000);
    }
});

// Set Featured Image in Preview
function setFeaturedImage(index) {
    document.querySelectorAll('.pmp-image-preview').forEach(preview => {
        preview.classList.remove('featured');
        const badge = preview.querySelector('.pmp-featured-badge');
        if (badge) badge.remove();
    });
    
    const targetPreview = document.querySelector(`.pmp-image-preview[data-index="${index}"]`);
    targetPreview.classList.add('featured');
    const badge = document.createElement('span');
    badge.className = 'pmp-featured-badge';
    badge.textContent = 'Featured';
    targetPreview.insertBefore(badge, targetPreview.querySelector('.pmp-image-actions'));
    
    document.getElementById('featured_image_index').value = index;
}

// Remove Image from Preview
function removePreviewImage(index) {
    const imageInput = document.getElementById('product_images');
    const dt = new DataTransfer();
    const files = Array.from(imageInput.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    imageInput.files = dt.files;
    imageInput.dispatchEvent(new Event('change'));
}

// Open Edit Modal
function openEditModal(productId) {
    const modal = document.getElementById('edit-product-modal');
    
    // Fetch product data via AJAX
    fetch('get_product.php?id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('edit_product_id').value = data.product.id;
                document.getElementById('edit_product_name').value = data.product.name;
                document.getElementById('edit_product_category').value = data.product.category_id;
                document.getElementById('edit_product_description').value = data.product.description || '';
                
                // Populate images
                const imagesContainer = document.getElementById('edit_images_container');
                imagesContainer.innerHTML = '';
                
                if (data.images && data.images.length > 0) {
                    data.images.forEach(img => {
                        const imageDiv = document.createElement('div');
                        imageDiv.className = 'pmp-existing-image' + (img.is_primary ? ' featured' : '');
                        imageDiv.innerHTML = `
                            <img src="media/products/${img.image_path}" alt="Product Image">
                            ${img.is_primary ? '<span class="pmp-featured-badge">Featured</span>' : ''}
                            <div class="pmp-image-actions">
                                <button type="button" class="pmp-img-btn pmp-img-btn-star" onclick="setFeaturedExisting(${img.id}, ${data.product.id})">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="pmp-img-btn pmp-img-btn-delete" onclick="deleteExistingImage(${img.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        imagesContainer.appendChild(imageDiv);
                    });
                }
                
                modal.style.display = 'flex';
            } else {
                alert('Error loading product data');
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
}

// Set Featured Image for Existing Product
function setFeaturedExisting(imageId, productId) {
    const formData = new FormData();
    formData.append('set_featured_image', '1');
    formData.append('image_id', imageId);
    formData.append('product_id', productId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.querySelectorAll('.pmp-existing-image').forEach(img => {
                img.classList.remove('featured');
                const badge = img.querySelector('.pmp-featured-badge');
                if (badge) badge.remove();
            });
            
            const targetImg = event.target.closest('.pmp-existing-image');
            targetImg.classList.add('featured');
            const badge = document.createElement('span');
            badge.className = 'pmp-featured-badge';
            badge.textContent = 'Featured';
            targetImg.insertBefore(badge, targetImg.querySelector('.pmp-image-actions'));
            
            showFeedback('Featured image updated', 'success');
        } else {
            alert(data.message);
        }
    });
}

// Delete Existing Image
function deleteExistingImage(imageId) {
    if (!confirm('Delete this image permanently?')) return;
    
    const formData = new FormData();
    formData.append('delete_image', '1');
    formData.append('image_id', imageId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            event.target.closest('.pmp-existing-image').remove();
            showFeedback('Image deleted', 'success');
        } else {
            alert(data.message);
        }
    });
}

// Show Feedback Message
function showFeedback(message, type) {
    const existing = document.getElementById('feedback-alert');
    if (existing) existing.remove();
    
    const alert = document.createElement('div');
    alert.id = 'feedback-alert';
    alert.className = 'pmp-alert pmp-alert-' + type;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
    `;
    
    const header = document.querySelector('.pmp-page-header');
    header.insertAdjacentElement('afterend', alert);
    
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 3000);
}
</script>

<?php require_once 'ic/afooter.php'; ?>