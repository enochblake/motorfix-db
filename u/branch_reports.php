<?php
/**
 * Motor Fix - Advanced Branch Reporting Tool
 *
 * @version 2.2
 * @date Friday, October 17, 2025
 *
 * @features
 * - REMOVED PDF attachments from emails for speed and reliability. Emails are now simple HTML.
 * - Emails are now sent in the background for an instant user experience.
 * - FIXED "Failed to load PDF document" error for the Download PDF button.
 * - Comprehensive, Sales, Expense, and Inventory reports.
 */

require_once 'ic/aheader.php'; // Handles security, session, and DB connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Load Libraries ---
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../FPDF-master/fpdf.php';

// --- Page Setup ---
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';

// Display flash message from background email sending
if (isset($_SESSION['flash_message'])) {
    $feedback_message = $_SESSION['flash_message']['text'];
    $feedback_type = $_SESSION['flash_message']['type'];
    unset($_SESSION['flash_message']);
}

// --- Report Filtering ---
$report_type = $_REQUEST['report_type'] ?? 'comprehensive';
$date_range = $_REQUEST['date_range'] ?? 'today';
$start_date = $_REQUEST['start_date'] ?? '';
$end_date = $_REQUEST['end_date'] ?? '';
$action = $_REQUEST['action'] ?? '';

$report_data = null;
$report_title = "Branch Report";
$report_period = "";

// --- Date Calculation ---
list($start_date_sql, $end_date_sql) = calculate_date_range($date_range, $start_date, $end_date);
$report_period = format_report_period($start_date_sql, $end_date_sql, $date_range);

// --- PDF Generation Class ---
class PDF extends FPDF {
    private $reportTitle;
    private $reportPeriod;
    function setReportHeader($title, $period) {
        $this->reportTitle = $title;
        $this->reportPeriod = $period;
    }
    function Header() {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10, 'Motor Fix - ' . $this->reportTitle, 0, 1, 'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,8, $this->reportPeriod, 0, 1, 'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}


// --- Main Controller Logic ---
if (!empty($action)) {
    try {
        // Handle background email action first
        if ($action == 'email_report') {
            // Set a session message and redirect immediately.
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Report is being sent to management in the background.'];
            $query_params = http_build_query(['action' => 'view_online', 'report_type' => $report_type, 'date_range' => $date_range, 'start_date' => $start_date, 'end_date' => $end_date]);
            header("Location: branch_reports.php?" . $query_params);
            
            // Close connection to browser and continue processing
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                ignore_user_abort(true);
                set_time_limit(60); // Allow 1 minute for email to send
                ob_start();
                echo ' '; // Send some content
                header('Connection: close');
                header('Content-Length: ' . ob_get_length());
                ob_end_flush();
                flush();
            }

            // --- Background Task: Generate and Send Email ---
            $report_data_bg = fetch_report_data($pdo, $report_type, $branch_id, $start_date_sql, $end_date_sql);
            $report_title_bg = get_report_title($report_type);
            $html_body = generate_simple_html_for_email($report_title_bg, $report_period, $_SESSION['branch_name'], $report_data_bg);
            send_report_email($html_body, $report_title_bg, $report_period);
            exit(); // End script execution after background task
        }

        // Generate data for view/download actions
        $report_data = fetch_report_data($pdo, $report_type, $branch_id, $start_date_sql, $end_date_sql);
        $report_title = get_report_title($report_type);
        if($report_type == 'inventory') $report_period = "As of " . date('F j, Y');


        if ($action == 'download_pdf') {
            $pdf_content = generate_pdf_report($report_title, $report_period, $_SESSION['branch_name'], $report_type, $report_data);
            $filename = str_replace(' ', '_', $report_title) . '_' . date('Y-m-d') . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdf_content));
            echo $pdf_content;
            exit;
        }

    } catch (Exception $e) {
        $feedback_message = "An error occurred: " . $e->getMessage();
        $feedback_type = "error";
    }
}


// --- HELPER & DATA FUNCTIONS ---

function get_report_title($type){
     switch ($type) {
        case 'sales': return "Sales Report";
        case 'expenses': return "Expense Report";
        case 'inventory': return "Inventory Status Report";
        default: return "Comprehensive Business Report";
    }
}

function fetch_report_data($pdo, $report_type, $branch_id, $start_date_sql, $end_date_sql){
    switch ($report_type) {
        case 'sales': return generate_sales_report($pdo, $branch_id, $start_date_sql, $end_date_sql);
        case 'expenses': return generate_expenses_report($pdo, $branch_id, $start_date_sql, $end_date_sql);
        case 'inventory': return generate_inventory_report($pdo, $branch_id);
        default: return generate_comprehensive_report($pdo, $branch_id, $start_date_sql, $end_date_sql);
    }
}


function calculate_date_range($range, $start, $end) {
    switch ($range) {
        case 'today': return [date('Y-m-d'), date('Y-m-d')];
        case 'last_7_days': return [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')];
        case 'last_30_days': return [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')];
        case 'last_3_months': return [date('Y-m-d', strtotime('-3 months')), date('Y-m-d')];
        case 'last_6_months': return [date('Y-m-d', strtotime('-6 months')), date('Y-m-d')];
        case 'last_12_months': return [date('Y-m-d', strtotime('-12 months')), date('Y-m-d')];
        case 'custom': return [!empty($start) ? $start : date('Y-m-d'), !empty($end) ? $end : date('Y-m-d')];
        default: return [date('Y-m-d'), date('Y-m-d')];
    }
}

function format_report_period($start, $end, $range) {
    if ($range === 'today') return date('F j, Y');
    if ($start === $end) return date('F j, Y', strtotime($start));
    return date('M j, Y', strtotime($start)) . " - " . date('M j, Y', strtotime($end));
}

function generate_sales_report($pdo, $branch_id, $start, $end) {
    $data = [];
    $stmt = $pdo->prepare("SELECT sr.id, sr.sale_timestamp, p.name as product_name, si.quantity_sold, si.selling_price_at_sale, si.purchase_price_at_sale, sr.amount_paid_cash, sr.amount_paid_mobile FROM sales_records sr JOIN sale_items si ON sr.id = si.sale_id JOIN products p ON si.product_id = p.id WHERE sr.branch_id = ? AND DATE(sr.sale_timestamp) BETWEEN ? AND ? ORDER BY sr.sale_timestamp DESC");
    $stmt->execute([$branch_id, $start, $end]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data['transactions'] = $transactions;

    $total_revenue = 0; $total_profit = 0; $cash = 0; $mobile = 0; $top_products = []; $daily_sales = [];
    $sales_payments = [];
    foreach($transactions as $row) { if (!isset($sales_payments[$row['id']])) { $sales_payments[$row['id']] = ['cash' => $row['amount_paid_cash'], 'mobile' => $row['amount_paid_mobile']]; } }
    foreach($sales_payments as $payment) { $cash += $payment['cash']; $mobile += $payment['mobile']; }

    foreach($data['transactions'] as $row) {
        $revenue = $row['quantity_sold'] * $row['selling_price_at_sale'];
        $profit = ($row['selling_price_at_sale'] - $row['purchase_price_at_sale']) * $row['quantity_sold'];
        $total_revenue += $revenue; $total_profit += $profit;
        @$top_products[$row['product_name']] += $row['quantity_sold'];
        $day = date('Y-m-d', strtotime($row['sale_timestamp']));
        @$daily_sales[$day]['revenue'] += $revenue; @$daily_sales[$day]['profit'] += $profit;
    }
    arsort($top_products); krsort($daily_sales);
    
    $data['summary'] = [ 'Total Revenue' => $total_revenue, 'Estimated Profit' => $total_profit, 'Transactions' => count($sales_payments) ];
    $data['payment_methods'] = [ 'Cash' => $cash, 'Mobile Money' => $mobile ];
    $data['top_products'] = array_slice($top_products, 0, 5, true);
    $data['daily_breakdown'] = $daily_sales;
    return $data;
}

function generate_expenses_report($pdo, $branch_id, $start, $end) {
    $data = [];
    $stmt = $pdo->prepare("SELECT expense_date, description, amount FROM expense_records WHERE branch_id = ? AND expense_date BETWEEN ? AND ? ORDER BY expense_date DESC");
    $stmt->execute([$branch_id, $start, $end]);
    $data['transactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_expenses = 0; $daily_expenses = [];
    foreach($data['transactions'] as $row) { $total_expenses += $row['amount']; $day = date('Y-m-d', strtotime($row['expense_date'])); @$daily_expenses[$day] += $row['amount']; }
    krsort($daily_expenses);
    $data['summary'] = ['Total Expenses' => $total_expenses, 'Entries' => count($data['transactions'])];
    $data['daily_breakdown'] = $daily_expenses;
    return $data;
}

function generate_inventory_report($pdo, $branch_id) {
    $data = [];
    $stmt = $pdo->prepare("SELECT p.name, pc.name as category, i.quantity, i.purchase_price, i.selling_price FROM inventory i JOIN products p ON i.product_id = p.id JOIN product_categories pc ON p.category_id = pc.id WHERE i.branch_id = ? AND i.quantity > 0 ORDER BY p.name ASC");
    $stmt->execute([$branch_id]);
    $data['inventory_list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_value = 0; $potential_profit = 0; $total_units = 0; $category_breakdown = []; $low_stock = [];
    foreach($data['inventory_list'] as $row) {
        $stock_value = $row['quantity'] * ($row['purchase_price'] ?? 0);
        $total_value += $stock_value; $potential_profit += (($row['selling_price'] ?? 0) - ($row['purchase_price'] ?? 0)) * $row['quantity'];
        $total_units += $row['quantity']; @$category_breakdown[$row['category']] += $stock_value;
        if ($row['quantity'] <= 5) $low_stock[] = $row;
    }
    arsort($category_breakdown);
    $data['summary'] = ['Total Stock Value (Cost)' => $total_value, 'Potential Profit' => $potential_profit, 'Total Units' => $total_units];
    $data['category_breakdown'] = $category_breakdown;
    $data['low_stock_alerts'] = $low_stock;
    return $data;
}

function generate_comprehensive_report($pdo, $branch_id, $start, $end) {
    $sales = generate_sales_report($pdo, $branch_id, $start, $end);
    $expenses = generate_expenses_report($pdo, $branch_id, $start, $end);
    $inventory = generate_inventory_report($pdo, $branch_id);
    $net_profit = ($sales['summary']['Estimated Profit'] ?? 0) - ($expenses['summary']['Total Expenses'] ?? 0);
    return [
        'financials' => [ 'Total Revenue' => $sales['summary']['Total Revenue'], 'Total Expenses' => $expenses['summary']['Total Expenses'], 'Net Profit' => $net_profit, ],
        'kpis' => [ 'Sale Transactions' => $sales['summary']['Transactions'], 'Avg. Revenue per Sale' => ($sales['summary']['Transactions'] > 0) ? ($sales['summary']['Total Revenue'] / $sales['summary']['Transactions']) : 0, 'Inventory Value' => $inventory['summary']['Total Stock Value (Cost)'], ],
        'sales_summary' => $sales, 'expenses_summary' => $expenses, 'inventory_summary' => $inventory
    ];
}

function generate_pdf_report($title, $period, $branch_name, $type, $data) {
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->setReportHeader($title, $period . ' - ' . $branch_name);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Executive Summary', 0, 1);
    $summary_data = $data['summary'] ?? ($data['financials'] ?? []);
     foreach($summary_data as $key => $val) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 7, $key . ':', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, is_numeric($val) ? 'Ksh ' . number_format($val, 2) : $val, 0, 1);
    }
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(230,230,230);
    if($type == 'sales' || $type == 'comprehensive'){
        $pdf->Cell(0, 10, 'Recent Transactions', 0, 1);
        $pdf->Cell(35, 7, 'Date', 1, 0, 'C', true);
        $pdf->Cell(75, 7, 'Product', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Total', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Profit', 1, 1, 'C', true);
        $pdf->SetFont('Arial','',8);
        foreach(array_slice($data['transactions'] ?? $data['sales_summary']['transactions'], 0, 35) as $row){
            if($pdf->GetY() > 270) { $pdf->AddPage(); } // Auto page break
            $pdf->Cell(35, 6, date('Y-m-d H:i', strtotime($row['sale_timestamp'])), 1);
            $x = $pdf->GetX(); $y = $pdf->GetY();
            $pdf->MultiCell(75, 6, $row['product_name'], 1);
            $pdf->SetXY($x + 75, $y);
            $pdf->Cell(15, 6, $row['quantity_sold'], 1, 0, 'C');
            $pdf->Cell(30, 6, number_format($row['quantity_sold'] * $row['selling_price_at_sale'], 2), 1, 0, 'R');
            $pdf->Cell(30, 6, number_format(($row['selling_price_at_sale'] - $row['purchase_price_at_sale']) * $row['quantity_sold'], 2), 1, 1, 'R');
        }
    }
    return $pdf->Output('S');
}


function generate_simple_html_for_email($title, $period, $branch_name, $data) {
    $summary_data = $data['summary'] ?? ($data['financials'] ?? []);
    $summary_html = '';
    foreach($summary_data as $key => $val) {
        $value_formatted = is_numeric($val) ? 'Ksh ' . number_format($val, 2) : htmlspecialchars($val);
        $summary_html .= "<tr><th style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($key) . ":</th><td style='padding: 8px; text-align: right; border-bottom: 1px solid #ddd;'><strong>" . $value_formatted . "</strong></td></tr>";
    }

    $body = <<<HTML
    <div style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;">
        <h1 style="color: #28a745; text-align: center;">Motor Fix - {$title}</h1>
        <p style="text-align: center; font-size: 1.1em;">{$branch_name}</p>
        <p style="text-align: center; color: #666;">{$period}</p>
        <hr>
        <h2 style="color: #007bff;">Executive Summary</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            {$summary_html}
        </table>
        <p style="text-align: center; color: #999; font-size: 0.9em; margin-top: 20px;">This is an automated report generated by {$_SESSION['user_name']}.</p>
    </div>
HTML;
    return $body;
}

function send_report_email($html_body, $title, $period) {
    if (empty($_ENV['SMTP_HOST'])) { error_log("SMTP not configured."); return false; }
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST']; $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER']; $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'] ?? 587;
        $mail->setFrom('reports@motorfix.co.ke', 'Motor Fix Reports');
        $mail->addAddress('info@motorfix.co.ke', 'Motor Fix Management');
        
        $mail->isHTML(true);
        $mail->Subject = "Branch Report: {$title} for " . $_SESSION['branch_name'];
        $mail->Body = $html_body;
        $mail->send(); return true;
    } catch (Exception $e) { error_log("Report Email Error: " . $mail->ErrorInfo); return false; }
}
?>

<main class="mf-main-content">
    <div class="mf-page-header">
        <h1><i class="fas fa-chart-pie"></i> Branch Reports</h1>
        <p>Generate and export reports for your branch: <?php echo htmlspecialchars($_SESSION['branch_name']); ?></p>
    </div>
    
    <?php if ($feedback_message): ?>
        <div class="mf-alert <?php echo $feedback_type; ?>"><?php echo $feedback_message; ?></div>
    <?php endif; ?>

    <div class="mf-form-panel">
        <form method="POST" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="report_type">Report Type</label>
                    <select name="report_type" id="report_type">
                        <option value="comprehensive" <?php echo ($report_type == 'comprehensive') ? 'selected' : ''; ?>>Comprehensive Report</option>
                        <option value="sales" <?php echo ($report_type == 'sales') ? 'selected' : ''; ?>>Sales Report</option>
                        <option value="expenses" <?php echo ($report_type == 'expenses') ? 'selected' : ''; ?>>Expense Report</option>
                        <option value="inventory" <?php echo ($report_type == 'inventory') ? 'selected' : ''; ?>>Inventory Report</option>
                    </select>
                </div>
                <div class="form-group" id="date-range-group">
                    <label for="date_range">Date Range</label>
                    <select name="date_range" id="date_range">
                        <option value="today" <?php echo ($date_range == 'today') ? 'selected' : ''; ?>>Today</option>
                        <option value="last_7_days" <?php echo ($date_range == 'last_7_days') ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="last_30_days" <?php echo ($date_range == 'last_30_days') ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="last_3_months" <?php echo ($date_range == 'last_3_months') ? 'selected' : ''; ?>>Last 3 Months</option>
                        <option value="last_6_months" <?php echo ($date_range == 'last_6_months') ? 'selected' : ''; ?>>Last 6 Months</option>
                        <option value="last_12_months" <?php echo ($date_range == 'last_12_months') ? 'selected' : ''; ?>>Last 12 Months</option>
                        <option value="custom" <?php echo ($date_range == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                </div>
                <div class="form-group" id="custom-dates" style="display: none;">
                    <label>Custom Dates</label>
                    <div class="date-inputs">
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        <span>to</span>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                </div>
                <div class="form-group-actions">
                    <button type="submit" name="action" value="view_online" class="btn-action"><i class="fas fa-eye"></i> View Online</button>
                    <button type="submit" name="action" value="download_pdf" class="btn-action"><i class="fas fa-file-pdf"></i> Download PDF</button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($action == 'view_online' && $report_data): ?>
    <div class="mf-report-container">
        <div class="report-header">
            <div>
                <h2><?php echo htmlspecialchars($report_title); ?></h2>
                <p><?php echo htmlspecialchars($report_period); ?></p>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="email_report">
                <input type="hidden" name="report_type" value="<?php echo htmlspecialchars($report_type); ?>">
                <input type="hidden" name="date_range" value="<?php echo htmlspecialchars($date_range); ?>">
                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                <button type="submit" class="btn-email"><i class="fas fa-envelope"></i> Email Report</button>
            </form>
        </div>
        
        <?php
            $view_file = __DIR__ . "/reports/{$report_type}_view.php";
            if (file_exists($view_file)) {
                include $view_file;
            } else {
                echo "<div class='mf-alert error'>Report view file not found.</div>";
            }
        ?>
    </div>
    <?php elseif ($action == 'view_online' && is_null($report_data)): ?>
        <div class="mf-report-container empty"><p>No data found for the selected criteria.</p></div>
    <?php endif; ?>

</main>

<style>
:root { --primary: #28a745; --secondary: #007bff; --danger: #dc3545; --dark: #212529; --grey: #6c757d; --light-bg: #f8f9fa; --border: #dee2e6; }
body { background-color: var(--light-bg); }
.mf-main-content { padding: 15px; max-width: 1400px; margin: 0 auto; }
.mf-page-header { text-align: center; margin-bottom: 20px; }
.mf-alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.mf-alert.success { background-color: #d4edda; color: #155724; border-left: 4px solid var(--primary); }
.mf-alert.error { background-color: #f8d7da; color: #721c24; border-left: 4px solid var(--danger); }
.mf-form-panel { background-color: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
.filter-form .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; align-items: flex-end; }
.form-group label { display: block; font-weight: 600; margin-bottom: 8px; }
.form-group select, .form-group input { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); font-size: 1rem; }
.date-inputs { display: flex; gap: 5px; align-items: center; }
.form-group-actions { display: flex; gap: 10px; }
.btn-action { flex: 1; background-color: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-action:last-child { background-color: var(--dark); }
.mf-report-container { background-color: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
.mf-report-container.empty { text-align: center; padding: 50px; }
.report-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 15px; gap: 15px; }
.report-header h2, .report-section h3 { margin: 0 0 5px 0; font-size: 1.5rem; }
.report-header p { margin: 0; color: var(--grey); }
.btn-email { background-color: var(--secondary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
.report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
.report-section { margin-bottom: 30px; }
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
.summary-item { background-color: var(--light-bg); padding: 15px; border-radius: 8px; border: 1px solid var(--border); }
.summary-item .label { display: block; font-size: 0.85rem; color: var(--grey); font-weight: 500;}
.summary-item .value { font-size: 1.4rem; font-weight: 700; color: var(--dark); }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
th { background-color: var(--light-bg); font-weight: 600; }
td.profit, .summary-item .value.profit { color: var(--primary); font-weight: 600; }
td.expense, .summary-item .value.expense { color: var(--danger); font-weight: 600; }
.value.net-profit.positive { color: var(--primary); }
.value.net-profit.negative { color: var(--danger); }
ul.styled-list { list-style: none; padding-left: 0; }
ul.styled-list li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); }
ul.styled-list li:last-child { border-bottom: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date_range');
    const customDatesDiv = document.getElementById('custom-dates');
    const reportTypeSelect = document.getElementById('report_type');
    const dateRangeGroup = document.getElementById('date-range-group');

    function toggleCustomDates() {
        customDatesDiv.style.display = (dateRangeSelect.value === 'custom') ? 'block' : 'none';
    }
    function toggleDateFilters() {
        const isInventory = reportTypeSelect.value === 'inventory';
        dateRangeGroup.style.display = isInventory ? 'none' : 'block';
        customDatesDiv.style.display = (isInventory || dateRangeSelect.value !== 'custom') ? 'none' : 'block';
    }

    dateRangeSelect.addEventListener('change', toggleCustomDates);
    reportTypeSelect.addEventListener('change', toggleDateFilters);
    toggleDateFilters();

    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});
</script>

<?php require_once 'ic/afooter.php'; ?>

