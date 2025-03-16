<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - HELP EventVision System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* === Navbar Styles (Consistent with Landing + Login Pages) === */
        header {
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header .logo {
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }

        header .nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        header .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header .nav a:hover {
            color: #6200ea;
        }

        header .profile {
            font-size: 14px;
            color: #333;
        }

        header .profile a {
            color: #6200ea;
            text-decoration: none;
            margin-left: 8px;
        }

        header .profile a:hover {
            text-decoration: underline;
        }

        /* === Container === */
        .container {
            max-width: 960px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .welcome {
            font-size: 16px;
            margin-bottom: 40px;
            color: #555;
        }

        /* === Dashboard Cards === */
        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            flex: 1;
            min-width: 250px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #6200ea;
        }

        /* === Quick Actions === */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .quick-actions button {
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-create {
            background-color: #e91e63;
        }

        .btn-create:hover {
            background-color: #c2185b;
        }

        .btn-analytics {
            background-color: #3f51b5;
        }

        .btn-analytics:hover {
            background-color: #303f9f;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            header .nav {
                flex-direction: column;
                gap: 15px;
                margin-top: 10px;
            }

            .dashboard-cards {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- === Navbar/Header === -->
<header>
    <div class="logo">HELP EventVision System</div>
    <nav class="nav">
        <a href="dashboardAdmin.php">Dashboard</a>
        <a href="RegistrationEventOrganiser.php">Register Event Organiser</a>
        <a href="#">Analytics Reports</a>
    </nav>
    <div class="profile">
        Admin User | <a href="login.php">Log Out</a>
    </div>
</header>

<!-- === Main Content === -->
<div class="container">
    <h2>Dashboard</h2>
    <div class="welcome">Welcome back, Admin User!</div>

    <!-- Dashboard Stats Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Revenue</h3>
            <p>RM 0.00</p>
        </div>
        <div class="card">
            <h3>Events This Month</h3>
            <p>0</p>
        </div>
        <div class="card">
            <h3>Total Tickets Sold</h3>
            <p>0</p>
        </div>
    </div>

    <!-- Quick Actions Buttons -->
    <div class="quick-actions">
        <button class="btn-create" onclick="window.location.href='RegistrationEventOrganiser.php'">Create Event Organiser</button>
        <button class="btn-analytics" onclick="window.location.href='#'">View Analytics Reports</button>
    </div>
</div>

</body>
</html>