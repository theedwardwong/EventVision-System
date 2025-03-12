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
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8f8f8; color: #333; }
        header { background: #fff; padding: 20px 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        header .nav a { margin: 0 15px; text-decoration: none; color: #000; font-weight: 500; }
        .hero { position: relative; background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb'); background-size: cover; background-position: center; height: 400px; color: #fff; display: flex; align-items: center; justify-content: center; flex-direction: column; text-align: center; }
        .hero h1 { font-size: 36px; margin-bottom: 10px; text-shadow: 2px 2px 5px rgba(0,0,0,0.5); }
        .hero p { font-size: 16px; margin-bottom: 20px; }
        .hero button { background: #e91e63; color: #fff; padding: 15px 30px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        h2 { margin-bottom: 20px; }
        .event-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .event-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: calc(33% - 13.33px); overflow: hidden; transition: transform 0.3s; }
        .event-card:hover { transform: translateY(-5px); }
        .event-card img { width: 100%; height: 200px; object-fit: cover; }
        .event-info { padding: 15px; }
        .event-info h3 { font-size: 18px; margin: 0 0 10px; }
        .event-info p { font-size: 14px; margin: 5px 0; color: #666; }
        .event-info strong { font-size: 16px; display: block; margin-top: 10px; }
        .event-info button { background: #e91e63; color: #fff; border: none; padding: 10px; width: 100%; border-radius: 5px; margin-top: 10px; cursor: pointer; }
        footer { background: #fff; padding: 20px; text-align: center; color: #aaa; margin-top: 40px; }
        @media (max-width: 900px) {
            .event-grid { flex-direction: column; }
            .event-card { width: 100%; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo"><strong>HELP EventVision System</strong></div>
    <nav class="nav">
        <a href="#">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="login.php"><button style="background:#6200ea; color:#fff; border:none; padding:8px 16px; border-radius:5px;">Login</button></a>
    </nav>
</header>

<section class="hero">
    <h1>Experience HELP Events Like Never Before!</h1>
    <p>Book tickets. Reserve your seats. Join the experience.</p>
    <button onclick="window.location.href='#featured'">Browse Events</button>
</section>

<div class="container">
    <h2 id="featured">Featured Events</h2>
    <div class="event-grid">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <img src="<?php echo htmlspecialchars($event['event_image'] ?? 'https://via.placeholder.com/400x200'); ?>" alt="Event Image">
                    <div class="event-info">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p>📅 <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                        <p>📍 <?php echo htmlspecialchars($event['event_location'] ?? 'HELP Subang 2'); ?></p>
                        <strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong>
                        <button onclick="window.location.href='book.php?id=<?php echo $event['id']; ?>'">Book Now</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> HELP EventVision System. All rights reserved.
</footer>

</body>
</html>