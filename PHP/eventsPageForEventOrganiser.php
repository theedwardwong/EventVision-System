<?php
session_start();
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];

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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4ff;
        }

        .navbar {
            width: 100%;
            background-color: #4a3aff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            color: white;
        }

        .navbar .logo {
            font-weight: bold;
            font-size: 20px;
        }

        .navbar ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .navbar ul li {
            cursor: pointer;
        }

        .container {
            padding: 40px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .events-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .event-card {
            width: 300px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .event-card h3 {
            margin: 10px 0 5px;
        }

        .event-card p {
            margin: 5px 0;
            color: #555;
        }

        .btn-primary {
            background-color: #6C63FF;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #574b90;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">HELP EventVision System</div>
        <ul>
            <li><a href="dashboard.php" style="color:white; text-decoration:none;">Dashboard</a></li>
            <li><a href="eventsPageForEventOrganiser.php" style="color:white; text-decoration:none;">Events</a></li>
            <li><a href="analytics.php" style="color:white; text-decoration:none;">Reports</a></li>
            <li><a href="logout.php" style="color:white; text-decoration:none;">Logout</a></li>
        </ul>
    </div>

    <div class="container">
        <h2>Your Events</h2>
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <img src="uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Poster">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($event['event_date']); ?></p>
                        <p><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($event['event_location']); ?></p>
                        <p><strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong></p>
                        <a href="ticketsetup.php?event_id=<?php echo $event['id']; ?>" class="btn-primary">Ticket Setup</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events found. <a href="eventCreation.php">Create one now!</a></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>