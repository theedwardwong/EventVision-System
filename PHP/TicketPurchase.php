<?php
session_start();

$event_id = $_GET['event_id'] ?? 0;
$conn = new mysqli("localhost", "root", "", "evsdatabase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Get Event Details
$event = null;
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Event not found.");
}

$event = $result->fetch_assoc();
$stmt->close();

// 2. Get Ticket Types
$tickets = [];
$stmt = $conn->prepare("SELECT * FROM ticket_types WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();

// 3. Get Promo Codes
$promoCodes = [];
$stmt = $conn->prepare("SELECT * FROM promotional_codes WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $promoCodes[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo htmlspecialchars($event['event_name']); ?> - Ticket Purchase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #f0f4ff;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* ======= NAVBAR ======= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar .logo {
            font-weight: bold;
            font-size: 18px;
            color: #222;
        }
        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .navbar ul li a:hover {
            color: #6200ea;
        }
        .navbar ul li .login-btn {
            background-color: #6200ea;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
        }
        .navbar ul li .login-btn:hover {
            background-color: #4b00c2;
        }

        /* ======= CONTAINER ======= */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .top-section {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .left-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .right-section {
            flex: 1;
            min-width: 320px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .event-header {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .event-header img {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
        }

        .event-info h1 {
            font-size: 22px;
            font-weight: 600;
            color: #222;
        }

        .event-info p {
            font-size: 14px;
            color: #666;
            margin: 4px 0;
        }

        .tickets {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .ticket-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
        }

        .ticket-option-info span {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .ticket-option-info p {
            font-size: 12px;
            color: #777;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-control button {
            background-color: #4a3aff;
            color: white;
            border: none;
            padding: 6px 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .ticket-price {
            font-weight: bold;
            min-width: 70px;
            text-align: right;
        }

        .booking-summary h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
        }

        .promo-code {
            display: flex;
            margin: 15px 0;
        }

        .promo-code input {
            flex: 1;
            padding: 8px;
            border-radius: 5px 0 0 5px;
            border: 1px solid #ddd;
        }

        .promo-code button {
            padding: 8px 16px;
            border: none;
            background-color: #4a3aff;
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }

        .btn-pay {
            background-color: #4a3aff;
            color: white;
            border: none;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        .btn-pay:hover {
            background-color: #372eea;
        }

        .seating-layout-container {
            margin-top: 40px;
        }

        .seating-layout-container h3 {
            margin-bottom: 20px;
        }

        .seating-image {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .seating-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .seat-selection {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .seat-selection select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>

     <!-- ===== NAVBAR ===== -->
     <div class="navbar">
        <div class="logo">
            <a href="landingPage.php" style="color: red; text-decoration: none;">HELP EventVision System</a>
        </div>
        <ul>
            <li><a href="eventPage.php">Events</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a class="login-btn" href="login.php">Login</a></li>
        </ul>
    </div>

    <div class="container">

        <div class="top-section">

            <!-- LEFT SECTION -->
            <div class="left-section">

                <!-- EVENT DETAILS -->
                <div class="card">
                    <div class="event-header">
                        <img src="uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>">
                        <div class="event-info">
                            <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
                            <p>📅 <?php echo htmlspecialchars($event['event_date']); ?></p>
                            <p>⏰ <?php echo htmlspecialchars($event['event_time'] ?? '7:00 PM'); ?></p>
                            <p>📍 <?php echo htmlspecialchars($event['event_location']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- SELECT TICKETS -->
                <div class="card">
                    <h3>Select Tickets</h3>
                    <div class="tickets" id="tickets-container">
                        <?php foreach ($tickets as $index => $ticket): ?>
                            <div class="ticket-option" data-index="<?php echo $index; ?>" data-price="<?php echo htmlspecialchars($ticket['price']); ?>">
                                <div class="ticket-option-info">
                                    <span><?php echo htmlspecialchars($ticket['type_name']); ?></span>
                                    <p><?php echo htmlspecialchars($ticket['type_name'] === 'VIP Access' ? 'Premium seating, exclusive benefits' : 'Standard entry, standing area'); ?></p>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" onclick="decreaseQuantity(<?php echo $index; ?>)">-</button>
                                    <span id="quantity-<?php echo $index; ?>">0</span>
                                    <button type="button" onclick="increaseQuantity(<?php echo $index; ?>)">+</button>
                                </div>
                                <div class="ticket-price">RM<?php echo htmlspecialchars(number_format($ticket['price'], 2)); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- RIGHT SECTION (BOOKING SUMMARY) -->
            <div class="card booking-summary">
                <h3>Booking Summary</h3>
                <p><strong><?php echo htmlspecialchars($event['event_name']); ?></strong></p>
                <p>📅 <?php echo htmlspecialchars($event['event_date']); ?> | <?php echo htmlspecialchars($event['event_time'] ?? '7:00 PM'); ?></p>
                <hr style="margin: 15px 0;">

                <div class="summary-item">
                    <span>Selected Tickets</span>
                    <span id="selected-total">RM0.00</span>
                </div>

                <!-- Promo message placeholder -->
                <div class="summary-item" id="promoAppliedMsg" style="display: none;">
                    Promo Applied: 10% Off
                </div>

                <div class="promo-code">
                    <input type="text" id="promoCodeInput" placeholder="Promo Code">
                    <button onclick="applyPromo()">Apply</button>
                </div>

                <hr style="margin: 15px 0;">
                <div class="summary-item">
                    <strong>Total</strong>
                    <strong id="grand-total">RM0.00</strong>
                </div>
                <button class="btn-pay" onclick="confirmPayment()">Proceed to Payment</button>
            </div>

        </div>

        <!-- SEATING LAYOUT -->
        <div class="seating-layout-container">
            <div class="card">
                <h3>Select Your Seats</h3>
                <div class="seating-image">
                    <img src="uploads/EMS.png" alt="Seating Layout">
                </div>
                <div class="seat-selection">
                    <select><option>Section A (Front) - VIP Access (A to D Row)</option></select>
                    <select><option>Section B (Middle) - General Admission (E to K Row)</option></select>
                    <select><option>Section C (Rear) - General Admission (AA to EE)</option></select>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ticketPrices = <?php echo json_encode(array_column($tickets, 'price')); ?>;
        const promoCodes = <?php echo json_encode($promoCodes); ?>;

        const quantities = Array(ticketPrices.length).fill(0);

        let promoDiscount = 0;
        let appliedPromo = false; // Track whether a promo has been applied

        function increaseQuantity(index) {
            quantities[index]++;
            updateDisplay(index);
        }

        function decreaseQuantity(index) {
            if (quantities[index] > 0) {
                quantities[index]--;
                updateDisplay(index);
            }
        }

        function updateDisplay(index) {
            document.getElementById(`quantity-${index}`).innerText = quantities[index];
            updateTotals();
        }

        function updateTotals() {
            let subtotal = 0;

            // Calculate subtotal based on selected ticket quantities
            quantities.forEach((qty, idx) => {
                subtotal += qty * parseFloat(ticketPrices[idx]);
            });

            // Apply promo discount (if any)
            let discountedTotal = subtotal;

            if (promoDiscount > 0 && subtotal > 0) {
                discountedTotal = subtotal - (subtotal * (promoDiscount / 100));
            }

            // Update the totals in the UI
            document.getElementById('selected-total').innerText = `RM${subtotal.toFixed(2)}`;
            document.getElementById('grand-total').innerText = `RM${discountedTotal.toFixed(2)}`;
        }

        function applyPromo() {
            const inputCode = document.getElementById('promoCodeInput').value.trim();
            const promo = promoCodes.find(p => p.code === inputCode);
            const promoMsg = document.getElementById('promoAppliedMsg');

            if (!promo) {
                alert("❌ Invalid promo code!");
                promoDiscount = 0;
                promoMsg.style.display = "none";
            } else {
                const expiry = new Date(promo.expiry_date);
                const today = new Date();

                if (expiry < today) {
                    alert("❌ This promo code has expired.");
                    promoDiscount = 0;
                    promoMsg.style.display = "none";
                } else {
                    promoDiscount = parseFloat(promo.discount_percentage);
                    alert(`✅ Promo code applied! ${promoDiscount}% discount`);
                    promoMsg.style.display = "block";
                    promoMsg.innerText = `Promo Applied: ${promoDiscount}% Off`;
                }
            }

            // Recalculate totals when promo code is applied
            updateTotals();
        }

        function confirmPayment() {
            const confirmProceed = confirm("Are you sure you want to proceed to payment?");
            if (confirmProceed) {
                alert("✅ Your payment is confirmed!");
            } else {
                alert("❌ Payment cancelled.");
            }
        }

    </script>
</body>
</html>