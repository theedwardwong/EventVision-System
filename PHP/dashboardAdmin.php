<?php
require 'config.php'; // Database connection

// Fetch total revenue from bookings table
$stmt = $pdo->query("SELECT SUM(total_amount) AS total_revenue FROM bookings");
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

// Fetch total events this month and today
$stmt = $pdo->query("
    SELECT COUNT(*) AS total_events 
    FROM events 
    WHERE MONTH(event_date) = MONTH(CURRENT_DATE()) 
      AND YEAR(event_date) = YEAR(CURRENT_DATE())
      AND DAY(event_date) = DAY(CURRENT_DATE())
");
$totalEvents = $stmt->fetch()['total_events'] ?? 0;

// Fetch total tickets sold from ticket_details (assumed format: JSON or comma-separated)
$stmt = $pdo->query("SELECT ticket_details FROM bookings");
$tickets = 0;

while ($row = $stmt->fetch()) {
    $details = $row['ticket_details'];

    // Example 1: if JSON like {"VIP":2,"Regular":3}
    $decoded = json_decode($details, true);
    if (is_array($decoded)) {
        $tickets += array_sum($decoded);
    } else {
        // Example 2: fallback if comma-separated numbers like "2,3"
        $numbers = array_map('intval', explode(',', $details));
        $tickets += array_sum($numbers);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - HELP EventVision System</title>
    <link rel="stylesheet" href="css/admindash.css">
</head>
<body>

<!-- === Navbar/Header === -->
<header>
    <div class="logo">HELP EventVision System</div>
    <nav class="nav">
        <a href="dashboardAdmin.php">Dashboard</a>
        <a href="RegistrationEventOrganiser.php">Register Event Organiser</a>
        <a href="generateReport.php">Analytics Reports</a> <!-- Updated link -->
    </nav>
    <div class="profile">
        Admin User | <a href="login.php">Log Out</a>
    </div>
</header>

<!-- === Main Content === -->
<div class="container">
    <h2>Dashboard</h2>
    <div class="welcome">Welcome back, Admin User!</div>

    <!-- Dashboard Stats Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Revenue</h3>
            <p>RM <?= number_format($totalRevenue, 2) ?></p>
        </div>
        <div class="card">
            <h3>Events This Month</h3>
            <p><?= $totalEvents ?></p>
        </div>
        <div class="card">
            <h3>Total Tickets Sold</h3>
            <p><?= $tickets ?></p>
        </div>
    </div>

    <!-- Quick Actions Buttons -->
    <div class="quick-actions">
        <button class="btn-create" onclick="window.location.href='RegistrationEventOrganiser.php'">Create Event Organiser</button>
        <button class="btn-analytics" onclick="window.location.href='generateReport.php'">View Analytics Reports</button>
    </div>
</div>

</body>
</html>
