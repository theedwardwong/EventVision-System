<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Event Organiser</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
        }
        .container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=text], input[type=email], input[type=tel] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn {
            background-color: #f44336;
            color: white;
            margin-right: 10px;
        }
        .register-btn {
            background-color: #6200ea;
            color: white;
        }
        .info {
            font-size: 0.9em;
            color: #007bff;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register Event Organiser</h2>
    <p>Create a new event organiser account</p>

    <form action="process_register.php" method="POST">
        <label for="fullname">Full Name *</label>
        <input type="text" id="fullname" name="fullname" required>

        <label for="email">Email Address *</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone Number *</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="organization">Organization Name</label>
        <input type="text" id="organization" name="organization">

        <p class="info">A welcome email will be sent to the registered email address with login credentials.</p>

        <button type="reset" class="cancel-btn">Cancel</button>
        <button type="submit" class="register-btn">Register Organizer</button>
    </form>
</div>

</body>
</html>