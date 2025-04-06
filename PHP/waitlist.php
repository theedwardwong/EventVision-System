<?php
session_start();
require 'config.php';

$event_id = $_GET['event_id'] ?? 0;
if (!$event_id) {
    die("No event selected.");
}

// Fetch current event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$currentEvent = $stmt->fetch();
if (!$currentEvent) {
    die("Event not found.");
}

// Fetch all events for 'Similar Events'
$similarStmt = $pdo->query("SELECT * FROM events");
$similarEvents = $similarStmt->fetchAll();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Page</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        header {
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header .logo {
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }

        header .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header .nav a:hover {
            color: #6200ea;
        }

        header .nav .login-btn {
            background-color: #6200ea;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        header .nav .login-btn:hover {
            background-color: #3700b3;
        }

        .event-container {
            display: flex;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 830px;
            margin: 40px auto;
            justify-content: space-between;
            align-items: center;
        }

        .event-details {
            text-align: left;
            width: 50%;
        }

        .event-details img {
            width: 100%;
            height: 185px;
            object-fit: cover;
            object-position: center;
            border-radius: 8px;
        }

        .event-details h2 {
            font-size: 20px;
            margin: 10px 0;
        }

        .event-details p {
            color: #777;
            font-size: 14px;
        }

        .event-waitlist {
            background: #f4f5f7;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            width: 40%;
            padding-right: 50px;
        }

        .event-waitlist h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .event-waitlist input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .event-waitlist button {
            background: #ff9f00;
            color: white;
            border: none;
            padding: 10px;
            width: 105%;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .similar-events {
            display: flex;
            gap: 15px;
            justify-content: left;
            max-width: 870px;
            margin: auto;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }

        .event-card img {
            width: 80%;
            height: 150px;
            border-radius: 8px;
        }

        .event-card button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .event-container {
                flex-direction: column;
                text-align: center;
            }
            .event-details, .event-waitlist {
                width: 100%;
            }
            .similar-events {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
<header>
    <div class="logo">
        <a href="landingPage.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
    </div>
    <nav class="nav">
        <a href="landingPage.php">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="login.php" class="login-btn">Login</a>
    </nav>
</header>

<?php if (isset($_GET['success'])): ?>
    <p style="color: green;">✅ You've been added to the waitlist!</p>
    <p style="color: green;">You'll receive an email confirmation shortly</p>
<?php endif; ?>

<div class="event-container">
<div class="event-details">
    <?php
        $currentImage = !empty($currentEvent['event_image']) && file_exists('uploads/' . $currentEvent['event_image'])
            ? 'uploads/' . htmlspecialchars($currentEvent['event_image'])
            : 'https://via.placeholder.com/400x200?text=No+Image';
    ?>
    <img src="<?= $currentImage ?>" alt="<?= htmlspecialchars($currentEvent['event_name']) ?>">
    <h2><?= htmlspecialchars($currentEvent['event_name']) ?></h2>
    <p>
        📅 <?= htmlspecialchars($currentEvent['event_date']) ?> &nbsp;
        <?php if (!empty($currentEvent['event_time'])): ?>
            ⏰ <?= htmlspecialchars($currentEvent['event_time']) ?> &nbsp;
        <?php endif; ?>
        📍 <?= htmlspecialchars($currentEvent['event_location']) ?>
    </p>
</div>
    <div class="event-waitlist">
        <h3>Event Sold Out</h3>
        <p>Join the waitlist to get notified when tickets become available</p>
        <form method="POST" action="submitWaitlist.php">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <button type="submit">Join Waitlist</button>
        </form>  
        <p class="waitlist-count">10 people on waitlist </p>
    </div>
</div>

<h3 class="similar-title">Similar Events You Might Like</h3>
<div class="similar-events">
    <?php foreach ($similarEvents as $event): ?>
        <?php
            $imagePath = !empty($event['event_image']) && file_exists('uploads/' . $event['event_image']) 
            ? 'uploads/' . htmlspecialchars($event['event_image']) 
            : 'https://via.placeholder.com/400x200?text=No+Image';
        ?>
        <div class="event-card">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
            <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p>📅 <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                        <p>📍 <?php echo htmlspecialchars($event['event_location'] ?? 'HELP Subang 2'); ?></p>
                        <strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong>
                        <button onclick="window.location.href='ticketPurchase.php?event_id=<?php echo $event['id']; ?>'">Book Now</button>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
