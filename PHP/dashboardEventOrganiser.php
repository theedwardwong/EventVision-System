<?php
session_start();
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['organiser_id'])) {
    header('Location: login.php');
    exit();
}

$organiser_id = $_SESSION['organiser_id'];
$organiser_name = $_SESSION['organiser_name'];

// Fetch total revenue and tickets sold
$stmt = $pdo->prepare("SELECT SUM(revenue) AS total_revenue, SUM(tickets_sold) AS total_tickets FROM events WHERE organiser_id = ?");
$stmt->execute([$organiser_id]);
$stats = $stmt->fetch();

// Fetch upcoming events
$stmt = $pdo->prepare("SELECT * FROM events WHERE organiser_id = ? ORDER BY event_date ASC LIMIT 5");
$stmt->execute([$organiser_id]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - HELP EventVision System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header .nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .header .profile {
            font-weight: 500;
        }

        .container { width: 80%; margin: 20px auto; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .quick-actions { display: flex; gap: 10px; margin-bottom: 20px; }
        .quick-actions button { padding: 15px; border: none; border-radius: 5px; cursor: pointer; flex: 1; color: #fff; }
        .create { background: #6200ea; }
        .ticket { background: #7b1fa2; }
        .view { background: #512da8; }
        .events { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .event-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        
    </style>
</head>
<body>

<div class="header">
    <div class="logo"><strong>HELP EventVision System</strong></div>
    <div class="nav">
        <a href="#">Dashboard</a>
        <a href="#">Events</a>
        <a href="#">Tickets</a>
        <a href="#">Reports</a>
    </div>
    <div class="profile">
        <?php echo htmlspecialchars($organiser_name); ?> | <a href="login.php">Log Out</a>
    </div>
</div>

<div class="container">
    <h2>Dashboard</h2>
    <p>Welcome back, Event Organiser, <?php echo htmlspecialchars($organiser_name); ?>!</p>

    <div class="stats">
        <div class="card">
            <h3>Total Revenue</h3>
            <p><strong>RM <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></strong></p>
            <small>Up 12% vs last period</small>
        </div>
        <div class="card">
            <h3>Tickets Sold</h3>
            <p><strong><?php echo $stats['total_tickets'] ?? 0; ?></strong></p>
            <small>Up 8% vs last period</small>
        </div>
    </div>

    <div class="quick-actions">
        <button class="create" onclick="window.location.href='create_event.php'">Create Event</button>
        <button class="ticket" onclick="window.location.href='ticket_setup.php'">Ticket Setup</button>
        <button class="view" onclick="window.location.href='attendees.php'">View Attendees</button>
    </div>

    <div class="events">
        <h3>Upcoming Events</h3>
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-item">
                    <div>
                        <strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br>
                        <small><?php echo date('F j, Y', strtotime($event['event_date'])); ?></small>
                    </div>
                    <div>
                        <strong><?php echo $event['tickets_sold']; ?> Tickets Sold</strong><br>
                        <small>RM<?php echo number_format($event['revenue'], 2); ?> Revenue</small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No upcoming events found. <a href="EventCreation.php">Create your first event!</a></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>