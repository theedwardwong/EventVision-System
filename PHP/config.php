<?php
    $host = 'localhost';
    $db   = 'evsdatabase'; // Make sure it matches your DB name
    $user = 'root';        // Default for XAMPP
    $pass = '';            // Blank by default for XAMPP
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit();
    }
?>