<?php
// reports/inventory_view.php
if (!isset($report_data)) die("No report data available");
?>
<div class="report-section">
    <h3>Stock Summary</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <span class="label">Total Stock Value (at Cost)</span>
            <span class="value">Ksh <?php echo number_format($report_data['summary']['Total Stock Value (Cost)'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Potential Profit from Stock</span>
            <span class="value profit">Ksh <?php echo number_format($report_data['summary']['Potential Profit'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Total Units in Stock</span>
            <span class="value"><?php echo $report_data['summary']['Total Units'] ?? 0; ?></span>
        </div>
    </div>
</div>

<div class="report-grid">
    <div class="report-section">
        <h3>Low Stock Alerts (<= 5 units)</h3>
        <?php if(empty($report_data['low_stock_alerts'])): ?>
            <p>No items with low stock. Well done!</p>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>Product</th><th>Category</th><th>Qty Left</th></tr>
                </thead>
                <tbody>
                    <?php foreach($report_data['low_stock_alerts'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td class="expense"><?php echo $item['quantity']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="report-section">
        <h3>Stock Value by Category</h3>
        <ul class="styled-list">
            <?php foreach($report_data['category_breakdown'] as $category => $value): ?>
                <li><span><?php echo htmlspecialchars($category); ?></span> <strong>Ksh <?php echo number_format($value, 2); ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


<div class="report-section">
    <h3>Complete Inventory List</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Product</th><th>Category</th><th>Qty</th><th>Cost Price</th><th>Selling Price</th></tr>
            </thead>
            <tbody>
                <?php foreach($report_data['inventory_list'] as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>Ksh <?php echo number_format($item['purchase_price'], 2); ?></td>
                    <td>Ksh <?php echo number_format($item['selling_price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
