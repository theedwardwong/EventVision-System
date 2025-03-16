<?php

session_start(); // 🔥 Required for accessing session variables

// Check if the organiser is logged in
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id']; // Dynamically fetched organiser_id
$organiser_name = $_SESSION['organiser_name'] ?? 'Organiser';

// Database connection
    $conn = new mysqli("localhost", "root", "", "evsdatabase");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_description = $_POST['event_description'];
    $seating_arrangement = $_POST['seating_arrangement']; // Optional, we can use this for 'category' field
    $event_location = $_POST['event_location']; // Add this to your form!

    $promo_code = $_POST['promo_code'];
    $discount = $_POST['discount'];
    $promo_expiry = $_POST['promo_expiry']; // Add expiry in form too!

    $category_name = $_POST['category_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if (isset($_FILES['event_poster']) && $_FILES['event_poster']['error'] === UPLOAD_ERR_OK) {
        $poster_name = $_FILES['event_poster']['name'];
        $poster_tmp = $_FILES['event_poster']['tmp_name'];
        $poster_name_clean = preg_replace("/[^A-Za-z0-9.\-]/", '_', $poster_name);
        $poster_folder = "uploads/" . $poster_name_clean;

        // Ensure uploads folder exists (optional)
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($poster_tmp, $poster_folder)) {
            // SUCCESS - Continue inserting into DB
            $stmt = $conn->prepare("INSERT INTO events (organiser_id, event_name, event_date, event_location, event_price, event_image, category, tickets_sold, revenue, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())");

            $event_price = $price; // You can define this differently based on your use case.

            $stmt->bind_param("isssdss", $organiser_id, $event_name, $event_date, $event_location, $event_price, $poster_name_clean, $seating_arrangement);

            if ($stmt->execute()) {
                $event_id = $stmt->insert_id;

                // Promo Code (optional)
                if (!empty($promo_code)) {
                    $stmt_promo = $conn->prepare("INSERT INTO promotional_codes (event_id, code, discount_percentage, expiry_date)
                        VALUES (?, ?, ?, ?)");
                    $stmt_promo->bind_param("isis", $event_id, $promo_code, $discount, $promo_expiry);
                    $stmt_promo->execute();
                }

                // Ticket Type (optional)
                if (!empty($category_name)) {
                    $stmt_ticket = $conn->prepare("INSERT INTO ticket_types (event_id, type_name, price, max_quantity, tickets_sold)
                        VALUES (?, ?, ?, ?, 0)");
                    $stmt_ticket->bind_param("isdi", $event_id, $category_name, $price, $quantity);
                    $stmt_ticket->execute();
                }

                $message = "✅ Event created successfully!";
            } else {
                $message = "❌ Failed to create event. Try again!";
            }

            $stmt->close();
        } else {
            $message = "❌ Failed to upload event poster! Check folder permissions or file size.";
        }
    } else {
        $message = "❌ Please select a valid event poster!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Create New Event - HELP EventVision System</title>

    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color: #fff;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .navbar .logo {
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }

        .navbar ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar ul li a:hover {
            color: #6200ea;
        }

        .navbar .profile {
            font-weight: 500;
        }

        /* Main Container */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        p.description {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
        }

        /* Form Layout */
        form {
            display: grid;
            gap: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-col {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        input, textarea, select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .file-upload {
            padding: 10px;
            border: 1px dashed #aaa;
            background-color: #f9f9f9;
            text-align: center;
            cursor: pointer;
        }

        /* Buttons */
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-cancel {
            background-color: #f44336;
            color: #fff;
        }

        .cancel {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-create {
            background-color: #6200ea;
            color: #fff;
        }

        .btn-cancel:hover {
            background-color: #d32f2f;
        }

        .btn-create:hover {
            background-color: #3700b3;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            color: #155724;
            background-color: #d4edda;
        }

        .error {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            color: #721c24;
            background-color: #f8d7da;
        }

    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <a href="dashboardEventOrganiser.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
        </div>
        <ul>
            <li><a href="dashboardEventOrganiser.php">Dashboard</a></li>
            <li><a href="eventsPageForEventOrganiser.php">Ticket Setup</a></li>
            <li><a href="analytics.php">Analytics Reports</a></li>
        </ul>
        <div class="profile">
            <?php echo htmlspecialchars($organiser_name); ?> | 
            <a href="login.php" style="color: #6200ea; text-decoration: none;">Log Out</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <h2>Create New Event</h2>
        <p class="description">Fill in the details below to create your event.</p>

        <?php if ($message): ?>
            <div class="<?php echo strpos($message, 'successfully') !== false ? 'message' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <div class="form-col">
                    <label for="event_name">Event Name</label>
                    <input type="text" id="event_name" name="event_name" required>
                </div>

                <div class="form-col">
                    <label for="event_poster">Event Poster</label>
                    <input type="file" id="event_poster" name="event_poster" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="event_date">Date</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>

                <div class="form-col">
                    <label for="event_time">Time</label>
                    <input type="time" id="event_time" name="event_time" required>
                </div>

                <div class="form-col">
                    <label for="event_location">Location</label>
                    <input type="text" id="event_location" name="event_location" required>
                </div>
            </div>

            <div class="form-col">
                <label for="event_description">Event Description</label>
                <textarea id="event_description" name="event_description"></textarea>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="promo_code">Promotional Code</label>
                    <input type="text" id="promo_code" name="promo_code">
                </div>

                <div class="form-col">
                    <label for="discount">Discount %</label>
                    <input type="number" id="discount" name="discount" min="0" max="100">
                </div>

                <div class="form-col">
                    <label for="promo_expiry">Promo Expiry</label>
                    <input type="date" id="promo_expiry" name="promo_expiry">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="seating_arrangement">Seating Arrangement</label>
                    <select id="seating_arrangement" name="seating_arrangement">
                        <option value="Theater Style">Theater Style</option>
                        <option value="Classroom Style">Classroom Style</option>
                        <option value="Banquet Style">Banquet Style</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="category_name">Ticket Category</label>
                    <input type="text" id="category_name" name="category_name">
                </div>

                <div class="form-col">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" min="0">
                </div>

                <div class="form-col">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0">
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="cancel" onclick="window.location.href='dashboardEventOrganiser.php'">Cancel</button>
                <button type="submit" class="btn btn-create">Create Event</button>
            </div>
        </form>
    </div>

</body>
</html>