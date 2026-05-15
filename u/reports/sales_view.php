<?php
// reports/sales_view.php
if (!isset($report_data)) die("No report data available");
?>
<div class="report-section">
    <h3>Transaction Summary</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <span class="label">Total Revenue</span>
            <span class="value profit">Ksh <?php echo number_format($report_data['summary']['Total Revenue'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Estimated Profit</span>
            <span class="value profit">Ksh <?php echo number_format($report_data['summary']['Estimated Profit'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Total Transactions</span>
            <span class="value"><?php echo $report_data['summary']['Transactions'] ?? 0; ?></span>
        </div>
    </div>
</div>

<div class="report-grid">
    <div class="report-section">
        <h3>Payment Method Breakdown</h3>
        <ul class="styled-list">
            <?php foreach ($report_data['payment_methods'] as $method => $amount): ?>
            <li>
                <span><?php echo htmlspecialchars($method); ?></span>
                <strong>Ksh <?php echo number_format($amount, 2); ?></strong>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="report-section">
        <h3>Top Selling Products (by Quantity)</h3>
        <ul class="styled-list">
            <?php foreach ($report_data['top_products'] as $product => $qty): ?>
            <li>
                <span><?php echo htmlspecialchars($product); ?></span>
                <strong><?php echo $qty; ?> units</strong>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="report-section">
    <h3>Recent Transactions</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Date/Time</th><th>Product</th><th>Qty</th><th>Total</th><th>Est. Profit</th></tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($report_data['transactions'], 0, 20) as $row): ?>
                <tr>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['sale_timestamp'])); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo $row['quantity_sold']; ?></td>
                    <td>Ksh <?php echo number_format($row['quantity_sold'] * $row['selling_price_at_sale'], 2); ?></td>
                    <td class="profit">Ksh <?php echo number_format(($row['selling_price_at_sale'] - $row['purchase_price_at_sale']) * $row['quantity_sold'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
