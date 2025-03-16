<?php
session_start();
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];
$organiser_name = $_SESSION['organiser_name'] ?? 'Organiser';
$event_id = $_GET['event_id'] ?? 0;

$conn = new mysqli("localhost", "root", "", "evsdatabase");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Confirm event ownership
$sql = "SELECT * FROM events WHERE id = ? AND organiser_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $organiser_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ You do not have permission to edit this event.");
}
$event = $result->fetch_assoc();
$stmt->close();

// Fetch existing ticket types
$tickets = [];
$sql = "SELECT * FROM ticket_types WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();

// Fetch existing promo codes
$promos = [];
$sql = "SELECT * FROM promotional_codes WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $promos[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === TICKET CATEGORIES ===
    if (!empty($_POST['ticket_type_name'])) {
        // Delete old ticket types
        $stmt = $conn->prepare("DELETE FROM ticket_types WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();

        // Insert updated ticket types
        foreach ($_POST['ticket_type_name'] as $index => $type_name) {
            $price = $_POST['ticket_price'][$index];
            $max_quantity = $_POST['ticket_quantity'][$index];
            $stmt = $conn->prepare("INSERT INTO ticket_types (event_id, type_name, price, max_quantity, tickets_sold) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("isdi", $event_id, $type_name, $price, $max_quantity);
            $stmt->execute();
        }

        // After saving ticket types, update event_price in events table
        $lowest_price = null;

        $price_sql = "SELECT MIN(price) AS lowest_price FROM ticket_types WHERE event_id = ?";
        $stmt = $conn->prepare($price_sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $lowest_price = $row['lowest_price'];
        }

        $stmt->close();

        // Update events.event_price
        if ($lowest_price !== null) {
            $update_event_sql = "UPDATE events SET event_price = ? WHERE id = ?";
            $stmt = $conn->prepare($update_event_sql);
            $stmt->bind_param("di", $lowest_price, $event_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // === PROMOTIONAL CODES ===
    if (!empty($_POST['promo_code'])) {
        $stmt = $conn->prepare("DELETE FROM promotional_codes WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();

        foreach ($_POST['promo_code'] as $index => $code) {
            $discount = $_POST['promo_discount'][$index];
            $expiry = $_POST['promo_expiry'][$index];
            $stmt = $conn->prepare("INSERT INTO promotional_codes (event_id, code, discount_percentage, expiry_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $event_id, $code, $discount, $expiry);
            $stmt->execute();
        }
    }

    if (isset($_POST['delete_event'])) {

        // Start a transaction to ensure data integrity
        $conn->begin_transaction();
    
        try {
            // Delete promotional codes
            $stmt = $conn->prepare("DELETE FROM promotional_codes WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
    
            // Delete ticket types
            $stmt = $conn->prepare("DELETE FROM ticket_types WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
    
            // Delete the event itself
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
    
            // You can also delete attendees/orders here if necessary
    
            // Commit transaction
            $conn->commit();
    
            // Redirect after deletion
            header("Location: eventsPageForEventOrganiser.php?message=EventDeleted");
            exit();
    
        } catch (Exception $e) {
            $conn->rollback();
            echo "❌ Failed to delete the event: " . $e->getMessage();
        }
    }

    // Redirect after save
    header("Location: eventsPageForEventOrganiser.php");
    exit();
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Setup - HELP EventVision System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #f0f2f5; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header .logo {
            font-weight: bold;
        }

        .nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .profile {
            font-weight: 500;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .ticket-group, .promo-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .add-btn {
            color: #007bff;
            background: none;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-btn:hover {
            text-decoration: underline;
        }

        .seating-layout img {
            max-width: 100%;
            border-radius: 10px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-save {
            background-color: #28a745;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete {
            background:rgb(255, 124, 1);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }


    </style>
    <script>
        function addCategory() {
            const container = document.getElementById('ticket-categories');
            const div = document.createElement('div');
            div.classList.add('ticket-group');
            div.innerHTML = `
                <input type="text" name="ticket_type_name[]" placeholder="Category Name" required>
                <input type="number" name="ticket_price[]" placeholder="Price" required>
                <input type="number" name="ticket_quantity[]" placeholder="Quantity" required>
                <select name="ticket_limit[]">
                    <option>No Limit</option>
                    <option>4 per order</option>
                </select>
                <button type="button" onclick="this.parentElement.remove()">❌</button>
            `;
            container.appendChild(div);
        }

        function addPromo() {
            const container = document.getElementById('promo-codes');
            const div = document.createElement('div');
            div.classList.add('promo-group');
            div.innerHTML = `
                <input type="text" name="promo_code[]" placeholder="Promo Code" required>
                <input type="number" name="promo_discount[]" placeholder="Discount %" required>
                <input type="date" name="promo_expiry[]" required>
                <select name="promo_category[]">
                    <option>All Categories</option>
                </select>
                <button type="button" onclick="this.parentElement.remove()">❌</button>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>

<div class="header">
    <div class="logo">HELP EventVision System</div>
    <div class="nav">
        <a href="dashboardEventOrganiser.php">Dashboard</a>
        <a href="eventsPageForEventOrganiser.php">Ticket Setup</a>
        <a href="#">Analytics Reports</a>
    </div>
    <div class="profile">
        <?php echo htmlspecialchars($organiser_name); ?> | <a href="logout.php" style="color: #6a1b9a; text-decoration: none;">Log Out</a>
    </div>
</div>

<div class="container">
    <h2>Ticket Setup for <?php echo htmlspecialchars($event['event_name']); ?></h2>
    <form method="post">
        <div class="form-section">
            <h3>Ticket Categories</h3>
            <div id="ticket-categories">
                <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-group">
                    <input type="text" name="ticket_type_name[]" value="<?php echo htmlspecialchars($ticket['type_name']); ?>" required>
                    <input type="number" name="ticket_price[]" value="<?php echo htmlspecialchars($ticket['price']); ?>" required>
                    <input type="number" name="ticket_quantity[]" value="<?php echo htmlspecialchars($ticket['max_quantity']); ?>" required>
                    <select name="ticket_limit[]">
                        <option>No Limit</option>
                        <option <?php echo $ticket['max_quantity'] == 4 ? 'selected' : ''; ?>>4 per order</option>
                    </select>
                    <button type="button" onclick="this.parentElement.remove()">❌</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-btn" onclick="addCategory()">+ Add Another Category</button>
        </div>

        <div class="form-section">
            <h3>Seating Layout</h3>
            <div class="seating-layout">
                <img src="https://s3.amazonaws.com/cdn.freshdesk.com/data/helpdesk/attachments/production/9167360778/original/p-IDpn2ET2_OXLSrzt24AIn8he70s0VoNQ.png?1661972018" alt="Seating Layout">
            </div>
        </div>

        <div class="form-section">
            <h3>Promotional Codes</h3>
            <div id="promo-codes">
                <?php foreach ($promos as $promo): ?>
                <div class="promo-group">
                    <input type="text" name="promo_code[]" value="<?php echo htmlspecialchars($promo['code']); ?>" required>
                    <input type="number" name="promo_discount[]" value="<?php echo htmlspecialchars($promo['discount_percentage']); ?>" required>
                    <input type="date" name="promo_expiry[]" value="<?php echo htmlspecialchars($promo['expiry_date']); ?>" required>
                    <select name="promo_category[]">
                        <option>All Categories</option>
                    </select>
                    <button type="button" onclick="this.parentElement.remove()">❌</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-btn" onclick="addPromo()">+ Add Promotional Code</button>
        </div>

        <div class="buttons">
            <button type="button" class="btn-cancel" onclick="window.location.href='eventsPageForEventOrganiser.php'">Cancel</button>
            <button type="submit" name="delete_event" class="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete Event</button>
            <button type="submit" class="btn-save">Save Changes</button>
        </div>
    </form>
</div>

</body>
</html>