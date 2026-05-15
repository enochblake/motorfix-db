<?php
// reports/expenses_view.php
if (!isset($report_data)) die("No report data available");
?>

<div class="report-section">
    <h3>Expense Summary</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <span class="label">Total Expenses</span>
            <span class="value expense">Ksh <?php echo number_format($report_data['summary']['Total Expenses'] ?? 0, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Total Entries</span>
            <span class="value"><?php echo $report_data['summary']['Entries'] ?? 0; ?></span>
        </div>
    </div>
</div>

<div class="report-grid">
    <div class="report-section">
        <h3>Daily Expenses</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>Date</th><th>Total Amount</th></tr>
                </thead>
                <tbody>
                    <?php foreach($report_data['daily_breakdown'] as $day => $amount): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($day)); ?></td>
                        <td class="expense">Ksh <?php echo number_format($amount, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-section">
        <h3>Detailed Expense List</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>Date</th><th>Description</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    <?php foreach($report_data['transactions'] as $row): ?>
                    <tr>
                        <td><?php echo date('Y-m-d', strtotime($row['expense_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="expense">Ksh <?php echo number_format($row['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
