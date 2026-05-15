<?php
// reports/comprehensive_view.php
if (!isset($report_data)) die("No report data available");
?>
<div class="report-section">
    <h3>Executive Summary</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <span class="label">Total Revenue</span>
            <span class="value profit">Ksh <?php echo number_format($report_data['financials']['Total Revenue'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Total Expenses</span>
            <span class="value expense">Ksh <?php echo number_format($report_data['financials']['Total Expenses'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Net Profit</span>
            <span class="value net-profit <?php echo (($report_data['financials']['Net Profit'] ?? 0) >= 0) ? 'positive' : 'negative'; ?>">
                Ksh <?php echo number_format($report_data['financials']['Net Profit'] ?? 0, 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="report-section">
    <h3>Key Performance Indicators</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <span class="label">Sale Transactions</span>
            <span class="value"><?php echo $report_data['kpis']['Sale Transactions'] ?? 0; ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Avg. Revenue per Sale</span>
            <span class="value">Ksh <?php echo number_format($report_data['kpis']['Avg. Revenue per Sale'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Current Inventory Value</span>
            <span class="value">Ksh <?php echo number_format($report_data['kpis']['Inventory Value'] ?? 0, 2); ?></span>
        </div>
    </div>
</div>

<div class="report-grid">
    <div class="report-section">
        <h3>Sales Analysis</h3>
        <ul class="styled-list">
            <li><span>Top Selling Product:</span> <strong><?php echo key($report_data['sales_summary']['top_products'] ?? ['N/A' => 0]); ?></strong></li>
            <li><span>Payment via Cash:</span> <strong>Ksh <?php echo number_format($report_data['sales_summary']['payment_methods']['Cash'] ?? 0, 2); ?></strong></li>
            <li><span>Payment via Mobile:</span> <strong>Ksh <?php echo number_format($report_data['sales_summary']['payment_methods']['Mobile Money'] ?? 0, 2); ?></strong></li>
        </ul>
    </div>
    <div class="report-section">
        <h3>Inventory Status</h3>
        <ul class="styled-list">
             <li><span>Total Units in Stock:</span> <strong><?php echo $report_data['inventory_summary']['summary']['Total Units'] ?? 0; ?></strong></li>
             <li><span>Products with Low Stock:</span> <strong><?php echo count($report_data['inventory_summary']['low_stock_alerts'] ?? []); ?></strong></li>
             <li><span>Potential Profit from Stock:</span> <strong class="profit">Ksh <?php echo number_format($report_data['inventory_summary']['summary']['Potential Profit'] ?? 0, 2); ?></strong></li>
        </ul>
    </div>
</div>

<div class="report-section">
    <h3>Insights & Recommendations</h3>
    <ul class="styled-list">
        <?php if (($report_data['financials']['Net Profit'] ?? 0) > 0): ?>
            <li><i class="fas fa-check-circle" style="color:var(--primary);"></i> Financial performance is positive for this period.</li>
        <?php else: ?>
             <li><i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i> Expenses have exceeded profits for this period. Review spending.</li>
        <?php endif; ?>
        <?php if (!empty($report_data['inventory_summary']['low_stock_alerts'])): ?>
             <li><i class="fas fa-box-open" style="color:var(--secondary);"></i> Several items are low in stock. Consider reordering soon.</li>
        <?php endif; ?>
    </ul>
</div>
