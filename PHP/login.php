<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - HELP EventVision System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }
        .container {
            width: 400px;
            margin: 80px auto;
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

<div class="container">
    <h2>HELP EventVision System</h2>
    <p>Manage your events seamlessly</p>

    <?php
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }
    ?>

    <form action="process_login.php" method="POST">
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