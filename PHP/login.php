<?php

$hashed = password_hash("admin123", PASSWORD_DEFAULT);
echo $hashed;

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

            if ($user) {
                // ✅ Verify password using password_verify
                if (password_verify($password, $user['password'])) {

                    // Set session variables
                    $_SESSION['organiser_id'] = $user['organiser_id'];
                    $_SESSION['organiser_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect by role
                    if ($user['role'] === 'Admin') {
                        header('Location: dashboardAdmin.php');
                        exit();
                    } elseif ($user['role'] === 'Organiser') {
                        header('Location: dashboardEventOrganiser.php');
                        exit();
                    } else {
                        $error = "Invalid user role. Please contact admin.";
                    }

                } else {
                    // ❌ Incorrect password
                    $error = "Invalid email or password.";
                }

            } else {
                // ❌ No matching user found
                $error = "Invalid email or password.";
            }

        } catch (PDOException $e) {
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - HELP EventVision System</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f0f2f5;
        }

        /* === Navbar Styles === */
        header {
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header .logo {
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }

        header .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header .nav a:hover {
            color: #6200ea;
        }

        header .nav .login-btn {
            background-color: #6200ea;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        header .nav .login-btn:hover {
            background-color: #3700b3;
        }

        /* === Login Container Styles === */
        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
        }

        p.subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        form {
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type=email],
        input[type=password] {
            width: 377px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .checkbox-container {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .checkbox-container input {
            margin-right: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #6200ea;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background-color: #3700b3;
        }

        .links {
            margin-top: 15px;
            font-size: 14px;
        }

        .links a {
            color: #6200ea;
            text-decoration: none;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
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

<div class="container">
    <h2>HELP EventVision System</h2>
    <p class="subtitle">Manage your events seamlessly</p>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <div class="checkbox-container">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>

        <button type="submit">Login</button>
    </form>

    <div class="links">
        <a href="#">Forgot Password?</a><br>
        <small>Not an organiser? Contact admin to register.</small>
    </div>
</div>

</body>
</html>