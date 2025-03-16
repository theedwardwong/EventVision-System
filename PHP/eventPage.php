<?php
require 'config.php';

// Handle search and category filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Base query
$sql = "SELECT * FROM events WHERE 1=1";
$params = [];

// Filter search by event name
if (!empty($search)) {
    $sql .= " AND event_name LIKE ?";
    $params[] = "%$search%";
}

// Filter by category if category is selected (assuming you have a 'category' column)
if (!empty($category) && $category != 'All Categories') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Example static categories
$categories = ['All Categories', 'Music', 'Theatre', 'Dance', 'Workshop'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Events - HELP EventVision System</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8f8f8; color: #333; }
        header { background: #fff; padding: 20px 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        header .nav a { margin: 0 15px; text-decoration: none; color: #000; font-weight: 500; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        h2 { margin-bottom: 20px; }

        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .filter-bar input[type="text"] {
            width: 300px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .filter-bar select, .filter-bar button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-bar select {
            background-color: #ffc107;
            color: #333;
        }
        .filter-bar button {
            background-color: #4caf50;
            color: #fff;
        }

        .event-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .event-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 300px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .event-info {
            padding: 15px;
        }
        .event-info h3 {
            font-size: 18px;
            margin: 0 0 10px;
        }
        .event-info p {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        .event-info strong {
            font-size: 16px;
            display: block;
            margin-top: 10px;
        }
        .event-info button {
            background: #ff4081;
            color: #fff;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

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
                align-items: center;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a href="dashboardEventOrganiser.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
    </div>
    <nav class="nav">
        <a href="#">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="login.php">
            <button style="background:#6200ea; color:#fff; border:none; padding:8px 16px; border-radius:5px;">Login</button>
        </a>
    </nav>
</header>

<div class="container">
    <h2>Upcoming Events</h2>

    <form method="GET" class="filter-bar">
        <input type="text" name="search" placeholder="Search events" value="<?php echo htmlspecialchars($search); ?>">
        <select name="category">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo ($cat == $category) ? 'selected' : ''; ?>>
                    <?php echo $cat; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="event-grid">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <?php 
                    // Check if event_image exists and file exists in uploads folder
                    $imagePath = !empty($event['event_image']) && file_exists('uploads/' . $event['event_image']) 
                        ? 'uploads/' . htmlspecialchars($event['event_image']) 
                        : 'https://via.placeholder.com/400x200?text=No+Image';
                ?>
                <div class="event-card">
                    <img src="<?php echo $imagePath; ?>" alt="Event Image">
                    <div class="event-info">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p>📅 <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                        <p>📍 <?php echo htmlspecialchars($event['event_location'] ?? 'HELP Subang 2'); ?></p>
                        <strong>RM <?php echo htmlspecialchars($event['event_price']); ?></strong>
                        <button onclick="window.location.href='TicketPurchase.php?id=<?php echo $event['id']; ?>'">Book Now</button>
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