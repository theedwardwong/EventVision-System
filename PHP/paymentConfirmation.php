<?php
session_start();
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$event_id = $_POST['event_id'] ?? 0;
$total_amount = $_POST['total_amount'] ?? 0;
$ticket_details = $_POST['ticket_details'] ?? '';
$promo_applied = $_POST['promo_applied'] ?? null;
$payment_method = $_POST['payment_method'] ?? 'Credit Card'; // fallback

// Dummy user email (adjust if session/user login available)
$user_email = "guest@example.com";
$organiser_id = 1; // static for now

try {
    // Insert into bookings
    $stmt = $pdo->prepare("INSERT INTO bookings (event_id, organiser_id, user_email, total_amount, ticket_details, promo_applied, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$event_id, $organiser_id, $user_email, $total_amount, $ticket_details, $promo_applied]);

    // Email Confirmation (dummy)
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@example.com';
        $mail->Password = 'password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@example.com', 'HELP EventVision');
        $mail->addAddress($user_email);

        $mail->isHTML(true);
        $mail->Subject = '🎟️ Ticket Payment Confirmation';
        $mail->Body = "
            <h3>Payment Successful!</h3>
            <p>Thank you for booking with us. Your booking was confirmed on <strong>" . date("F d, Y h:i A") . "</strong>.</p>
            <p><strong>Tickets:</strong> $ticket_details</p>
            <p><strong>Total Paid:</strong> RM " . number_format($total_amount, 2) . "</p>
            <p><strong>Promo Used:</strong> " . ($promo_applied ?? 'None') . "</p>
        ";

        // $mail->send(); // Commented for dummy testing

    } catch (Exception $e) {
        // Log email failure silently
    }

} catch (PDOException $e) {
    die("❌ Booking failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6fa;
            text-align: center;
            padding: 40px;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #4caf50;
        }
        p {
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            background-color: #6200ea;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>✅ Payment Successful!</h2>
    <p>Your booking has been confirmed.</p>
    <p>You'll receive a payment receipt shortly. Kindly check out your inbox</p>
    <p><strong>Tickets:</strong> <?= htmlspecialchars($ticket_details) ?></p>
    <p><strong>Total Paid:</strong> RM <?= number_format($total_amount, 2) ?></p>
    <p><strong>Promo Used:</strong> <?= htmlspecialchars($promo_applied ?? 'None') ?></p>
    <a href="eventPage.php" class="btn">Back to Events</a>
</div>
</body>
</html>
