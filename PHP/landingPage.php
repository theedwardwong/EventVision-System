<?php
require 'config.php';

// Fetch featured events (Example: LIMIT 3 for the homepage)
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
$events = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HELP EventVision System - Events</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f8f8f8;
            color: #333;
        }

        /* NAVBAR */
        header {
            background: #fff;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header .logo {
            font-weight: bold;
        }

        header .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        header .nav button {
            background: #6200ea;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* HERO */
        .hero {
            background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb');
            background-size: cover;
            background-position: center;
            height: 400px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .hero h1 {
            font-size: 36px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
        }

        .hero p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .hero button {
            background: #e91e63;
            color: #fff;
            padding: 15px 30px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* MAIN CONTAINER */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* EVENT GRID */
        .event-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .event-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            overflow: hidden;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }

        .event-info {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .event-info h3 {
            font-size: 16px;
            margin: 0;
            color: #333;
        }

        .event-info p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .event-info strong {
            font-size: 14px;
            color: #333;
        }

        .event-info button {
            background: #e91e63;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: auto;
        }

        .event-info button a {
            color: #fff;
            text-decoration: none;
        }

        /* FOOTER */
        footer {
            background: #fff;
            padding: 20px;
            text-align: center;
            color: #aaa;
            margin-top: 40px;
        }

        @media (max-width: 900px) {
            .event-grid {
                flex-direction: column;
            }

            .event-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<header>
    <div class="logo">
        <a href="landingPage.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
    </div>
    <nav class="nav">
        <a href="eventPage.php">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="login.php"><button>Login</button></a>
    </nav>
</header>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Experience HELP Events Like Never Before!</h1>
    <p>Book tickets. Reserve your seats. Join the experience.</p>
    <button onclick="window.location.href='eventPage.php'">Browse Events</button>
</section>

<!-- FEATURED EVENTS -->
<div class="container">
    <h2 id="featured">Featured Events</h2>
    <div class="event-grid">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <?php 
                        $image_path = !empty($event['event_image']) 
                            ? 'uploads/' . htmlspecialchars($event['event_image']) 
                            : 'https://via.placeholder.com/400x200';
                    ?>
                    <img src="<?php echo $image_path; ?>" alt="Event Image">
                    <div class="event-info">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p>📅 <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                        <p>📍 <?php echo htmlspecialchars($event['event_location'] ?? 'HELP Subang 2'); ?></p>
                        <strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong>
                        <button>
                            <a href="ticketPurchase.php?event_id=<?php echo $event['id']; ?>">Book Now</a>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER -->
<footer>
    &copy; <?php echo date('Y'); ?> HELP EventVision System. All rights reserved.
</footer>

</body>
</html>