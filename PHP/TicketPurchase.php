<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            flex-direction: column; /* Now everything stacks */
            gap: 20px;
        }
        .top-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .left-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .right-section {
            flex: 1;
            min-width: 300px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .event-header {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .event-header img {
            width: 120px;
            border-radius: 8px;
        }
        .event-info h2 {
            font-size: 20px;
            font-weight: 600;
        }
        .event-info p {
            font-size: 14px;
            color: #666;
        }
        .tickets {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .ticket-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        .ticket-option span {
            font-weight: 600;
        }
        .quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity button {
            padding: 5px 10px;
            border: none;
            background: #6200ea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .booking-summary h3 {
            font-size: 18px;
            font-weight: 600;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .promo {
            display: flex;
            gap: 10px;
        }
        .promo input {
            flex: 1;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .promo button {
            padding: 5px 10px;
            border: none;
            background: #6200ea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-pay {
            padding: 10px;
            width: 100%;
            background: #6200ea;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .seating {
            text-align: center;
        }
        .seating img {
            max-width: 100%;
            border-radius: 10px;
        }
        .seat-selection {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .seat-selection select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- TOP SECTION (Event, Tickets, Summary) -->
    <div class="top-section">
        <!-- LEFT SECTION (Event & Tickets) -->
        <div class="left-section">
            
            <!-- Event Info -->
            <div class="card">
                <div class="event-header">
                    <img src="https://d1csarkz8obe9u.cloudfront.net/posterpreviews/spring-music-festival-poster-design-template-b08b14431a1307dd55b8de20e24f2f16_screen.jpg?ts=1698502392" alt="Spring Music Festival">
                    <div class="event-info">
                        <h1>Spring Music Festival</h1>
                        <p>📅 March 17, 2025</p>
                        <p>⏰ 7:00 PM</p>
                        <p>📍 HELP Subang 2</p>
                    </div>
                </div>
            </div>

            <!-- Select Tickets -->
            <div class="card">
                <h3>Select Tickets</h3>
                <div class="tickets">
                    <div class="ticket-option">
                        <div>
                            <span>General Admission</span>
                            <p>Standard entry, standing area</p>
                        </div>
                        <div class="quantity">
                            <button>-</button>
                            <span>2</span>
                            <button>+</button>
                        </div>
                        <span>RM79</span>
                    </div>
                    <div class="ticket-option">
                        <div>
                            <span>VIP Access</span>
                            <p>Premium seating, exclusive benefits</p>
                        </div>
                        <div class="quantity">
                            <button>-</button>
                            <span>1</span>
                            <button>+</button>
                        </div>
                        <span>RM199</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT SECTION (BOOKING SUMMARY) -->
        <div class="right-section">
            <div class="card booking-summary">
                <h3>Booking Summary</h3>
                <p>Spring Music Festival</p>
                <p>📅 March 17, 2025, 7:00 PM</p>
                <div class="summary-item">
                    <span>General Admission x2</span>
                    <span>RM158.00</span>
                </div>
                <div class="summary-item">
                    <span>VIP Access x1</span>
                    <span>RM199.00</span>
                </div>
                <hr>
                <div class="promo">
                    <input type="text" placeholder="Promo Code">
                    <button>Apply</button>
                </div>
                <hr>
                <div class="summary-item">
                    <strong>Total</strong>
                    <strong>RM357.00</strong>
                </div>
                <button class="btn-pay">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- SELECT YOUR SEATS (Moved to the bottom) -->
    <div class="card">
        <h3>Select Your Seats</h3>
        <div class="seating">
            <img src="https://s3-alpha-sig.figma.com/img/8c63/bb8d/7d8569059de912d398e48294de70c898?Expires=1742774400&Key-Pair-Id=APKAQ4GOSFWCW27IBOMQ&Signature=sVIVWswqCwFm6agqNtwJHKFV3PcihxmqrmNSypyet5ahannv3s7SfJD-WkmcISTOko8fIjgB2fWUvXUmuYRSBHGwXTpoWWh0j1pkdYvPdK77f9hIuL~90zTdyr3uk3zP-FyLB7rACgxBsJRkOvyZ9FdFfUWmqoPqUefZ3DOUxcczSWHm5odJ1Wl3~NL5YIXvCP5hnmx3gn2-GZRUpdeBP0O7ZZuBFkQBip-jJkSZuQ8vA3toRl-DKGsDju2PfGRCT8FNPO5W1RdZ~xmdM96SdACDrxeKrHaLHDeoOZ0z4K3IssUtoN11WJvxlzTJnrAETxN3Gu1AKcVBsUrHboHDPw__" alt="Seating Chart">
        </div>
        <div class="seat-selection">
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

</body>
</html>
