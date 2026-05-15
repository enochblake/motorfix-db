<?php
/**
 * Motor Fix - Branch Expense Recording System
 * 
 * Features:
 * - Mobile-first design for easy expense recording
 * - Category-based expense tracking
 * - Multiple expense entry support
 * - Daily/Monthly expense summaries
 * - Activity logging and email notifications
 * - Receipt attachment support
 * - Expense history and reports
 * 
 * @version 1.0
 * @date Friday, October 17, 2025
 */

require_once 'ic/aheader.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';

// Predefined expense categories
$expense_categories = [
    'Rent & Utilities' => ['Rent', 'Electricity', 'Water', 'Internet'],
    'Salaries & Wages' => ['Staff Salaries', 'Overtime', 'Bonuses', 'Allowances'],
    'Transport & Fuel' => ['Fuel', 'Vehicle Maintenance', 'Transport Fares', 'Parking'],
    'Marketing & Advertising' => ['Advertising', 'Promotional Materials', 'Social Media Ads'],
    'Office Supplies' => ['Stationery', 'Cleaning Supplies', 'Office Equipment'],
    'Repairs & Maintenance' => ['Building Repairs', 'Equipment Repairs', 'Painting'],
    'Professional Services' => ['Accounting', 'Legal Fees', 'Consulting'],
    'Miscellaneous' => ['Bank Charges', 'Insurance', 'Licenses', 'Other']
];

// Helper function for activity logging
function logActivity($pdo, $user_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $ip = hash('sha256', $_SERVER['REMOTE_ADDR']);
        $stmt->execute([$user_id, $action, $details ? json_encode($details) : null, $ip]);
    } catch(PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

// Helper function for email notification
function sendExpenseNotification($expense_details) {
    if (empty($_ENV['SMTP_HOST'])) {
        error_log("SMTP is not configured. Skipping expense notification email.");
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

        $mail->setFrom('expenses@motorfix.co.ke', 'Motor Fix Expense System');
        $mail->addAddress('info@motorfix.co.ke', 'Motor Fix Management');

        $mail->isHTML(true);
        $mail->Subject = "New Expense Recorded - {$expense_details['branch']}";
        
        $mail->Body = "
        <h2>Expense Recorded Successfully</h2>
        <p><strong>Branch:</strong> {$expense_details['branch']}</p>
        <p><strong>Recorded By:</strong> {$expense_details['user']}</p>
        <p><strong>Date:</strong> {$expense_details['date']}</p>
        <p><strong>Time Recorded:</strong> {$expense_details['timestamp']}</p>
        <hr>
        <h3>Expense Details:</h3>
        <p><strong>Description:</strong> {$expense_details['description']}</p>
        <p><strong>Amount:</strong> Ksh " . number_format($expense_details['amount'], 2) . "</p>
        <hr>
        <p><strong>Today's Total Expenses:</strong> Ksh " . number_format($expense_details['today_total'], 2) . "</p>
        <p><em>This is an automated notification from Motor Fix Expense Management System.</em></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Expense Email Error: " . $mail->ErrorInfo);
    }
}

// Process Expense Submission
if (isset($_POST['record_expense'])) {
    $descriptions = $_POST['description'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');
    
    if (empty($descriptions) || empty($amounts)) {
        $feedback_message = "Please add at least one expense entry.";
        $feedback_type = "error";
    } else {
        $pdo->beginTransaction();
        try {
            $total_expenses = 0;
            $processed_expenses = [];
            
            // Validate and process expenses
            foreach ($descriptions as $index => $description) {
                $description = trim($description);
                $amount = floatval($amounts[$index]);
                
                if (empty($description) || $amount <= 0) {
                    continue;
                }
                
                // Insert expense record
                $stmt = $pdo->prepare("
                    INSERT INTO expense_records (branch_id, user_id, description, amount, expense_date)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$branch_id, $user_id, $description, $amount, $expense_date]);
                
                $total_expenses += $amount;
                $processed_expenses[] = [
                    'description' => $description,
                    'amount' => $amount
                ];
            }
            
            if (empty($processed_expenses)) {
                throw new Exception("No valid expense entries to process.");
            }
            
            $pdo->commit();
            
            // Get today's total expenses
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(amount), 0) as today_total
                FROM expense_records
                WHERE branch_id = ? AND expense_date = CURDATE()
            ");
            $stmt->execute([$branch_id]);
            $today_total = $stmt->fetchColumn();
            
            // Log activity
            logActivity($pdo, $user_id, 'expense_recorded', [
                'count' => count($processed_expenses),
                'total_amount' => $total_expenses,
                'date' => $expense_date
            ]);
            
            // Send email for each expense (or combine them)
            foreach ($processed_expenses as $expense) {
                sendExpenseNotification([
                    'branch' => $_SESSION['branch_name'],
                    'user' => $_SESSION['user_name'],
                    'date' => date('l, F j, Y', strtotime($expense_date)),
                    'timestamp' => date('g:i A'),
                    'description' => $expense['description'],
                    'amount' => $expense['amount'],
                    'today_total' => $today_total
                ]);
            }
            
            $feedback_message = count($processed_expenses) . " expense(s) recorded successfully! Total: Ksh " . number_format($total_expenses, 2);
            $feedback_type = "success";
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $feedback_message = "Error: " . $e->getMessage();
            $feedback_type = "error";
        }
    }
}

// Delete expense
if (isset($_POST['delete_expense'])) {
    $expense_id = intval($_POST['expense_id']);
    try {
        $stmt = $pdo->prepare("SELECT description, amount FROM expense_records WHERE id = ? AND branch_id = ?");
        $stmt->execute([$expense_id, $branch_id]);
        $expense = $stmt->fetch();
        
        if ($expense) {
            $pdo->prepare("DELETE FROM expense_records WHERE id = ?")->execute([$expense_id]);
            logActivity($pdo, $user_id, 'expense_deleted', [
                'expense_id' => $expense_id,
                'description' => $expense['description'],
                'amount' => $expense['amount']
            ]);
            $feedback_message = "Expense deleted successfully.";
            $feedback_type = "success";
        }
    } catch(PDOException $e) {
        $feedback_message = "Error deleting expense: " . $e->getMessage();
        $feedback_type = "error";
    }
}

// Fetch today's summary
$today_stmt = $pdo->prepare("
    SELECT 
        COUNT(id) as total_count,
        COALESCE(SUM(amount), 0) as total_amount
    FROM expense_records
    WHERE branch_id = ? AND expense_date = CURDATE()
");
$today_stmt->execute([$branch_id]);
$today_summary = $today_stmt->fetch();

// Fetch this month's summary
$month_stmt = $pdo->prepare("
    SELECT 
        COUNT(id) as total_count,
        COALESCE(SUM(amount), 0) as total_amount
    FROM expense_records
    WHERE branch_id = ? 
    AND MONTH(expense_date) = MONTH(CURDATE())
    AND YEAR(expense_date) = YEAR(CURDATE())
");
$month_stmt->execute([$branch_id]);
$month_summary = $month_stmt->fetch();

// Fetch recent expenses
$recent_stmt = $pdo->prepare("
    SELECT er.*, u.name as recorded_by
    FROM expense_records er
    JOIN users u ON er.user_id = u.id
    WHERE er.branch_id = ?
    ORDER BY er.expense_date DESC, er.created_at DESC
    LIMIT 20
");
$recent_stmt->execute([$branch_id]);
$recent_expenses = $recent_stmt->fetchAll();
?>

<main class="mf-main-content">
    <div class="mf-page-header">
        <h1><i class="fas fa-receipt"></i> Record Expenses</h1>
        <p>Track and manage expenses for <?php echo htmlspecialchars($_SESSION['branch_name']); ?></p>
    </div>

    <?php if ($feedback_message): ?>
        <div class="mf-alert <?php echo $feedback_type; ?>" id="feedback-message">
            <?php echo $feedback_message; ?>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="mf-summary-cards">
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="card-info">
                <span class="card-label">Today's Expenses</span>
                <span class="card-value">Ksh <?php echo number_format($today_summary['total_amount'], 2); ?></span>
                <span class="card-meta"><?php echo $today_summary['total_count']; ?> entries</span>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="card-info">
                <span class="card-label">This Month</span>
                <span class="card-value">Ksh <?php echo number_format($month_summary['total_amount'], 2); ?></span>
                <span class="card-meta"><?php echo $month_summary['total_count']; ?> entries</span>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calculator"></i></div>
            <div class="card-info">
                <span class="card-label">Daily Average</span>
                <span class="card-value">Ksh <?php echo number_format($month_summary['total_amount'] / max(1, date('j')), 2); ?></span>
                <span class="card-meta">This month</span>
            </div>
        </div>
    </div>

    <!-- Expense Entry Form -->
    <div class="mf-expense-form">
        <form method="POST" id="expenseForm">
            <input type="hidden" name="record_expense" value="1">
            
            <div class="form-section">
                <h2><i class="fas fa-plus-circle"></i> Add Expense Entries</h2>
                
                <div class="form-group">
                    <label for="expense_date">Expense Date *</label>
                    <input type="date" name="expense_date" id="expense_date" 
                           value="<?php echo date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div id="expenseEntriesContainer">
                    <!-- JavaScript will populate this -->
                </div>
                
                <button type="button" class="btn-add-entry" onclick="addExpenseEntry()">
                    <i class="fas fa-plus-circle"></i> Add Another Expense
                </button>
            </div>

            <div class="form-section totals-section">
                <div class="expense-totals">
                    <div class="total-row">
                        <span>Total Expenses:</span>
                        <strong id="totalExpenses">Ksh 0.00</strong>
                    </div>
                </div>
                
                <button type="submit" class="btn-record-expense" id="submitBtn" disabled>
                    <i class="fas fa-save"></i> Record Expenses
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Category Selection -->
    <div class="quick-categories">
        <h3><i class="fas fa-tag"></i> Quick Categories</h3>
        <div class="category-grid">
            <?php foreach ($expense_categories as $main_cat => $sub_cats): ?>
                <div class="category-group">
                    <h4><?php echo htmlspecialchars($main_cat); ?></h4>
                    <?php foreach ($sub_cats as $sub_cat): ?>
                        <button type="button" class="category-btn" 
                                onclick="setExpenseDescription('<?php echo htmlspecialchars($sub_cat); ?>')">
                            <?php echo htmlspecialchars($sub_cat); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="recent-expenses">
        <h2><i class="fas fa-history"></i> Recent Expenses</h2>
        
        <?php if (empty($recent_expenses)): ?>
            <p class="empty-state">No expenses recorded yet.</p>
        <?php else: ?>
            <div class="expense-list">
                <?php foreach ($recent_expenses as $expense): ?>
                    <div class="expense-item">
                        <div class="expense-info">
                            <div class="expense-desc">
                                <strong><?php echo htmlspecialchars($expense['description']); ?></strong>
                                <span class="expense-meta">
                                    <?php echo date('M d, Y', strtotime($expense['expense_date'])); ?> • 
                                    By <?php echo htmlspecialchars($expense['recorded_by']); ?>
                                </span>
                            </div>
                            <div class="expense-amount">
                                Ksh <?php echo number_format($expense['amount'], 2); ?>
                            </div>
                        </div>
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('Delete this expense record? This action cannot be undone.');">
                            <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                            <button type="submit" name="delete_expense" class="btn-delete-expense">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
:root {
    --primary: #28a745;
    --secondary: #007bff;
    --danger: #dc3545;
    --warning: #ffc107;
    --dark: #212529;
    --grey: #6c757d;
    --light-bg: #f8f9fa;
    --border: #dee2e6;
}

* {
    box-sizing: border-box;
}

body {
    background: var(--light-bg);
    margin: 0;
    padding: 0;
}

.mf-main-content {
    padding: 15px;
    max-width: 1000px;
    margin: 0 auto;
    padding-bottom: 80px;
}

.mf-page-header {
    text-align: center;
    margin-bottom: 20px;
}

.mf-page-header h1 {
    font-size: 1.5rem;
    margin: 0 0 5px 0;
    color: var(--dark);
}

.mf-page-header p {
    color: var(--grey);
    margin: 0;
    font-size: 0.9rem;
}

.mf-alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    animation: slideDown 0.3s ease;
}

.mf-alert.success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid var(--primary);
}

.mf-alert.error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid var(--danger);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Summary Cards */
.mf-summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.card-icon {
    font-size: 2rem;
    color: var(--danger);
    opacity: 0.8;
}

.card-info {
    display: flex;
    flex-direction: column;
}

.card-label {
    font-size: 0.75rem;
    color: var(--grey);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--dark);
}

.card-meta {
    font-size: 0.75rem;
    color: var(--grey);
}

/* Expense Form */
.mf-expense-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.form-section {
    padding: 20px;
    border-bottom: 1px solid var(--border);
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h2 {
    font-size: 1.1rem;
    margin: 0 0 15px 0;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.expense-entry {
    background: var(--light-bg);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid var(--border);
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.entry-number {
    font-weight: 700;
    color: var(--danger);
}

.btn-remove-entry {
    background: var(--danger);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.btn-remove-entry:hover {
    background: #c82333;
}

.form-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 10px;
}

.btn-add-entry {
    width: 100%;
    padding: 12px;
    background: var(--secondary);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-add-entry:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

/* Totals Section */
.totals-section {
    background: linear-gradient(to bottom, white 0%, #f8f9fa 100%);
}

.expense-totals {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 2px solid var(--danger);
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    color: var(--danger);
}

.btn-record-expense {
    width: 100%;
    padding: 16px;
    background: var(--danger);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}

.btn-record-expense:hover:not(:disabled) {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.btn-record-expense:disabled {
    background: var(--grey);
    cursor: not-allowed;
    opacity: 0.6;
}

/* Quick Categories */
.quick-categories {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.quick-categories h3 {
    font-size: 1.1rem;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.category-group h4 {
    font-size: 0.85rem;
    color: var(--grey);
    margin: 0 0 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.category-btn {
    display: block;
    width: 100%;
    padding: 8px 12px;
    background: var(--light-bg);
    border: 1px solid var(--border);
    border-radius: 6px;
    text-align: left;
    font-size: 0.9rem;
    cursor: pointer;
    margin-bottom: 6px;
    transition: all 0.2s;
}

.category-btn:hover {
    background: var(--danger);
    color: white;
    border-color: var(--danger);
}

/* Recent Expenses */
.recent-expenses {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

.recent-expenses h2 {
    font-size: 1.1rem;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: var(--grey);
}

.expense-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.expense-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: var(--light-bg);
    border-radius: 8px;
    border-left: 4px solid var(--danger);
    transition: all 0.2s;
}

.expense-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.expense-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.expense-desc strong {
    display: block;
    margin-bottom: 4px;
    color: var(--dark);
}

.expense-meta {
    font-size: 0.85rem;
    color: var(--grey);
}

.expense-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--danger);
    white-space: nowrap;
}

.btn-delete-expense {
    background: transparent;
    color: var(--danger);
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-delete-expense:hover {
    background: var(--danger);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .mf-main-content {
        padding: 10px;
    }
    
    .mf-summary-cards {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .category-grid {
        grid-template-columns: 1fr;
    }
    
    .expense-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>

<script>
let entryCount = 0;

function addExpenseEntry() {
    const container = document.getElementById('expenseEntriesContainer');
    const index = entryCount++;
    
    const entryHtml = `
        <div class="expense-entry" data-entry-index="${index}">
            <div class="entry-header">
                <span class="entry-number">Entry #${index + 1}</span>
                <button type="button" class="btn-remove-entry" onclick="removeEntry(${index})">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Description *</label>
                    <input type="text" name="description[]" class="description-input" 
                           placeholder="e.g., Electricity Bill" required oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Amount (Ksh) *</label>
                    <input type="number" name="amount[]" class="amount-input" 
                           step="0.01" min="0.01" placeholder="0.00" required oninput="calculateTotal()">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', entryHtml);
    calculateTotal();
}

function removeEntry(index) {
    document.querySelector(`[data-entry-index="${index}"]`).remove();
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    let hasValidEntries = false;
    
    document.querySelectorAll('.expense-entry').forEach(entry => {
        const amount = parseFloat(entry.querySelector('.amount-input').value) || 0;
        const description = entry.querySelector('.description-input').value.trim();
        
        if (amount > 0 && description) {
            hasValidEntries = true;
        }
        
        total += amount;
    });
    
    document.getElementById('totalExpenses').textContent = `Ksh ${total.toFixed(2)}`;
    document.getElementById('submitBtn').disabled = !hasValidEntries || total === 0;
}

function setExpenseDescription(category) {
    const entries = document.querySelectorAll('.expense-entry');
    if (entries.length > 0) {
        const lastEntry = entries[entries.length - 1];
        const descInput = lastEntry.querySelector('.description-input');
        if (!descInput.value) {
            descInput.value = category;
            descInput.focus();
        }
    } else {
        addExpenseEntry();
        setTimeout(() => {
            const newEntry = document.querySelector('.expense-entry:last-child');
            if (newEntry) {
                newEntry.querySelector('.description-input').value = category;
                newEntry.querySelector('.amount-input').focus();
            }
        }, 100);
    }
}

// Form submission validation
document.addEventListener('DOMContentLoaded', function() {
    // Start with one entry
    addExpenseEntry();
    
    // Auto-hide feedback messages
    setTimeout(() => {
        const feedback = document.getElementById('feedback-message');
        if (feedback) {
            feedback.style.transition = 'opacity 0.5s';
            feedback.style.opacity = '0';
            setTimeout(() => feedback.remove(), 500);
        }
    }, 7000);
    
    // Form submission handler
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        let hasValidEntry = false;
        let emptyEntries = [];
        
        document.querySelectorAll('.expense-entry').forEach((entry, index) => {
            const description = entry.querySelector('.description-input').value.trim();
            const amount = parseFloat(entry.querySelector('.amount-input').value) || 0;
            
            if (description && amount > 0) {
                hasValidEntry = true;
            } else if (description || amount > 0) {
                emptyEntries.push(index + 1);
            }
        });
        
        if (!hasValidEntry) {
            e.preventDefault();
            alert('Please add at least one complete expense entry with both description and amount.');
            return false;
        }
        
        if (emptyEntries.length > 0) {
            const confirm = window.confirm(`Entry #${emptyEntries.join(', #')} is incomplete. Continue without it?`);
            if (!confirm) {
                e.preventDefault();
                return false;
            }
        }
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        return true;
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const form = document.getElementById('expenseForm');
            if (!document.getElementById('submitBtn').disabled) {
                form.requestSubmit();
            }
        }
        
        // Ctrl/Cmd + Plus to add entry
        if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '=')) {
            e.preventDefault();
            addExpenseEntry();
        }
    });
});
</script>

<?php require_once 'ic/afooter.php'; ?>