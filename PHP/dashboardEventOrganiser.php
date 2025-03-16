<?php
    session_start();
    require 'config.php';

    $organiser_id = $_SESSION['organiser_id'];
    $organiser_name = $_SESSION['organiser_name'];

    $error = "";
    $success = "";

    // Handle password change request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
        } else {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE event_organisers SET password = ?, first_login = 0 WHERE organiser_id = ?");
            $stmt->execute([$hashed_new_password, $organiser_id]);

            $success = "Password changed successfully!";
            header("Location: dashboardEventOrganiser.php?password_changed=1");
            exit();
        }
    }

    // Fetch the organiser's current `first_login` flag
    $stmt = $pdo->prepare("SELECT first_login FROM event_organisers WHERE organiser_id = ?");
    $stmt->execute([$organiser_id]);
    $user = $stmt->fetch();

    $force_password_change = $user['first_login'] == 1;

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
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 0; color: #333; }
            header { background: #ffffff; display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
            header .logo { font-weight: bold; font-size: 20px; color: #333; }
            header .nav { display: flex; gap: 30px; align-items: center; }
            header .nav a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s ease; }
            header .nav a:hover { color: #6200ea; }
            header .profile { font-size: 14px; color: #333; }
            header .profile a { color: #6200ea; text-decoration: none; margin-left: 8px; }
            header .profile a:hover { text-decoration: underline; }
            .container { max-width: 960px; margin: 40px auto; padding: 20px; }
            h2 { font-size: 28px; margin-bottom: 10px; }
            .welcome { font-size: 16px; margin-bottom: 40px; color: #555; }
            .stats { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
            .card { background: #fff; flex: 1; min-width: 300px; padding: 20px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s ease; }
            .card:hover { transform: translateY(-5px); }
            .card h3 { font-size: 18px; margin-bottom: 10px; color: #333; }
            .card p { font-size: 24px; font-weight: bold; color: #6200ea; }
            .quick-actions { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 40px; }
            .quick-actions button { flex: 1; min-width: 200px; padding: 15px; font-size: 16px; font-weight: 600; color: #fff; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s ease; }
            .create { background-color: #e91e63; }
            .create:hover { background-color: #c2185b; }
            .ticket { background-color: #7b1fa2; }
            .ticket:hover { background-color: #6a1b9a; }
            .view { background-color: #3f51b5; }
            .view:hover { background-color: #303f9f; }
            .events { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
            .events h3 { margin-bottom: 20px; }
            .event-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee; }
            .event-item:last-child { border-bottom: none; }
            .event-item div:first-child { flex: 2; }
            .event-item div:last-child { flex: 1; text-align: right; }
            .event-item strong { color: #333; }
            .event-item small { color: #777; }
            @media (max-width: 768px) {
                header { flex-direction: column; align-items: flex-start; }
                header .nav { flex-direction: column; gap: 15px; margin-top: 10px; }
                .stats { flex-direction: column; }
                .quick-actions { flex-direction: column; }
                .event-item { flex-direction: column; text-align: left; gap: 10px; }
            }
        </style>
    </head>
    <body>

    <header>
        <div class="logo">
            <a href="dashboardEventOrganiser.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
        </div>
        <nav class="nav">
            <a href="dashboardEventOrganiser.php">Dashboard</a>
            <a href="eventsPageForEventOrganiser.php">Ticket Setup</a>
            <a href="#">Analytics Reports</a>
        </nav>
        <div class="profile">
            <?php echo htmlspecialchars($organiser_name); ?> | <a href="login.php">Log Out</a>
        </div>
    </header>

    <div class="container">
        <h2>Dashboard</h2>
        <div class="welcome">Welcome back, Event Organiser, <?php echo htmlspecialchars($organiser_name); ?>!</div>

        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <?php if ($force_password_change): ?>
            <div style="background: #ffe0e0; padding: 20px; margin-bottom: 20px; border-radius: 10px;">
                <h3>Change Your Password</h3>
                <form method="POST">
                    <label>New Password</label><br>
                    <input type="password" name="new_password" required><br><br>
                    <label>Confirm New Password</label><br>
                    <input type="password" name="confirm_password" required><br><br>
                    <button type="submit" name="change_password">Change Password</button>
                </form>
            </div>
        <?php else: ?>
            <div class="stats">
                <div class="card">
                    <h3>Total Revenue</h3>
                    <p>RM <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></p>
                    <small>Up 12% vs last period</small>
                </div>
                <div class="card">
                    <h3>Tickets Sold</h3>
                    <p><?php echo $stats['total_tickets'] ?? 0; ?></p>
                    <small>Up 8% vs last period</small>
                </div>
            </div>

            <div class="quick-actions">
                <button class="create" onclick="window.location.href='EventCreation.php'">Create Event</button>
                <button class="ticket" onclick="window.location.href='eventsPageForEventOrganiser.php'">Ticket Setup</button>
                <button class="view" onclick="window.location.href='#'">View Attendees</button>
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
        <?php endif; ?>

    </div>
    </body>
</html>