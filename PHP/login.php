<?php
session_start();
require 'config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM event_organisers WHERE email = ? AND status = 'Active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                // Store session variables
                $_SESSION['organiser_id'] = $user['organiser_id'];
                $_SESSION['organiser_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'Admin') {
                    header('Location: dashboardAdmin.php');
                } else if ($user['role'] == 'Organiser') {
                    header('Location: dashboardEventOrganiser.php');
                } else {
                    $error = "Invalid user role.";
                }
                exit();

            } else {
                $error = "Invalid email or password.";
            }

        } catch (PDOException $e) {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Login - HELP EventVision System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f0f2f5;
        }

        /* === Navbar Styles === */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        .navbar .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .navbar .nav-links a:hover {
            color: #6200ea;
        }

        .navbar .login-btn {
            background-color: #6200ea;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .navbar .login-btn:hover {
            background-color: #3700b3;
        }

        /* === Login Container Styles === */
        .container {
            width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        input[type=email], input[type=password] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #6200ea;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3700b3;
        }

        .links {
            text-align: center;
            margin-top: 10px;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>


<div class="navbar">
    <div class="logo">HELP EventVision System</div>
    <div class="nav-links">
        <a href="#">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="login.php" class="login-btn">Login</a>
    </div>
</div>

<div class="container">
    <h2>HELP EventVision System</h2>
    <p>Manage your events seamlessly</p>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label><input type="checkbox" name="remember"> Remember me</label>

        <button type="submit">Login</button>
    </form>

    <div class="links">
        <a href="#">Forgot Password?</a><br>
        <small>Not an organizer? Contact admin to register</small>
    </div>
</div>

</body>
</html>