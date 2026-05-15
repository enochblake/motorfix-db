<?php
/**
 * Motor Fix - Enhanced Inventory Management System
 * Features:
 * - Full CRUD Operations (Create, Read, Update, Delete)
 * - Implemented Soft Deletes for Products (sets is_active=0)
 * - Fixed Image Path Preview & Management
 * - Scrollable Modals with Better UX
 * - Manual Stock Deletion with Activity Logging
 * - Improved Prerequisites Styling
 * - No Page Reload on Updates (Stay on Active Tab)
 * * @version 3.1 (Soft Delete Edition)
 * @date Monday, October 20, 2025
 */

require_once 'ic/aheader.php';

$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';
$active_tab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'stock';

// Helper function for logging activities
function logActivity($pdo, $user_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $ip = hash('sha256', $_SERVER['REMOTE_ADDR']);
        $stmt->execute([$user_id, $action, $details ? json_encode($details) : null, $ip]);
    } catch(PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

// Helper function for unique image filename
function generateUniqueFilename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    return 'prod_' . uniqid() . '_' . time() . '.' . strtolower($extension);
}

// =============================================================================
// DELETE HANDLERS (with Soft Delete for Products)
// =============================================================================

if (isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);
    $active_tab = 'manage-products';
    try {
        $pdo->beginTransaction();
        
        // --- SOFT DELETE CHANGE ---
        // Instead of deleting images from the filesystem, we keep them in case the product is reactivated.
        /*
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($images as $img) {
            $file_path = __DIR__ . '/media/products/' . $img['image_path'];
            if (file_exists($file_path)) unlink($file_path);
        }
        */
        
        // Get product name for logging
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // --- SOFT DELETE CHANGE ---
        // Change from DELETE to UPDATE to set the product as inactive.
        $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
        $stmt->execute([$product_id]);
        
        $pdo->commit();
        // Log the deactivation for clarity
        logActivity($pdo, $user_id, 'product_deactivated', ['product_id' => $product_id, 'name' => $product['name']]);
        $feedback_message = "Product deactivated successfully."; 
        $feedback_type = "success";
    } catch(PDOException $e) {
        $pdo->rollBack();
        // The original error message is now less likely, but we keep the catch block for other potential errors.
        $feedback_message = "Error deactivating product: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}


if (isset($_POST['delete_stock'])) {
    $inventory_id = intval($_POST['inventory_id']);
    $active_tab = 'inventory';
    try {
        $pdo->beginTransaction();
        
        // Get details for logging
        $stmt = $pdo->prepare("SELECT i.*, p.name as product_name FROM inventory i 
                              JOIN products p ON i.product_id = p.id WHERE i.id = ?");
        $stmt->execute([$inventory_id]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stock) {
            // Log the stock movement
            $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, branch_id, user_id, quantity_change, movement_type, notes) 
                                  VALUES (?, ?, ?, ?, 'adjustment', 'Manual deletion by admin')");
            $stmt->execute([$stock['product_id'], $stock['branch_id'], $user_id, -$stock['quantity']]);
            
            // Delete inventory record
            $pdo->prepare("DELETE FROM inventory WHERE id = ?")->execute([$inventory_id]);
            
            $pdo->commit();
            logActivity($pdo, $user_id, 'stock_deleted', [
                'inventory_id' => $inventory_id, 
                'product' => $stock['product_name'],
                'quantity' => $stock['quantity']
            ]);
            $feedback_message = "Stock deleted successfully."; 
            $feedback_type = "success";
        }
    } catch(PDOException $e) {
        $pdo->rollBack();
        $feedback_message = "Error deleting stock: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}

if (isset($_POST['delete_category'])) {
    $category_id = intval($_POST['category_id']);
    $active_tab = 'prerequisites';
    try {
        // Check if category has products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $feedback_message = "Cannot delete category: It has {$result['count']} product(s) assigned."; 
            $feedback_type = "error";
        } else {
            $stmt = $pdo->prepare("SELECT name FROM product_categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $cat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pdo->prepare("DELETE FROM product_categories WHERE id = ?")->execute([$category_id]);
            logActivity($pdo, $user_id, 'category_deleted', ['id' => $category_id, 'name' => $cat['name']]);
            $feedback_message = "Category deleted successfully."; 
            $feedback_type = "success";
        }
    } catch(PDOException $e) {
        $feedback_message = "Error: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}

if (isset($_POST['delete_car_make'])) {
    $make_id = intval($_POST['make_id']);
    $active_tab = 'prerequisites';
    try {
        // Check if make has models
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM car_models WHERE make_id = ?");
        $stmt->execute([$make_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $feedback_message = "Cannot delete make: It has {$result['count']} model(s) assigned."; 
            $feedback_type = "error";
        } else {
            $stmt = $pdo->prepare("SELECT name FROM car_makes WHERE id = ?");
            $stmt->execute([$make_id]);
            $make = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pdo->prepare("DELETE FROM car_makes WHERE id = ?")->execute([$make_id]);
            logActivity($pdo, $user_id, 'car_make_deleted', ['id' => $make_id, 'name' => $make['name']]);
            $feedback_message = "Car make deleted successfully."; 
            $feedback_type = "success";
        }
    } catch(PDOException $e) {
        $feedback_message = "Error: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}

if (isset($_POST['delete_car_model'])) {
    $model_id = intval($_POST['model_id']);
    $active_tab = 'prerequisites';
    try {
        $stmt = $pdo->prepare("SELECT name FROM car_models WHERE id = ?");
        $stmt->execute([$model_id]);
        $model = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pdo->prepare("DELETE FROM car_models WHERE id = ?")->execute([$model_id]);
        logActivity($pdo, $user_id, 'car_model_deleted', ['id' => $model_id, 'name' => $model['name']]);
        $feedback_message = "Car model deleted successfully."; 
        $feedback_type = "success";
    } catch(PDOException $e) {
        $feedback_message = "Error: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}

if (isset($_POST['delete_image'])) {
    $image_id = intval($_POST['image_id']);
    $active_tab = isset($_POST['from_tab']) ? $_POST['from_tab'] : 'manage-products';
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($image) {
            $file_path = __DIR__ . '/media/products/' . $image['image_path'];
            if (file_exists($file_path)) unlink($file_path);
            $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$image_id]);
            logActivity($pdo, $user_id, 'image_deleted', ['image_id' => $image_id]);
            $feedback_message = "Image deleted successfully."; 
            $feedback_type = "success";
        }
    } catch(PDOException $e) {
        $feedback_message = "Error deleting image: " . $e->getMessage(); 
        $feedback_type = "error";
    }
}

// =============================================================================
// UPDATE HANDLERS
// =============================================================================

if (isset($_POST['update_category'])) {
    $category_id = intval($_POST['category_id']);
    $category_name = trim($_POST['category_name']);
    $category_desc = trim($_POST['category_description']);
    $active_tab = 'prerequisites';
    if ($category_id > 0 && !empty($category_name)) {
        try {
            $stmt = $pdo->prepare("UPDATE product_categories SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$category_name, $category_desc, $category_id]);
            logActivity($pdo, $user_id, 'category_updated', ['id' => $category_id, 'name' => $category_name]);
            $feedback_message = "Category updated."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['update_car_make'])) {
    $make_id = intval($_POST['make_id']);
    $make_name = trim($_POST['make_name']);
    $active_tab = 'prerequisites';
    if ($make_id > 0 && !empty($make_name)) {
        try {
            $stmt = $pdo->prepare("UPDATE car_makes SET name = ? WHERE id = ?");
            $stmt->execute([$make_name, $make_id]);
            logActivity($pdo, $user_id, 'car_make_updated', ['id' => $make_id, 'name' => $make_name]);
            $feedback_message = "Car make updated."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['update_car_model'])) {
    $model_id = intval($_POST['model_id']);
    $make_id = intval($_POST['make_id']);
    $model_name = trim($_POST['model_name']);
    $year_start = !empty($_POST['year_start']) ? intval($_POST['year_start']) : null;
    $year_end = !empty($_POST['year_end']) ? intval($_POST['year_end']) : null;
    $active_tab = 'prerequisites';
    if ($model_id > 0 && $make_id > 0 && !empty($model_name)) {
        try {
            $stmt = $pdo->prepare("UPDATE car_models SET make_id = ?, name = ?, year_start = ?, year_end = ? WHERE id = ?");
            $stmt->execute([$make_id, $model_name, $year_start, $year_end, $model_id]);
            logActivity($pdo, $user_id, 'car_model_updated', ['id' => $model_id, 'name' => $model_name]);
            $feedback_message = "Car model updated."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $category_id = intval($_POST['product_category']);
    $product_name = trim($_POST['product_name']);
    $product_desc = trim($_POST['product_description']);
    $manufacturer_part = trim($_POST['manufacturer_part_number']);
    $active_tab = 'manage-products';
    
    if ($product_id > 0 && $category_id > 0 && !empty($product_name)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, manufacturer_part_number = ? WHERE id = ?");
            $stmt->execute([$category_id, $product_name, $product_desc, $manufacturer_part ?: null, $product_id]);

            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/media/products/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($tmp_name) && is_uploaded_file($tmp_name) && in_array($_FILES['product_images']['type'][$key], $allowed_types)) {
                        $unique_name = generateUniqueFilename($_FILES['product_images']['name'][$key]);
                        if (move_uploaded_file($tmp_name, $upload_dir . $unique_name)) {
                            $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                            $stmt_img->execute([$product_id, $unique_name]);
                        }
                    }
                }
            }

            $pdo->prepare("DELETE FROM product_vehicle_compatibility WHERE product_id = ?")->execute([$product_id]);
            if (isset($_POST['compatible_models']) && is_array($_POST['compatible_models'])) {
                $stmt_compat = $pdo->prepare("INSERT INTO product_vehicle_compatibility (product_id, model_id) VALUES (?, ?)");
                foreach ($_POST['compatible_models'] as $model_id) {
                    if (intval($model_id) > 0) $stmt_compat->execute([$product_id, intval($model_id)]);
                }
            }

            $pdo->commit();
            logActivity($pdo, $user_id, 'product_updated', ['product_id' => $product_id, 'name' => $product_name]);
            $feedback_message = "Product updated successfully."; 
            $feedback_type = "success";
        } catch(Exception $e) {
            $pdo->rollBack();
            $feedback_message = "Error updating product: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

// =============================================================================
// CREATE HANDLERS
// =============================================================================

if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $active_tab = 'prerequisites';
    if (!empty($category_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO product_categories (name, description) VALUES (?, ?)");
            $stmt->execute([$category_name, trim($_POST['category_description'])]);
            logActivity($pdo, $user_id, 'category_created', ['name' => $category_name]);
            $feedback_message = "Category created."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['add_car_make'])) {
    $make_name = trim($_POST['make_name']);
    $active_tab = 'prerequisites';
    if (!empty($make_name)) {
        try {
            $pdo->prepare("INSERT INTO car_makes (name) VALUES (?)")->execute([$make_name]);
            logActivity($pdo, $user_id, 'car_make_created', ['name' => $make_name]);
            $feedback_message = "Car make created."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['add_car_model'])) {
    $make_id = intval($_POST['make_id']);
    $model_name = trim($_POST['model_name']);
    $year_start = !empty($_POST['year_start']) ? intval($_POST['year_start']) : null;
    $year_end = !empty($_POST['year_end']) ? intval($_POST['year_end']) : null;
    $active_tab = 'prerequisites';
    if ($make_id > 0 && !empty($model_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO car_models (make_id, name, year_start, year_end) VALUES (?, ?, ?, ?)");
            $stmt->execute([$make_id, $model_name, $year_start, $year_end]);
            logActivity($pdo, $user_id, 'car_model_created', ['model' => $model_name]);
            $feedback_message = "Car model created."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $feedback_message = "Error: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['create_product'])) {
    $category_id = intval($_POST['product_category']);
    $product_name = trim($_POST['product_name']);
    $active_tab = 'product';
    if ($category_id > 0 && !empty($product_name)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, manufacturer_part_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category_id, $product_name, trim($_POST['product_description']), trim($_POST['manufacturer_part_number']) ?: null]);
            $product_id = $pdo->lastInsertId();
            
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $upload_dir = __DIR__ . '/media/products/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $is_first = true;
                foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                        $unique_name = generateUniqueFilename($_FILES['product_images']['name'][$key]);
                        if (move_uploaded_file($tmp_name, $upload_dir . $unique_name)) {
                            $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)");
                            $stmt_img->execute([$product_id, $unique_name, $is_first ? 1 : 0]);
                            $is_first = false;
                        }
                    }
                }
            }
            
            if (isset($_POST['compatible_models']) && is_array($_POST['compatible_models'])) {
                $stmt_compat = $pdo->prepare("INSERT INTO product_vehicle_compatibility (product_id, model_id) VALUES (?, ?)");
                foreach ($_POST['compatible_models'] as $model_id) {
                    if (intval($model_id) > 0) $stmt_compat->execute([$product_id, intval($model_id)]);
                }
            }
            
            $pdo->commit();
            logActivity($pdo, $user_id, 'product_created', ['product_id' => $product_id, 'name' => $product_name]);
            $feedback_message = "Product created successfully."; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $pdo->rollBack();
            $feedback_message = "Error creating product: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

if (isset($_POST['add_stock'])) {
    $product_id = intval($_POST['stock_product_id']);
    $quantity = intval($_POST['stock_quantity']);
    $purchase_price = !empty($_POST['purchase_price']) ? floatval($_POST['purchase_price']) : 0.0;
    $selling_price = floatval($_POST['selling_price']);
    $active_tab = 'stock';
    if ($product_id > 0 && $quantity > 0 && $selling_price > 0) {
        try {
            $pdo->beginTransaction();
            $stmt_check = $pdo->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND branch_id = ?");
            $stmt_check->execute([$product_id, $branch_id]);
            $existing = $stmt_check->fetch();
            
            if ($existing) {
                $new_quantity = $existing['quantity'] + $quantity;
                $stmt_update = $pdo->prepare("UPDATE inventory SET quantity = ?, purchase_price = ?, selling_price = ? WHERE id = ?");
                $stmt_update->execute([$new_quantity, $purchase_price, $selling_price, $existing['id']]);
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO inventory (product_id, branch_id, quantity, purchase_price, selling_price) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->execute([$product_id, $branch_id, $quantity, $purchase_price, $selling_price]);
            }
            
            $pdo->commit();
            logActivity($pdo, $user_id, 'stock_added', ['product_id' => $product_id, 'quantity' => $quantity]);
            $feedback_message = "Stock added successfully!"; 
            $feedback_type = "success";
        } catch(PDOException $e) {
            $pdo->rollBack();
            $feedback_message = "Error adding stock: " . $e->getMessage(); 
            $feedback_type = "error";
        }
    }
}

// =============================================================================
// DATA FETCHING
// =============================================================================
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$car_makes = $pdo->query("SELECT * FROM car_makes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$car_models = $pdo->query("SELECT cm.*, mk.name as make_name FROM car_models cm JOIN car_makes mk ON cm.make_id = mk.id ORDER BY mk.name, cm.name")->fetchAll(PDO::FETCH_ASSOC);

// --- SOFT DELETE CHANGE ---
// Added "WHERE p.is_active = 1" to only fetch active products for management and selection.
$products_stmt = $pdo->query("
    SELECT p.*, pc.name as category_name,
        (SELECT GROUP_CONCAT(pi.id, '::', pi.image_path SEPARATOR '||') FROM product_images pi WHERE pi.product_id = p.id) as images,
        (SELECT GROUP_CONCAT(pvc.model_id) FROM product_vehicle_compatibility pvc WHERE pvc.product_id = p.id) as compatible_models_ids
    FROM products p JOIN product_categories pc ON p.category_id = pc.id 
    WHERE p.is_active = 1 
    ORDER BY p.name ASC");
$all_products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- SOFT DELETE CHANGE ---
// Added "AND p.is_active = 1" to only show inventory for active products.
$inventory_stmt = $pdo->prepare("
    SELECT i.*, p.name as product_name, p.manufacturer_part_number, pc.name as category_name,
           (i.quantity * i.purchase_price) as stock_value, (i.quantity * i.selling_price) as potential_revenue
    FROM inventory i JOIN products p ON i.product_id = p.id JOIN product_categories pc ON p.category_id = pc.id
    WHERE i.branch_id = ? AND p.is_active = 1 
    ORDER BY i.last_updated_at DESC");
$inventory_stmt->execute([$branch_id]);
$branch_inventory = $inventory_stmt->fetchAll(PDO::FETCH_ASSOC);

$logs_stmt = $pdo->prepare("SELECT al.*, u.name as user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id 
    WHERE al.action LIKE '%stock%' OR al.action LIKE '%product%' OR al.action LIKE '%category%' ORDER BY al.timestamp DESC LIMIT 20");
$logs_stmt->execute();
$recent_logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="mf-main-content">
    <div class="mf-page-title">
        <h1><i class="fas fa-boxes"></i> Inventory Management</h1>
        <p>Manage products, stock, and prerequisites for <strong><?php echo htmlspecialchars($_SESSION['branch_name']); ?></strong></p>
    </div>
    
    <?php if ($feedback_message): ?>
        <div class="mf-feedback <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <div class="mf-tabs">
        <button class="mf-tab-btn <?php echo $active_tab === 'stock' ? 'active' : ''; ?>" data-tab="stock">Add Stock</button>
        <button class="mf-tab-btn <?php echo $active_tab === 'product' ? 'active' : ''; ?>" data-tab="product">Create Product</button>
        <button class="mf-tab-btn <?php echo $active_tab === 'manage-products' ? 'active' : ''; ?>" data-tab="manage-products">Manage Products</button>
        <button class="mf-tab-btn <?php echo $active_tab === 'prerequisites' ? 'active' : ''; ?>" data-tab="prerequisites">Prerequisites</button>
        <button class="mf-tab-btn <?php echo $active_tab === 'inventory' ? 'active' : ''; ?>" data-tab="inventory">Current Inventory</button>
        <button class="mf-tab-btn <?php echo $active_tab === 'logs' ? 'active' : ''; ?>" data-tab="logs">Activity Logs</button>
    </div>

    <!-- TAB 1: Add Stock -->
    <div class="mf-tab-content <?php echo $active_tab === 'stock' ? 'active' : ''; ?>" id="stock">
        <div class="mf-form-panel">
            <h2><i class="fas fa-plus-circle"></i> Add Stock to Branch</h2>
            <form method="POST" class="mf-form">
                <input type="hidden" name="active_tab" value="stock">
                <div class="mf-form-row">
                    <div class="mf-form-group">
                        <label for="stock_product_id">Select Product *</label>
                        <select name="stock_product_id" id="stock_product_id" required>
                            <option value="">-- Choose Product --</option>
                            <?php foreach ($all_products as $prod): ?>
                                <option value="<?php echo $prod['id']; ?>">
                                    <?php echo htmlspecialchars($prod['name'] . ' (' . $prod['category_name'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mf-form-row">
                    <div class="mf-form-group">
                        <label for="stock_quantity">Quantity *</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="1" required>
                    </div>
                    <div class="mf-form-group">
                        <label for="purchase_price">Purchase Price (per unit)</label>
                        <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0">
                    </div>
                    <div class="mf-form-group">
                        <label for="selling_price">Selling Price (per unit) *</label>
                        <input type="number" name="selling_price" id="selling_price" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mf-stock-calculator">
                    <h3>Auto Calculator</h3>
                    <div class="calc-results">
                        <div class="calc-item"><span>Stock Value:</span><strong id="calc_stock_value">Ksh 0.00</strong></div>
                        <div class="calc-item"><span>Expected Revenue:</span><strong id="calc_revenue">Ksh 0.00</strong></div>
                        <div class="calc-item profit"><span>Expected Profit:</span><strong id="calc_profit">Ksh 0.00</strong></div>
                        <div class="calc-item"><span>Profit Margin:</span><strong id="calc_margin">0%</strong></div>
                    </div>
                </div>
                <button type="submit" name="add_stock" class="mf-btn-primary">
                    <i class="fas fa-save"></i> Add Stock
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 2: Create Product -->
    <div class="mf-tab-content <?php echo $active_tab === 'product' ? 'active' : ''; ?>" id="product">
        <div class="mf-form-panel">
            <h2><i class="fas fa-box-open"></i> Create New Product</h2>
            <form method="POST" enctype="multipart/form-data" class="mf-form">
                <input type="hidden" name="active_tab" value="product">
                <div class="mf-form-row">
                    <div class="mf-form-group">
                        <label for="product_category">Category *</label>
                        <select name="product_category" id="product_category" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mf-form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" name="product_name" id="product_name" required>
                    </div>
                </div>
                <div class="mf-form-group">
                    <label for="manufacturer_part_number">Manufacturer Part Number (Optional)</label>
                    <input type="text" name="manufacturer_part_number" id="manufacturer_part_number">
                </div>
                <div class="mf-form-group">
                    <label for="product_description">Product Description</label>
                    <textarea name="product_description" id="product_description" rows="5"></textarea>
                </div>
                <div class="mf-form-group">
                    <label for="product_images">Product Images</label>
                    <input type="file" name="product_images[]" id="product_images" multiple accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="mf-form-group">
                    <label>Compatible Vehicles</label>
                    <div class="mf-checkbox-grid">
                        <?php foreach ($car_models as $model): ?>
                            <label class="mf-checkbox-label">
                                <input type="checkbox" name="compatible_models[]" value="<?php echo $model['id']; ?>">
                                <?php echo htmlspecialchars($model['make_name'] . ' ' . $model['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" name="create_product" class="mf-btn-primary">
                    <i class="fas fa-plus"></i> Create Product
                </button>
            </form>
        </div>
    </div>
    
    <!-- TAB 3: Manage Products -->
    <div class="mf-tab-content <?php echo $active_tab === 'manage-products' ? 'active' : ''; ?>" id="manage-products">
        <div class="mf-table-container">
            <h2><i class="fas fa-edit"></i> Manage Existing Products</h2>
            <table class="mf-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Part #</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_products as $prod): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['manufacturer_part_number'] ?? 'N/A'); ?></td>
                            <td>
                                <button class="mf-btn-edit" data-modal-target="#edit-product-modal"
                                    data-product-id="<?php echo $prod['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($prod['name'] ?? '', ENT_QUOTES); ?>"
                                    data-category-id="<?php echo $prod['category_id']; ?>"
                                    data-part-number="<?php echo htmlspecialchars($prod['manufacturer_part_number'] ?? '', ENT_QUOTES); ?>"
                                    data-description="<?php echo htmlspecialchars($prod['description'] ?? '', ENT_QUOTES); ?>"
                                    data-images="<?php echo htmlspecialchars($prod['images'] ?? '', ENT_QUOTES); ?>"
                                    data-compatibility="<?php echo htmlspecialchars($prod['compatible_models_ids'] ?? '', ENT_QUOTES); ?>">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Deactivate this product? It will be hidden from lists but sales history will be kept.');">
                                    <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                    <input type="hidden" name="active_tab" value="manage-products">
                                    <button type="submit" name="delete_product" class="mf-btn-delete">
                                        <i class="fas fa-trash"></i> Deactivate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: Prerequisites -->
    <div class="mf-tab-content <?php echo $active_tab === 'prerequisites' ? 'active' : ''; ?>" id="prerequisites">
        <div class="mf-prerequisites-grid">
            <!-- Product Categories -->
            <div class="mf-form-panel">
                <h3><i class="fas fa-tags"></i> Product Categories</h3>
                <form method="POST" class="mf-form-compact">
                    <input type="hidden" name="active_tab" value="prerequisites">
                    <div class="mf-form-group">
                        <input type="text" name="category_name" placeholder="Category Name" required>
                    </div>
                    <div class="mf-form-group">
                        <textarea name="category_description" placeholder="Description (optional)" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add_category" class="mf-btn-secondary">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </form>
                <div class="mf-existing-list">
                    <strong>Existing Categories:</strong>
                    <ul>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                <div class="mf-action-buttons">
                                    <button class="mf-btn-edit-small" data-modal-target="#edit-category-modal" 
                                        data-id="<?php echo $cat['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>" 
                                        data-description="<?php echo htmlspecialchars($cat['description'] ?? '', ENT_QUOTES); ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category? Only categories with no products can be deleted.');">
                                        <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                        <input type="hidden" name="active_tab" value="prerequisites">
                                        <button type="submit" name="delete_category" class="mf-btn-delete-small">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Car Makes -->
            <div class="mf-form-panel">
                <h3><i class="fas fa-car"></i> Car Makes</h3>
                <form method="POST" class="mf-form-compact">
                    <input type="hidden" name="active_tab" value="prerequisites">
                    <div class="mf-form-group">
                        <input type="text" name="make_name" placeholder="Make Name (e.g., Toyota)" required>
                    </div>
                    <button type="submit" name="add_car_make" class="mf-btn-secondary">
                        <i class="fas fa-plus"></i> Add Make
                    </button>
                </form>
                <div class="mf-existing-list">
                    <strong>Existing Makes:</strong>
                    <ul>
                        <?php foreach ($car_makes as $make): ?>
                            <li>
                                <span><?php echo htmlspecialchars($make['name']); ?></span>
                                <div class="mf-action-buttons">
                                    <button class="mf-btn-edit-small" data-modal-target="#edit-make-modal" 
                                        data-id="<?php echo $make['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($make['name'], ENT_QUOTES); ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this make? Only makes with no models can be deleted.');">
                                        <input type="hidden" name="make_id" value="<?php echo $make['id']; ?>">
                                        <input type="hidden" name="active_tab" value="prerequisites">
                                        <button type="submit" name="delete_car_make" class="mf-btn-delete-small">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Car Models -->
            <div class="mf-form-panel">
                <h3><i class="fas fa-cogs"></i> Car Models</h3>
                <form method="POST" class="mf-form-compact">
                    <input type="hidden" name="active_tab" value="prerequisites">
                    <div class="mf-form-group">
                        <select name="make_id" required>
                            <option value="">-- Select Make --</option>
                            <?php foreach ($car_makes as $make): ?>
                                <option value="<?php echo $make['id']; ?>"><?php echo htmlspecialchars($make['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mf-form-group">
                        <input type="text" name="model_name" placeholder="Model Name (e.g., Corolla)" required>
                    </div>
                    <div class="mf-form-row-inline">
                        <input type="number" name="year_start" placeholder="Year Start">
                        <input type="number" name="year_end" placeholder="Year End">
                    </div>
                    <button type="submit" name="add_car_model" class="mf-btn-secondary">
                        <i class="fas fa-plus"></i> Add Model
                    </button>
                </form>
                <div class="mf-existing-list">
                    <strong>Existing Models:</strong>
                    <ul>
                        <?php foreach ($car_models as $model): ?>
                            <li>
                                <span><?php echo htmlspecialchars($model['make_name'] . ' ' . $model['name']); ?></span>
                                <div class="mf-action-buttons">
                                    <button class="mf-btn-edit-small" data-modal-target="#edit-model-modal" 
                                        data-id="<?php echo $model['id']; ?>" 
                                        data-make-id="<?php echo $model['make_id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($model['name'], ENT_QUOTES); ?>" 
                                        data-year-start="<?php echo htmlspecialchars($model['year_start'] ?? '', ENT_QUOTES); ?>" 
                                        data-year-end="<?php echo htmlspecialchars($model['year_end'] ?? '', ENT_QUOTES); ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this model?');">
                                        <input type="hidden" name="model_id" value="<?php echo $model['id']; ?>">
                                        <input type="hidden" name="active_tab" value="prerequisites">
                                        <button type="submit" name="delete_car_model" class="mf-btn-delete-small">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB 5: Current Inventory -->
    <div class="mf-tab-content <?php echo $active_tab === 'inventory' ? 'active' : ''; ?>" id="inventory">
        <div class="mf-table-container">
            <h2><i class="fas fa-warehouse"></i> Branch Inventory Overview</h2>
            <?php if (empty($branch_inventory)): ?>
                <p class="mf-empty-state">No inventory items yet.</p>
            <?php else: ?>
                <table class="mf-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Part #</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Stock Value</th>
                            <th>Potential Revenue</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_stock_value = 0; 
                        $total_potential = 0; 
                        foreach ($branch_inventory as $item): 
                            $total_stock_value += $item['stock_value']; 
                            $total_potential += $item['potential_revenue']; 
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['manufacturer_part_number'] ?? 'N/A'); ?></td>
                                <td><span class="mf-badge"><?php echo $item['quantity']; ?></span></td>
                                <td>Ksh <?php echo number_format($item['purchase_price'], 2); ?></td>
                                <td>Ksh <?php echo number_format($item['selling_price'], 2); ?></td>
                                <td>Ksh <?php echo number_format($item['stock_value'], 2); ?></td>
                                <td class="text-success">Ksh <?php echo number_format($item['potential_revenue'], 2); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($item['last_updated_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this stock? This action will be logged and cannot be undone.');">
                                        <input type="hidden" name="inventory_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="active_tab" value="inventory">
                                        <button type="submit" name="delete_stock" class="mf-btn-delete-small">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">TOTALS</th>
                            <th>Ksh <?php echo number_format($total_stock_value, 2); ?></th>
                            <th class="text-success">Ksh <?php echo number_format($total_potential, 2); ?></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB 6: Activity Logs -->
    <div class="mf-tab-content <?php echo $active_tab === 'logs' ? 'active' : ''; ?>" id="logs">
        <div class="mf-logs-container">
            <h2><i class="fas fa-history"></i> Recent Activity Logs</h2>
            <?php foreach ($recent_logs as $log): ?>
                <div class="mf-log-entry">
                    <div class="log-icon"><i class="fas fa-circle"></i></div>
                    <div class="log-content">
                        <strong><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></strong>
                        <span class="log-action"><?php echo htmlspecialchars(str_replace('_', ' ', $log['action'])); ?></span>
                        <?php if ($log['details']): ?>
                            <div class="log-details"><?php echo htmlspecialchars($log['details']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="log-time"><?php echo date('M d, H:i', strtotime($log['timestamp'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- MODALS for Editing -->
<div class="mf-modal" id="edit-category-modal">
    <div class="mf-modal-content">
        <span class="mf-modal-close">&times;</span>
        <h2>Edit Category</h2>
        <form method="POST">
            <input type="hidden" name="category_id" id="edit_category_id">
            <input type="hidden" name="active_tab" value="prerequisites">
            <div class="mf-form-group">
                <label>Name</label>
                <input type="text" name="category_name" id="edit_category_name" required>
            </div>
            <div class="mf-form-group">
                <label>Description</label>
                <textarea name="category_description" id="edit_category_description" rows="3"></textarea>
            </div>
            <button type="submit" name="update_category" class="mf-btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<div class="mf-modal" id="edit-make-modal">
    <div class="mf-modal-content">
        <span class="mf-modal-close">&times;</span>
        <h2>Edit Car Make</h2>
        <form method="POST">
            <input type="hidden" name="make_id" id="edit_make_id">
            <input type="hidden" name="active_tab" value="prerequisites">
            <div class="mf-form-group">
                <label>Name</label>
                <input type="text" name="make_name" id="edit_make_name" required>
            </div>
            <button type="submit" name="update_car_make" class="mf-btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<div class="mf-modal" id="edit-model-modal">
    <div class="mf-modal-content">
        <span class="mf-modal-close">&times;</span>
        <h2>Edit Car Model</h2>
        <form method="POST">
            <input type="hidden" name="model_id" id="edit_model_id">
            <input type="hidden" name="active_tab" value="prerequisites">
            <div class="mf-form-group">
                <label>Make</label>
                <select name="make_id" id="edit_model_make_id" required>
                    <?php foreach ($car_makes as $make): ?>
                        <option value="<?php echo $make['id']; ?>"><?php echo htmlspecialchars($make['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mf-form-group">
                <label>Model Name</label>
                <input type="text" name="model_name" id="edit_model_name" required>
            </div>
            <div class="mf-form-row">
                <div class="mf-form-group">
                    <label>Year Start</label>
                    <input type="number" name="year_start" id="edit_model_year_start">
                </div>
                <div class="mf-form-group">
                    <label>Year End</label>
                    <input type="number" name="year_end" id="edit_model_year_end">
                </div>
            </div>
            <button type="submit" name="update_car_model" class="mf-btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<div class="mf-modal large" id="edit-product-modal">
    <div class="mf-modal-content scrollable">
        <span class="mf-modal-close">&times;</span>
        <h2>Edit Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">
            <input type="hidden" name="active_tab" value="manage-products">
            
            <h3>Core Details</h3>
            <div class="mf-form-row">
                <div class="mf-form-group">
                    <label>Category</label>
                    <select name="product_category" id="edit_product_category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mf-form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" id="edit_product_name" required>
                </div>
            </div>
            
            <div class="mf-form-group">
                <label>Manufacturer Part #</label>
                <input type="text" name="manufacturer_part_number" id="edit_manufacturer_part_number">
            </div>
            
            <div class="mf-form-group">
                <label>Description</label>
                <textarea name="product_description" id="edit_product_description" rows="4"></textarea>
            </div>
            
            <h3>Manage Images</h3>
            <div id="edit_product_images_container" class="mf-image-grid"></div>
            
            <div class="mf-form-group">
                <label>Add New Images</label>
                <input type="file" name="product_images[]" multiple accept="image/jpeg,image/png,image/webp">
            </div>
            
            <h3>Vehicle Compatibility</h3>
            <div class="mf-checkbox-grid" id="edit_product_compatibility_container">
                <?php foreach ($car_models as $model): ?>
                    <label class="mf-checkbox-label">
                        <input type="checkbox" name="compatible_models[]" value="<?php echo $model['id']; ?>">
                        <?php echo htmlspecialchars($model['make_name'] . ' ' . $model['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" name="update_product" class="mf-btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<style>
:root {
    --mf-primary: #28a745;
    --mf-secondary: #007bff;
    --mf-success: #28a745;
    --mf-danger: #dc3545;
    --mf-dark: #212529;
    --mf-grey: #6c757d;
    --mf-light-bg: #f8f9fa;
    --mf-border: #dee2e6;
}

.mf-main-content {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.mf-page-title h1 {
    font-size: 1.8rem;
    margin-bottom: 8px;
}

.mf-page-title p {
    color: var(--mf-grey);
    margin: 0;
}

.mf-feedback {
    padding: 15px 20px;
    border-radius: 8px;
    margin: 20px 0;
    font-weight: 500;
}

.mf-feedback.success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid var(--mf-success);
}

.mf-feedback.error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid var(--mf-danger);
}

.mf-tabs {
    display: flex;
    gap: 5px;
    margin: 20px 0;
    border-bottom: 2px solid var(--mf-border);
    overflow-x: auto;
}

.mf-tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 600;
    color: var(--mf-grey);
    transition: all 0.3s;
    white-space: nowrap;
}

.mf-tab-btn:hover {
    color: var(--mf-primary);
}

.mf-tab-btn.active {
    color: var(--mf-primary);
    border-bottom-color: var(--mf-primary);
}

.mf-tab-content {
    display: none;
    padding: 20px 0;
    animation: fadeIn 0.3s;
}

.mf-tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.mf-form-panel {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.mf-form-panel h2,
.mf-form-panel h3 {
    margin: 0 0 20px;
    color: var(--mf-dark);
}

.mf-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.mf-form-group {
    display: flex;
    flex-direction: column;
}

.mf-form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--mf-dark);
}

.mf-form-group input,
.mf-form-group select,
.mf-form-group textarea {
    padding: 10px 15px;
    border: 1px solid var(--mf-border);
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.mf-form-group input:focus,
.mf-form-group select:focus,
.mf-form-group textarea:focus {
    outline: none;
    border-color: var(--mf-primary);
}

.mf-stock-calculator {
    background: var(--mf-light-bg);
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.calc-results {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.calc-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 15px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.calc-item.profit strong {
    color: var(--mf-success);
}

.mf-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 10px;
    max-height: 250px;
    overflow-y: auto;
    padding: 15px;
    background: var(--mf-light-bg);
    border-radius: 8px;
    border: 1px solid var(--mf-border);
}

.mf-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 5px;
}

.mf-checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.mf-btn-primary,
.mf-btn-secondary {
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.mf-btn-primary {
    background: var(--mf-primary);
    color: white;
}

.mf-btn-primary:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.mf-btn-secondary {
    background: var(--mf-secondary);
    color: white;
}

.mf-btn-secondary:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.mf-btn-edit {
    background: var(--mf-secondary);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding: 6px 12px;
    font-size: 0.85rem;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.mf-btn-edit:hover {
    background: #0056b3;
}

.mf-btn-delete {
    background: var(--mf-danger);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding: 6px 12px;
    font-size: 0.85rem;
    margin-left: 5px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.mf-btn-delete:hover {
    background: #c82333;
}

.mf-btn-edit-small,
.mf-btn-delete-small {
    border: none;
    border-radius: 4px;
    cursor: pointer;
    padding: 4px 8px;
    font-size: 0.75rem;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.mf-btn-edit-small {
    background: var(--mf-secondary);
    color: white;
}

.mf-btn-edit-small:hover {
    background: #0056b3;
}

.mf-btn-delete-small {
    background: var(--mf-danger);
    color: white;
}

.mf-btn-delete-small:hover {
    background: #c82333;
}

.mf-prerequisites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.mf-form-compact {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mf-form-compact .mf-form-group {
    margin: 0;
}

.mf-form-compact input,
.mf-form-compact select,
.mf-form-compact textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid var(--mf-border);
    border-radius: 8px;
    font-size: 14px;
}

.mf-form-compact button {
    width: 100%;
    padding: 10px;
}

.mf-form-row-inline {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.mf-form-row-inline input {
    padding: 10px 15px;
    border: 1px solid var(--mf-border);
    border-radius: 8px;
}

.mf-existing-list {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid var(--mf-border);
}

.mf-existing-list strong {
    display: block;
    margin-bottom: 10px;
    color: var(--mf-dark);
}

.mf-existing-list ul {
    list-style: none;
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
}

.mf-existing-list li {
    padding: 10px 12px;
    background: var(--mf-light-bg);
    margin-bottom: 6px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s;
}

.mf-existing-list li:hover {
    background: #e9ecef;
}

.mf-action-buttons {
    display: flex;
    gap: 5px;
}

.mf-table-container {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.mf-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.mf-table th,
.mf-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--mf-border);
}

.mf-table th {
    background: var(--mf-light-bg);
    font-weight: 600;
    color: var(--mf-dark);
}

.mf-table tbody tr:hover {
    background: var(--mf-light-bg);
}

.mf-table tfoot th {
    background: var(--mf-dark);
    color: white;
    font-size: 1.1rem;
}

.mf-badge {
    display: inline-block;
    padding: 4px 12px;
    background: var(--mf-primary);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.text-success {
    color: var(--mf-success) !important;
    font-weight: 600;
}

.mf-empty-state {
    text-align: center;
    padding: 40px;
    color: var(--mf-grey);
    font-size: 1.1rem;
}

.mf-logs-container {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mf-log-entry {
    display: grid;
    grid-template-columns: 40px 1fr auto;
    gap: 15px;
    padding: 15px;
    border-left: 3px solid var(--mf-primary);
    background: var(--mf-light-bg);
    margin-bottom: 10px;
    border-radius: 6px;
    transition: all 0.3s;
}

.mf-log-entry:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.log-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--mf-primary);
}

.log-content strong {
    display: block;
    margin-bottom: 5px;
    color: var(--mf-dark);
}

.log-action {
    color: var(--mf-grey);
    font-size: 0.9rem;
    text-transform: capitalize;
}

.log-details {
    margin-top: 8px;
    padding: 8px 12px;
    background: white;
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--mf-grey);
}

.log-time {
    font-size: 0.85rem;
    color: var(--mf-grey);
    white-space: nowrap;
}

/* Modal Styles */
.mf-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    align-items: center;
    justify-content: center;
}

.mf-modal-content {
    background-color: #fefefe;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.mf-modal.large .mf-modal-content {
    max-width: 900px;
}

.mf-modal-content.scrollable {
    overflow-y: auto;
}

.mf-modal-close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.mf-modal-close:hover {
    color: var(--mf-danger);
}

.mf-image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.mf-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.mf-image-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    display: block;
    border: 2px solid var(--mf-border);
    border-radius: 8px;
}

.mf-image-item .delete-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--mf-danger);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: all 0.3s;
}

.mf-image-item .delete-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .mf-main-content {
        padding: 15px;
    }
    
    .mf-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    
    .mf-tab-btn {
        padding: 10px 16px;
        font-size: 0.9rem;
    }
    
    .mf-form-row {
        grid-template-columns: 1fr;
    }
    
    .mf-prerequisites-grid {
        grid-template-columns: 1fr;
    }
    
    .calc-results {
        grid-template-columns: 1fr;
    }
    
    .mf-modal-content {
        width: 95%;
        padding: 20px;
    }
    
    .mf-table-container {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .mf-page-title h1 {
        font-size: 1.5rem;
    }
    
    .mf-form-panel {
        padding: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching Logic
    const tabContainer = document.querySelector('.mf-tabs');
    if (tabContainer) {
        tabContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('mf-tab-btn')) {
                document.querySelectorAll('.mf-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.mf-tab-content').forEach(c => c.classList.remove('active'));
                e.target.classList.add('active');
                document.getElementById(e.target.dataset.tab).classList.add('active');
            }
        });
    }

    // Stock Calculator
    const qIn = document.getElementById('stock_quantity');
    const pIn = document.getElementById('purchase_price');
    const sIn = document.getElementById('selling_price');
    
    function calcStock() {
        const q = parseFloat(qIn.value) || 0;
        const p = parseFloat(pIn.value) || 0;
        const s = parseFloat(sIn.value) || 0;
        const val = q * p;
        const rev = q * s;
        const prof = rev - val;
        const marg = rev > 0 ? (prof / rev) * 100 : 0;
        
        document.getElementById('calc_stock_value').textContent = 'Ksh ' + val.toFixed(2);
        document.getElementById('calc_revenue').textContent = 'Ksh ' + rev.toFixed(2);
        document.getElementById('calc_profit').textContent = 'Ksh ' + prof.toFixed(2);
        document.getElementById('calc_margin').textContent = marg.toFixed(2) + '%';
    }
    
    if (qIn && pIn && sIn) {
        [qIn, pIn, sIn].forEach(el => el.addEventListener('input', calcStock));
    }

    // Modal Handling Logic
    document.body.addEventListener('click', function(event) {
        const button = event.target.closest('[data-modal-target]');
        if (button) {
            const modal = document.querySelector(button.dataset.modalTarget);
            if(modal) {
                populateModal(modal, button.dataset);
                modal.style.display = 'flex';
            }
        }
        
        if (event.target.classList.contains('mf-modal-close')) {
            event.target.closest('.mf-modal').style.display = 'none';
        }
        
        if (event.target.classList.contains('mf-modal')) {
            event.target.style.display = 'none';
        }
    });
    
    function populateModal(modal, data) {
        if (modal.id === 'edit-category-modal') {
            modal.querySelector('#edit_category_id').value = data.id;
            modal.querySelector('#edit_category_name').value = data.name;
            modal.querySelector('#edit_category_description').value = data.description;
        }
        
        if (modal.id === 'edit-make-modal') {
            modal.querySelector('#edit_make_id').value = data.id;
            modal.querySelector('#edit_make_name').value = data.name;
        }
        
        if (modal.id === 'edit-model-modal') {
            modal.querySelector('#edit_model_id').value = data.id;
            modal.querySelector('#edit_model_make_id').value = data.makeId;
            modal.querySelector('#edit_model_name').value = data.name;
            modal.querySelector('#edit_model_year_start').value = data.yearStart;
            modal.querySelector('#edit_model_year_end').value = data.yearEnd;
        }
        
        if (modal.id === 'edit-product-modal') {
            modal.querySelector('#edit_product_id').value = data.productId;
            modal.querySelector('#edit_product_name').value = data.name;
            modal.querySelector('#edit_product_category').value = data.categoryId;
            modal.querySelector('#edit_manufacturer_part_number').value = data.partNumber;
            modal.querySelector('#edit_product_description').value = data.description;
            
            // Handle Images with Fixed Path
            const imagesContainer = modal.querySelector('#edit_product_images_container');
            imagesContainer.innerHTML = '';
            if (data.images) {
                const images = data.images.split('||');
                images.forEach(imgData => {
                    const [id, path] = imgData.split('::');
                    if(id && path) {
                        const item = document.createElement('div');
                        item.className = 'mf-image-item';
                        // Fixed path: Use correct relative path from dashboard to media folder
                        item.innerHTML = `
                            <img src="media/products/${path}" alt="Product Image">
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this image permanently?');">
                                <input type="hidden" name="image_id" value="${id}">
                                <input type="hidden" name="from_tab" value="manage-products">
                                <input type="hidden" name="active_tab" value="manage-products">
                                <button type="submit" name="delete_image" class="delete-btn" title="Delete Image">&times;</button>
                            </form>`;
                        imagesContainer.appendChild(item);
                    }
                });
            }

            // Handle Vehicle Compatibility
            const checkboxes = modal.querySelectorAll('#edit_product_compatibility_container input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            if(data.compatibility) {
                const selectedIds = data.compatibility.split(',');
                checkboxes.forEach(cb => {
                    if (selectedIds.includes(cb.value)) cb.checked = true;
                });
            }
        }
    }

    // Auto-hide feedback messages
    setTimeout(() => {
        const feedback = document.querySelector('.mf-feedback');
        if (feedback) {
            feedback.style.transition = 'opacity 0.5s';
            feedback.style.opacity = '0';
            setTimeout(() => feedback.remove(), 500);
        }
    }, 7000);
});
</script>

<?php require_once 'ic/afooter.php'; ?>
