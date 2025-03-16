<?php
session_start();
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];
$organiser_name = $_SESSION['organiser_name'] ?? 'Organiser';

$conn = new mysqli("localhost", "root", "", "evsdatabase");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get events for the current organiser
$sql = "SELECT * FROM events WHERE organiser_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Events - HELP EventVision System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4ff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        .nav {
            display: flex;
            align-items: center;
        }

        .nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav a:hover {
            color: #6200ea;
        }

        .profile {
            font-weight: 500;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .events-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .event-card {
            width: calc(25% - 15px);
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-info {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .event-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .event-info p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }

        .event-info strong {
            font-size: 16px;
            margin-top: 10px;
            display: block;
            color: #333;
        }

        .btn-primary {
            background-color: #6200ea;
            color: white;
            text-align: center;
            padding: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            margin-top: auto;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3700b3;
        }

        @media (max-width: 1024px) {
            .event-card {
                width: calc(50% - 10px);
            }
        }

        @media (max-width: 600px) {
            .event-card {
                width: 100%;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav a {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

    <!-- Header/Navbar -->
    <div class="header">
        <div class="logo">HELP EventVision System</div>
        <div class="nav">
            <a href="dashboardEventOrganiser.php">Dashboard</a>
            <a href="eventsPageForEventOrganiser.php">Ticket Setup</a>
            <a href="#">Analytics Reports</a>
        </div>
        <div class="profile">
            <?php echo htmlspecialchars($organiser_name); ?> | <a href="logout.php" style="color: #6200ea;">Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2>Your Events</h2>
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <img src="uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Poster">
                        <div class="event-info">
                            <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                            <p><?php echo htmlspecialchars($event['event_date']); ?></p>
                            <p><?php echo htmlspecialchars($event['event_location']); ?></p>
                            <strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong>
                            <a href="ticketsetup.php?event_id=<?php echo $event['id']; ?>" class="btn-primary">Ticket Setup</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events found. <a href="eventCreation.php" style="color: #6200ea;">Create one now!</a></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>