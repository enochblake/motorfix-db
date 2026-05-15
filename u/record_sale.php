<?php
/**
 * Motor Fix - Real-Time Sales Recording System (Hybrid Design)
 * * Features:
 * - Professional, mobile-first design for easy multi-product recording.
 * - Real-time inventory updates with secure database transactions.
 * - Live calculation of item totals, grand total, and payment balance.
 * - Activity logging and detailed email notifications with profit calculation.
 * * @version 2.0 (Merged Professional UI with Secure Backend)
 * @date Thursday, October 16, 2025
 */

require_once 'ic/aheader.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer if it's not already handled by a global autoloader
require_once __DIR__ . '/../vendor/autoload.php';

$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';

// --- HELPER FUNCTIONS ---

function logActivity($pdo, $user_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $ip = hash('sha256', $_SERVER['REMOTE_ADDR']);
        $stmt->execute([$user_id, $action, $details ? json_encode($details) : null, $ip]);
    } catch(PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

function sendSaleNotification($sale_details) {
    if (empty($_ENV['SMTP_HOST'])) {
        error_log("SMTP is not configured. Skipping sale notification email.");
        return;
    }
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

        $mail->setFrom('sales@motorfix.co.ke', 'Motor Fix Sales System');
        $mail->addAddress('info@motorfix.co.ke', 'Motor Fix Management');

        $mail->isHTML(true);
        $mail->Subject = "New Sale Recorded - {$sale_details['branch']}";
        
        $items_html = '';
        foreach ($sale_details['items'] as $item) {
            $items_html .= "<tr><td>{$item['product']}</td><td>{$item['quantity']}</td><td>Ksh " . number_format($item['unit_price'], 2) . "</td><td>Ksh " . number_format($item['total'], 2) . "</td></tr>";
        }
        
        $mail->Body = "
        <h2>Sale Recorded Successfully (ID: {$sale_details['sale_id']})</h2>
        <p><strong>Branch:</strong> {$sale_details['branch']}</p>
        <p><strong>Recorded By:</strong> {$sale_details['user']}</p>
        <p><strong>Date/Time:</strong> {$sale_details['timestamp']}</p><hr>
        <h3>Sale Items:</h3>
        <table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>
            <thead style='background-color: #f4f4f4;'><tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr></thead>
            <tbody>{$items_html}</tbody>
        </table><hr>
        <p><strong>Total Amount:</strong> Ksh " . number_format($sale_details['total'], 2) . "</p>
        <p><strong>Cash Payment:</strong> Ksh " . number_format($sale_details['cash'], 2) . "</p>
        <p><strong>Mobile Payment:</strong> Ksh " . number_format($sale_details['mobile'], 2) . "</p>
        <p><strong>Est. Profit:</strong> Ksh " . number_format($sale_details['profit'], 2) . "</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Sale Email Error: " . $mail->ErrorInfo);
    }
}

// --- PROCESS SALE SUBMISSION ---
if (isset($_POST['record_sale'])) {
    $product_ids = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $cash_payment = floatval($_POST['cash_payment'] ?? 0);
    $mobile_payment = floatval($_POST['mobile_payment'] ?? 0);
    
    if (empty($product_ids)) {
        $feedback_message = "Please select at least one product.";
        $feedback_type = "error";
    } else {
        $pdo->beginTransaction();
        try {
            $total_amount = 0;
            $total_profit = 0;
            $processed_items = [];

            // Step 1: Pre-process and validate all items before database writes
            foreach ($product_ids as $index => $product_id) {
                $product_id = intval($product_id);
                $quantity_sold = floatval($quantities[$index]);
                
                if ($product_id <= 0 || $quantity_sold <= 0) continue;
                
                // CRITICAL: Lock the row to prevent race conditions
                $stmt = $pdo->prepare("
                    SELECT i.id as inventory_id, i.product_id, i.quantity, i.selling_price, i.purchase_price, p.name 
                    FROM inventory i JOIN products p ON i.product_id = p.id 
                    WHERE i.product_id = ? AND i.branch_id = ? FOR UPDATE
                ");
                $stmt->execute([$product_id, $branch_id]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$item) throw new Exception("Product ID #{$product_id} not found.");
                if ($item['quantity'] < $quantity_sold) {
                    throw new Exception("Not enough stock for '{$item['name']}'. Available: {$item['quantity']}, Requested: {$quantity_sold}.");
                }
                
                $item_total = $quantity_sold * $item['selling_price'];
                $item_cost = $quantity_sold * ($item['purchase_price'] ?? 0);
                
                $total_amount += $item_total;
                $total_profit += ($item_total - $item_cost);
                
                $item['quantity_sold'] = $quantity_sold;
                $processed_items[] = $item;
            }
            
            if (empty($processed_items)) throw new Exception("No valid items to process.");
            
            // Step 2: Create the main sale record
            $stmt = $pdo->prepare("INSERT INTO sales_records (branch_id, user_id, total_amount, amount_paid_cash, amount_paid_mobile) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$branch_id, $user_id, $total_amount, $cash_payment, $mobile_payment]);
            $sale_id = $pdo->lastInsertId();
            
            // Step 3: Insert sale items, update inventory, and log movements
            foreach ($processed_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity_sold, selling_price_at_sale, purchase_price_at_sale) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sale_id, $item['product_id'], $item['quantity_sold'], $item['selling_price'], $item['purchase_price']]);
                
                $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity_sold'], $item['inventory_id']]);
                
                $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, branch_id, user_id, quantity_change, movement_type, notes) VALUES (?, ?, ?, ?, 'sale', ?)");
                $stmt->execute([$item['product_id'], $branch_id, $user_id, -$item['quantity_sold'], "Sale ID: {$sale_id}"]);
            }
            
            $pdo->commit();
            
            logActivity($pdo, $user_id, 'sale_recorded', ['sale_id' => $sale_id, 'total' => $total_amount, 'profit' => $total_profit]);
            
            sendSaleNotification([
                'sale_id' => $sale_id,
                'branch' => $_SESSION['branch_name'],
                'user' => $_SESSION['user_name'],
                'timestamp' => date('l, F j, Y \a\t g:i A'),
                'items' => array_map(fn($item) => [
                    'product' => $item['name'], 'quantity' => $item['quantity_sold'],
                    'unit_price' => $item['selling_price'], 'total' => $item['quantity_sold'] * $item['selling_price']
                ], $processed_items),
                'total' => $total_amount, 'cash' => $cash_payment, 'mobile' => $mobile_payment, 'profit' => $total_profit
            ]);
            
            $feedback_message = "Sale #{$sale_id} recorded! Total: Ksh " . number_format($total_amount, 2);
            $feedback_type = "success";
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $feedback_message = "Transaction Failed: " . $e->getMessage();
            $feedback_type = "error";
        }
    }
}

// --- DATA FETCHING for the page ---
$available_inventory_stmt = $pdo->prepare("SELECT i.product_id, p.name, i.quantity, i.selling_price FROM inventory i JOIN products p ON i.product_id = p.id WHERE i.branch_id = ? AND i.quantity > 0 ORDER BY p.name ASC");
$available_inventory_stmt->execute([$branch_id]);
$available_inventory = $available_inventory_stmt->fetchAll(PDO::FETCH_ASSOC);

$today_summary_stmt = $pdo->prepare("SELECT COUNT(DISTINCT sr.id) as total_sales, COALESCE(SUM(sr.total_amount), 0) as total_revenue, COALESCE(SUM((si.selling_price_at_sale - si.purchase_price_at_sale) * si.quantity_sold), 0) as total_profit FROM sales_records sr LEFT JOIN sale_items si ON sr.id = si.sale_id WHERE sr.branch_id = ? AND DATE(sr.sale_timestamp) = CURDATE()");
$today_summary_stmt->execute([$branch_id]);
$today_summary = $today_summary_stmt->fetch(PDO::FETCH_ASSOC);
?>

<main class="mf-main-content">
    <div class="mf-page-header">
        <h1><i class="fas fa-cash-register"></i> Record Sale</h1>
        <p>Quick and easy sales recording for <?php echo htmlspecialchars($_SESSION['branch_name']); ?></p>
    </div>

    <?php if ($feedback_message): ?>
        <div class="mf-alert <?php echo $feedback_type; ?>" id="feedback-message"><?php echo $feedback_message; ?></div>
    <?php endif; ?>

    <div class="mf-summary-cards">
        <div class="summary-card"><div class="card-icon"><i class="fas fa-shopping-cart"></i></div><div class="card-info"><span class="card-label">Sales Today</span><span class="card-value"><?php echo $today_summary['total_sales']; ?></span></div></div>
        <div class="summary-card"><div class="card-icon"><i class="fas fa-money-bill-wave"></i></div><div class="card-info"><span class="card-label">Revenue</span><span class="card-value">Ksh <?php echo number_format($today_summary['total_revenue'], 2); ?></span></div></div>
        <div class="summary-card profit"><div class="card-icon"><i class="fas fa-chart-line"></i></div><div class="card-info"><span class="card-label">Profit</span><span class="card-value">Ksh <?php echo number_format($today_summary['total_profit'], 2); ?></span></div></div>
    </div>

    <div class="mf-sale-form">
        <form method="POST" id="saleForm">
            <input type="hidden" name="record_sale" value="1">
            <div class="form-section">
                <h2><i class="fas fa-box"></i> Sale Items</h2>
                <div id="saleItemsContainer">
                    <!-- JS will populate this -->
                </div>
                <button type="button" class="btn-add-item" onclick="addItem()"><i class="fas fa-plus-circle"></i> Add Item</button>
            </div>

            <div class="form-section payment-section">
                <h2><i class="fas fa-wallet"></i> Payment Details</h2>
                <div class="sale-totals"><div class="total-row main-total"><span>Total Amount:</span><strong id="grandTotal">Ksh 0.00</strong></div></div>
                <div class="form-row">
                    <div class="form-group"><label>Cash Payment</label><input type="number" name="cash_payment" id="cashPayment" step="0.01" min="0" placeholder="0.00" oninput="calculateGrandTotal()"></div>
                    <div class="form-group"><label>Mobile Payment</label><input type="number" name="mobile_payment" id="mobilePayment" step="0.01" min="0" placeholder="0.00" oninput="calculateGrandTotal()"></div>
                </div>
                <div class="payment-summary">
                    <div class="payment-row"><span>Total Paid:</span><span id="totalPaid">Ksh 0.00</span></div>
                    <div class="payment-row" id="balanceRow"><span>Balance:</span><span id="balance">Ksh 0.00</span></div>
                </div>
                <button type="submit" class="btn-record-sale" id="submitBtn" disabled><i class="fas fa-check-circle"></i> Record Sale</button>
            </div>
        </form>
    </div>
</main>

<style>
:root { --primary: #28a745; --secondary: #007bff; --danger: #dc3545; --warning: #ffc107; --dark: #212529; --grey: #6c757d; --light-bg: #f8f9fa; --border: #dee2e6; }
body { background: var(--light-bg); }
.mf-main-content { padding: 15px; max-width: 800px; margin: 0 auto; }
.mf-page-header { text-align: center; margin-bottom: 20px; }
.mf-page-header h1 { font-size: 1.5rem; margin: 0 0 5px 0; }
.mf-page-header p { color: var(--grey); margin: 0; }
.mf-alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
.mf-alert.success { background: #d4edda; color: #155724; border-left: 4px solid var(--primary); }
.mf-alert.error { background: #f8d7da; color: #721c24; border-left: 4px solid var(--danger); }
.mf-summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
.summary-card { background: white; padding: 15px; border-radius: 10px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.summary-card.profit { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
.card-icon { font-size: 2rem; opacity: 0.8; }
.card-info { display: flex; flex-direction: column; }
.card-label { font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; }
.card-value { font-size: 1.2rem; font-weight: 700; }
.mf-sale-form { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); }
.form-section { padding: 20px; border-bottom: 1px solid var(--border); }
.form-section h2 { font-size: 1.1rem; margin: 0 0 15px 0; }
.sale-item { background: var(--light-bg); padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid var(--border); }
.item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.item-number { font-weight: 700; color: var(--primary); }
.btn-remove-item { background: var(--danger); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; }
.form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
.form-group input[readonly] { background: #e9ecef; }
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; }
.stock-info { color: var(--grey); font-size: 0.8rem; margin-top: 4px; display: block; }
.btn-add-item { width: 100%; padding: 12px; background: var(--secondary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
.payment-section { background: var(--light-bg); }
.sale-totals { background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid var(--primary); }
.total-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 1.2rem; }
.payment-summary { background: white; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid var(--border); }
.payment-row { display: flex; justify-content: space-between; padding: 6px 0; }
#balanceRow { font-weight: 700; font-size: 1.1rem; color: var(--danger); }
.btn-record-sale { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; margin-top: 20px; }
.btn-record-sale:disabled { background: var(--grey); cursor: not-allowed; opacity: 0.6; }
</style>

<script>
const inventoryData = <?php echo json_encode($available_inventory); ?>;
let itemCount = 0;

function createProductOption(product) {
    return `<option value="${product.product_id}" data-price="${product.selling_price}" data-stock="${product.quantity}" data-name="${product.name.replace(/"/g, '&quot;')}">${product.name} (Stock: ${product.quantity}) - Ksh ${parseFloat(product.selling_price).toFixed(2)}</option>`;
}

const productOptionsHtml = '<option value="">-- Choose Product --</option>' + inventoryData.map(createProductOption).join('');

function addItem() {
    const container = document.getElementById('saleItemsContainer');
    const index = itemCount++;
    const newItemHtml = `
        <div class="sale-item" data-item-index="${index}">
            <div class="item-header">
                <span class="item-number">Item #${index + 1}</span>
                <button type="button" class="btn-remove-item" onclick="removeItem(${index})"><i class="fas fa-times"></i></button>
            </div>
            <div class="form-group">
                <label>Select Product *</label>
                <select name="product_id[]" class="product-select" required onchange="calculateGrandTotal()">${productOptionsHtml}</select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Quantity *</label>
                    <input type="number" name="quantity[]" class="quantity-input" step="0.01" min="0.01" required oninput="calculateGrandTotal()">
                    <small class="stock-info"></small>
                </div>
                <div class="form-group">
                    <label>Unit Price</label>
                    <input type="text" class="unit-price" readonly>
                </div>
                <div class="form-group">
                    <label>Item Total</label>
                    <input type="text" class="item-total" readonly>
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', newItemHtml);
}

function removeItem(index) {
    document.querySelector(`[data-item-index="${index}"]`).remove();
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotalValue = 0;
    let hasValidItems = false;
    
    document.querySelectorAll('.sale-item').forEach(item => {
        const select = item.querySelector('.product-select');
        const option = select.options[select.selectedIndex];
        const quantityInput = item.querySelector('.quantity-input');
        const quantity = parseFloat(quantityInput.value) || 0;
        
        if (!option || !option.value) {
            item.querySelector('.unit-price').value = '';
            item.querySelector('.item-total').value = '';
            item.querySelector('.stock-info').textContent = '';
            return;
        }

        hasValidItems = true;
        const price = parseFloat(option.dataset.price) || 0;
        const stock = parseFloat(option.dataset.stock) || 0;
        const total = price * quantity;
        grandTotalValue += total;

        item.querySelector('.unit-price').value = `Ksh ${price.toFixed(2)}`;
        item.querySelector('.item-total').value = `Ksh ${total.toFixed(2)}`;
        
        const stockInfo = item.querySelector('.stock-info');
        stockInfo.textContent = `Available: ${stock}`;
        stockInfo.style.color = (quantity > stock) ? 'var(--danger)' : 'var(--grey)';
    });
    
    document.getElementById('grandTotal').textContent = `Ksh ${grandTotalValue.toFixed(2)}`;
    
    const cash = parseFloat(document.getElementById('cashPayment').value) || 0;
    const mobile = parseFloat(document.getElementById('mobilePayment').value) || 0;
    const totalPaid = cash + mobile;
    const balance = totalPaid - grandTotalValue;
    
    document.getElementById('totalPaid').textContent = `Ksh ${totalPaid.toFixed(2)}`;
    document.getElementById('balance').textContent = `Ksh ${balance.toFixed(2)}`;
    
    const balanceRow = document.getElementById('balanceRow');
    balanceRow.querySelector('span:first-child').textContent = balance < 0 ? 'Balance Due:' : 'Change:';
    balanceRow.style.color = (Math.abs(balance) > 0.01 && balance < 0) ? 'var(--danger)' : 'var(--primary)';
    
    document.getElementById('submitBtn').disabled = !hasValidItems || grandTotalValue <= 0 || totalPaid < grandTotalValue;
}

document.addEventListener('DOMContentLoaded', function() {
    addItem(); // Start with one item
    
    setTimeout(() => {
        const feedback = document.getElementById('feedback-message');
        if (feedback) {
            feedback.style.transition = 'opacity 0.5s';
            feedback.style.opacity = '0';
            setTimeout(() => feedback.remove(), 500);
        }
    }, 7000);

    document.getElementById('saleForm').addEventListener('submit', function(e) {
        let stockErrors = [];
        document.querySelectorAll('.sale-item').forEach(item => {
            const select = item.querySelector('.product-select');
            const option = select.options[select.selectedIndex];
            if(option && option.value) {
                const stock = parseFloat(option.dataset.stock) || 0;
                const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
                if (quantity > stock) {
                    stockErrors.push(`'${option.dataset.name}': Quantity (${quantity}) exceeds stock (${stock}).`);
                }
            }
        });

        if (stockErrors.length > 0) {
            e.preventDefault();
            alert('Cannot process sale due to stock errors:\n\n' + stockErrors.join('\n'));
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    });
});
</script>


