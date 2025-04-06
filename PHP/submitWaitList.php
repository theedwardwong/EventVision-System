<?php
require 'config.php';

$event_id = $_POST['event_id'];
$email = $_POST['email'];
$phone = $_POST['phone'];

try {
    $stmt = $pdo->prepare("INSERT INTO waitlist (event_id, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$event_id, $email, $phone]);
    header("Location: waitlist.php?event_id=" . $event_id . "&success=1");
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>