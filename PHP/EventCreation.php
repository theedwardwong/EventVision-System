<?php

session_start(); // 🔥 Required for accessing session variables

// Check if the organiser is logged in
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id']; // Dynamically fetched organiser_id

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event</title>
    <style>
        /* Your existing CSS code here */
        /* Add styles for navbar */
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f0f4ff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .header {
            width: 850px;
            text-align: left;
            margin-bottom: 10px;
        }

        .header h2 {
            color: #222;
            font-size: 24px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .container {
            width: 850px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 80px;
        }

        .file-upload {
            padding: 20px;
            border: 2px dashed #ccc;
            text-align: center;
            color: #666;
            border-radius: 5px;
            cursor: pointer;
            background: #f9f9f9;
        }

        .file-upload span {
            color: #007bff;
            cursor: pointer;
        }

        .promo-code {
            display: flex;
            gap: 10px;
        }

        .promo-code input {
            flex: 1;
        }

        .category {
            display: flex;
            gap: 10px;
        }

        .category input {
            flex: 1;
        }

        .add-category {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            text-align: left;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .cancel {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .create {
            background: #4a3aff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">HELP EventVision System</div>
        <ul>
            <li><a href="dashboard.php" style="color:white; text-decoration:none;">Dashboard</a></li>
            <li><a href="events.php" style="color:white; text-decoration:none;">Events</a></li>
            <li><a href="analytics.php" style="color:white; text-decoration:none;">Reports</a></li>
            <li><a href="logout.php" style="color:white; text-decoration:none;">Logout</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Create New Event</h2>
        <p>Fill in the details below to create your event</p>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="<?php echo strpos($message, 'success') !== false ? 'message' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            
            <div class="form-group">
                <div>
                    <label>Event Name</label>
                    <input type="text" name="event_name" required>
                </div>
                <div class="file-upload">
                    <p>Drop your event poster here or <span>browse files</span></p>
                    <input type="file" name="event_poster" accept="image/*" required>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label>Date</label>
                    <input type="date" name="event_date" required>
                </div>
                <div>
                    <label>Time</label>
                    <input type="time" name="event_time" required>
                </div>
            </div>

            <!-- Location Field -->
            <div class="form-group">
                <div>
                    <label>Location</label>
                    <input type="text" name="event_location" required>
                </div>
            </div>

            <label>Event Description</label>
            <textarea name="event_description"></textarea>

            <div class="form-group">
                <div>
                    <label>Promotional Codes</label>
                    <div class="promo-code">
                        <input type="text" name="promo_code" placeholder="Code">
                        <input type="number" name="discount" placeholder="Discount %" min="0" max="100">
                        <input type="date" name="promo_expiry" placeholder="Expiry Date">
                    </div>
                </div>

                <div>
                    <label>Seating Arrangement</label>
                    <select name="seating_arrangement">
                        <option value="Theater Style">Theater Style</option>
                        <option value="Classroom Style">Classroom Style</option>
                        <option value="Banquet Style">Banquet Style</option>
                    </select>
                </div>
            </div>

            <label>Ticket Categories</label>
            <div class="category">
                <input type="text" name="category_name" placeholder="Category Name">
                <input type="number" name="price" placeholder="Price">
                <input type="number" name="quantity" placeholder="Quantity">
            </div>

            <div class="buttons">
                <button type="reset" class="cancel">Cancel</button>
                <button type="submit" class="create">Create Event</button>
            </div>

        </form>
    </div>

</body>
</html>
