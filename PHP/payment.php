<?php
session_start();

// Receive POST data from ticketPurchase.php
$event_id = $_POST['event_id'] ?? 0;
$event_name = $_POST['event_name'] ?? '';
$ticket_summary = $_POST['ticket_summary'] ?? '2x VIP Tickets';
$total_amount = $_POST['total_amount'] ?? 0;
$sst = round($total_amount * 0.05, 2);
$grand_total = $total_amount + $sst;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Payment - HELP EventVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2fd;
            margin: 0;
            padding: 0;
        }

        header {
            background: #fff;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            font-weight: bold;
            font-size: 20px;
            color: red;
        }

        .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .nav .login-btn {
            background: #6200ea;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .payment-wrapper {
            background: white;
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 30px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .payment-method {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: border 0.3s ease;
        }

        .payment-option:hover {
            border-color: #6200ea;
        }

        .payment-option input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .option-label {
            font-weight: 500;
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: 500;
        }

        input[type=text], input[type=number] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .input-row {
            display: flex;
            gap: 10px;
        }

        .pay-btn {
            background: #573bff;
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            font-weight: bold;
            font-size: 16px;
            border-radius: 6px;
            margin-top: 25px;
            cursor: pointer;
        }

        .pay-btn:hover {
            background: #4523cc;
        }

        .note {
            text-align: center;
            font-size: 12px;
            color: gray;
            margin-top: 10px;
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

<div class="payment-wrapper">
    <h2><?= htmlspecialchars($event_name) ?></h2>
    <p>Secure payment powered by Stripe</p>

    <div class="section-title">Order Summary</div>
    <div class="summary-row">
        <span><?= htmlspecialchars($ticket_summary) ?></span>
        <span>RM<?= number_format($total_amount, 2) ?></span>
    </div>
    <div class="summary-row">
        <span>Sales and Service Tax (SST)</span>
        <span>RM<?= number_format($sst, 2) ?></span>
    </div>
    <div class="summary-row" style="font-weight: bold;">
        <span>Total</span>
        <span>RM<?= number_format($grand_total, 2) ?></span>
    </div>

    <div class="section-title">Payment Method</div>
    <form action="paymentConfirmation.php" method="POST">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        <input type="hidden" name="ticket_summary" value="<?= htmlspecialchars($ticket_summary) ?>">
        <input type="hidden" name="total_amount" value="<?= $grand_total ?>">

        <div class="payment-method">
            <label class="payment-option">
                <input type="radio" name="payment_method" value="credit" checked>
                <span class="option-label">Credit Card</span>
            </label>

            <label class="payment-option">
                <input type="radio" name="payment_method" value="paypal">
                <span class="option-label">PayPal</span>
            </label>
        </div>

        <label for="card_number">Card Number</label>
        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required>

        <div class="input-row">
            <div style="flex: 1;">
                <label for="expiry">Expiry Date</label>
                <input type="text" name="expiry" id="expiry" placeholder="MM/YY" required>
            </div>
            <div style="flex: 1;">
                <label for="cvc">CVC</label>
                <input type="text" name="cvc" id="cvc" placeholder="123" required>
            </div>
        </div>

        <button type="submit" class="pay-btn">Pay</button>
    </form>

    <p class="note">Your payment information is secure and encrypted</p>
</div>
</body>
</html>
