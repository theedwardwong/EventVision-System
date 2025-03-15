<?php
session_start();
if (!isset($_SESSION['organiser_id'])) {
    header("Location: login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Setup - HELP EventVision System</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;}
        body {background-color: #f0f4ff;}
        .navbar {width: 100%; background-color: #4a3aff; display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; color: white;}
        .navbar .logo {font-weight: bold; font-size: 20px;}
        .navbar ul {display: flex; list-style: none; gap: 20px;}
        .navbar ul li a {color: white; text-decoration: none;}
        .container {max-width: 960px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        h2 {margin-bottom: 20px; color: #333;}
        .section {margin-bottom: 30px;}
        .section h3 {font-size: 18px; color: #222; margin-bottom: 10px;}
        .form-group {display: flex; gap: 10px; margin-bottom: 10px;}
        input, select {flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;}
        .add-btn {background: none; border: none; color: #007bff; cursor: pointer; font-size: 14px;}
        .add-btn:hover {text-decoration: underline;}
        .seating-layout img {width: 100%; max-width: 400px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 15px;}
        .buttons {display: flex; justify-content: space-between;}
        .cancel {background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;}
        .save {background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;}
    </style>
    <script>
        function addCategory() {
            const container = document.getElementById('ticket-category-container');
            const div = document.createElement('div');
            div.classList.add('form-group');
            div.innerHTML = `
                <input type="text" name="ticket_type_name[]" placeholder="Category Name">
                <input type="number" name="ticket_price[]" placeholder="Price">
                <input type="number" name="ticket_quantity[]" placeholder="Quantity">
                <select name="ticket_limit[]">
                    <option>No Limit</option>
                    <option>4 per order</option>
                </select>
                <button type="button" onclick="this.parentElement.remove()">&#128465;</button>
            `;
            container.appendChild(div);
        }

        function addPromo() {
            const container = document.getElementById('promo-code-container');
            const div = document.createElement('div');
            div.classList.add('form-group');
            div.innerHTML = `
                <input type="text" name="promo_code[]" placeholder="Promo Code">
                <input type="number" name="promo_discount[]" placeholder="Discount %">
                <input type="date" name="promo_expiry[]">
                <select name="promo_category[]">
                    <option>All Categories</option>
                </select>
                <button type="button" onclick="this.parentElement.remove()">&#128465;</button>
            `;
            container.appendChild(div);
        }
    </script>
</head>

<body>
    <div class="navbar">
        <div class="logo">HELP EventVision System</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="eventsPageForEventOrganiser.php">Events</a></li>
            <li><a href="analytics.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="container">
        <h2>Ticket Setup for <?php echo htmlspecialchars($event['event_name']); ?></h2>
        <form method="post">
            <div class="section">
                <h3>Ticket Categories</h3>
                <div id="ticket-category-container">
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="form-group">
                            <input type="text" name="ticket_type_name[]" value="<?php echo htmlspecialchars($ticket['type_name']); ?>">
                            <input type="number" name="ticket_price[]" value="<?php echo htmlspecialchars($ticket['price']); ?>">
                            <input type="number" name="ticket_quantity[]" value="<?php echo htmlspecialchars($ticket['max_quantity']); ?>">
                            <select name="ticket_limit[]">
                                <option>No Limit</option>
                                <option <?php echo $ticket['max_quantity'] == 4 ? 'selected' : ''; ?>>4 per order</option>
                            </select>
                            <button type="button" onclick="this.parentElement.remove()">&#128465;</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="add-btn" onclick="addCategory()">+ Add Another Category</button>
            </div>

            <div class="section">
                <h3>Seating Layout</h3>
                <div class="seating-layout">
                    <img src="https://s3.amazonaws.com/cdn.freshdesk.com/data/helpdesk/attachments/production/9167360778/original/p-IDpn2ET2_OXLSrzt24AIn8he70s0VoNQ.png?1661972018" alt="Seating Layout">
                    <select><option>Section A (Front) - VIP Access</option></select>
                    <select><option>Section B (Middle) - General Admission</option></select>
                    <select><option>Section C (Rear) - General Admission</option></select>
                </div>
            </div>

            <div class="section">
                <h3>Promotional Codes</h3>
                <div id="promo-code-container">
                    <?php foreach ($promos as $promo): ?>
                        <div class="form-group">
                            <input type="text" name="promo_code[]" value="<?php echo htmlspecialchars($promo['code']); ?>">
                            <input type="number" name="promo_discount[]" value="<?php echo htmlspecialchars($promo['discount_percentage']); ?>">
                            <input type="date" name="promo_expiry[]" value="<?php echo htmlspecialchars($promo['expiry_date']); ?>">
                            <select name="promo_category[]">
                                <option>All Categories</option>
                            </select>
                            <button type="button" onclick="this.parentElement.remove()">&#128465;</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="add-btn" onclick="addPromo()">+ Add Promotional Code</button>
            </div>

            <div class="buttons">
                <button type="button" class="cancel" onclick="window.location.href='eventsPageForEventOrganiser.php'">Cancel</button>
                <button type="submit" class="save">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
