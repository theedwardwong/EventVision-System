<?php
require 'config.php';

$reportType = $_GET['report_type'] ?? 'revenue';
$reportPeriod = $_GET['report_period'] ?? 'monthly';

// Set default formats
$dateFormat = "%Y-%m";
$groupBy = "GROUP BY period";

// Adjust formats based on period
if ($reportPeriod === 'daily') {
    $dateFormat = "%Y-%m-%d";
} elseif ($reportPeriod === 'weekly') {
    $groupBy = "GROUP BY YEAR(created_at), WEEK(created_at)";
    $dateFormat = "%Y-%u";
} else {
    $dateFormat = "%Y-%m";
}

$data = [];

if ($reportType === 'revenue') {
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '$dateFormat') AS period, 
               SUM(total_amount) AS value
        FROM bookings
        $groupBy
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($reportType === 'ticket_sales') {
    if ($reportPeriod === 'weekly') {
        $stmt = $pdo->query("
            SELECT 
                MIN(DATE(created_at)) AS start_date,
                MAX(DATE(created_at)) AS end_date,
                WEEK(created_at) AS week_num,
                SUM(ticket_details) AS value
            FROM bookings
            WHERE YEAR(created_at) = YEAR(CURDATE())
            GROUP BY YEAR(created_at), WEEK(created_at)
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $start = date("j M", strtotime($row['start_date']));
            $end = date("j M", strtotime($row['end_date']));
            $data[] = [
                'period' => "$start – $end",
                'value' => $row['value']
            ];
        }
    } else {
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '$dateFormat') AS period,
                   SUM(ticket_details) AS value
            FROM bookings
            $groupBy
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} elseif ($reportType === 'seat_occupancy') {
    $stmt = $pdo->query("
        SELECT e.event_name AS period, 
               ROUND(SUM(ts.ticket_details) / tt.max_quantity * 100, 2) AS value
        FROM bookings ts
        JOIN events e ON ts.event_id = e.id
        JOIN ticket_types tt ON e.id = tt.event_id
        WHERE tt.max_quantity > 0
        GROUP BY ts.event_id
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report - HELP EventVision System</title>
    <link rel="stylesheet" href="css/generate.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

<header>
    <div class="logo">HELP EventVision System</div>
    <nav class="nav">
        <a href="dashboardAdmin.php">Dashboard</a>
        <a href="RegistrationEventOrganiser.php">Register Event Organiser</a>
        <a href="generateReport.php">Analytics Reports</a>
    </nav>
    <div class="profile">Admin User | <a href="login.php">Log Out</a></div>
</header>

<div class="container">
    <h2>Analytics Reports</h2>

    <!-- Report Filters -->
    <form method="GET">
        <label for="report_type">Report Type:</label>
        <select name="report_type" id="report_type">
            <option value="revenue" <?= $reportType == 'revenue' ? 'selected' : '' ?>>Revenue</option>
            <option value="ticket_sales" <?= $reportType == 'ticket_sales' ? 'selected' : '' ?>>Ticket Sales</option>
            <option value="seat_occupancy" <?= $reportType == 'seat_occupancy' ? 'selected' : '' ?>>Seat Occupancy</option>
        </select>

        <label for="report_period">Reporting Period:</label>
        <select name="report_period" id="report_period">
            <option value="daily" <?= $reportPeriod == 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="weekly" <?= $reportPeriod == 'weekly' ? 'selected' : '' ?>>Weekly</option>
            <option value="monthly" <?= $reportPeriod == 'monthly' ? 'selected' : '' ?>>Monthly</option>
        </select>

        <button type="submit">Generate Report</button>
    </form>

    <!-- Data Table -->
    <h3>Report Data</h3>
    <table>
        <tr>
            <th>Period</th>
            <th><?= $reportType === 'seat_occupancy' ? 'Occupancy (%)' : 'Value' ?></th>
        </tr>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['period']) ?></td>
            <td><?= number_format($row['value'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Chart -->
    <canvas id="reportChart" style="margin-top: 40px;"></canvas>
    <button onclick="downloadPDF()">Download PDF</button>
</div>

<script>
    const labels = <?= json_encode(array_column($data, 'period')) ?>;
    const values = <?= json_encode(array_column($data, 'value')) ?>;
    const reportType = "<?= $reportType ?>";

    let chartType = 'line';
    if (reportType === 'ticket_sales') chartType = 'bar';
    if (reportType === 'seat_occupancy') chartType = 'doughnut';

    new Chart(document.getElementById('reportChart'), {
        type: chartType,
        data: {
            labels: labels,
            datasets: [{
                label: reportType.replace('_', ' ').toUpperCase(),
                data: values,
                borderColor: 'blue',
                backgroundColor: chartType === 'doughnut' 
                    ? ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF']
                    : 'rgba(54, 162, 235, 0.6)',
                borderWidth: 1,
                fill: chartType === 'line'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed;
                            return reportType === 'seat_occupancy' ? `${val}%` : `RM ${val}`;
                        }
                    }
                }
            },
            scales: reportType !== 'seat_occupancy' ? {
                y: {
                    beginAtZero: true
                }
            } : {}
        }
    });

    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text("Analytics Report", 20, 10);
        doc.text(`Type: ${reportType}`, 20, 20);
        let y = 30;
        labels.forEach((label, i) => {
            doc.text(`${label}: ${values[i]}`, 20, y);
            y += 10;
        });
        doc.save("analytics_report.pdf");
    }
</script>

</body>
</html>
