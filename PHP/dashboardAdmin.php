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
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 60px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header .nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .header .profile {
            font-weight: 500;
        }

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
            color: #333;
        }

        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            width: 30%;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            text-align: center;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

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

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo"><strong>HELP EventVision System</strong></div>
    <div class="nav">
        <a href="#">Dashboard</a>
        <a href="#">Register Event Organiser</a>
        <a href="#">Analytics Reports</a>
    </div>
    <div class="profile">
        Admin User | <a href="#">Log Out</a>
    </div>
</div>

<div class="container">
    <h2>Dashboard</h2>
    <div class="welcome">Welcome back, Admin User!</div>

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

    <div class="quick-actions">
        <button class="btn-create">Create Event Organiser</button>
        <button class="btn-analytics">View Analytics Reports</button>
    </div>
</div>

</body>
</html>