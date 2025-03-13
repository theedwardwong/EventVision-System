<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticketing</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f2f5;
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
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
        }

        h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #222;
            font-weight: bold;
        }

        .section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        input, select {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .seating-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .seating-layout img {
            width: 300px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .seating-options {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        .cancel {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .save {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-category {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
        }

        .add-category:hover {
            text-decoration: underline;
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
            Admin User | <a href="#" style="text-decoration: none;">Log Out</a>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h3>Ticket Categories</h3>
            <div class="form-group">
                <input type="text" value="General Admission">
                <input type="number" value="99.00">
                <input type="number" value="1000">
                <select>
                    <option>No Limit</option>
                    <option>4 per order</option>
                </select>
                <button>&#128465;</button>
            </div>
            <div class="form-group">
                <input type="text" value="VIP Access">
                <input type="number" value="199.00">
                <input type="number" value="200">
                <select>
                    <option>4 per order</option>
                    <option>No Limit</option>
                </select>
                <button>&#128465;</button>
            </div>
            <button class="add-category">+ Add Another Category</button>
        </div>

        <div class="section seating-layout">
            <h3>Seating Layout</h3>
            <div class="seating-container">
                <img src="https://s3.amazonaws.com/cdn.freshdesk.com/data/helpdesk/attachments/production/9167360778/original/p-IDpn2ET2_OXLSrzt24AIn8he70s0VoNQ.png?1661972018" 
                     alt="Seating Layout">
                <div class="seating-options">
                    <select>
                        <option>Section A (Front) - VIP Access</option>
                    </select>
                    <select>
                        <option>Section B (Middle) - General Admission</option>
                    </select>
                    <select>
                        <option>Section C (Rear) - General Admission</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Promotional Codes</h3>
            <div class="form-group">
                <input type="text" value="EARLY25">
                <input type="number" value="25">
                <input type="date">
                <select>
                    <option>All Categories</option>
                </select>
                <button>&#128465;</button>
            </div>
            <button class="add-category">+ Add Promotional Code</button>
        </div>

        <div class="buttons">
            <button class="cancel">Cancel</button>
            <button class="save">Save Changes</button>
        </div>
    </div>
</body>
</html>
