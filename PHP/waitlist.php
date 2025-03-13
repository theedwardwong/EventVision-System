<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Page</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        /* Event Container */
        .event-container {
            display: flex;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 830px;
            margin: auto;
            justify-content: space-between;
            align-items: center;
        }

        /* Event Image & Details */
        .event-details {
            text-align: left;
            width: 50%;
        }
        .event-details img {
            width: 100%;
    height: 185px; /* Keeps uniform height */
    object-fit: cover; /* Crops and zooms in */
    object-position: center; /* Centers the image */
    border-radius: 8px;
        }
        .event-details h2 {
            font-size: 20px;
            margin: 10px 0;
        }
        .event-details p {
            color: #777;
            font-size: 14px;
        }

        /* Waitlist Section */
        .event-waitlist {
            background: #f4f5f7;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            width: 40%;
        }
        .event-waitlist h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .event-waitlist input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .event-waitlist button {
            background: #ff9f00;
            color: white;
            border: none;
            padding: 10px;
            width: 105%;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        /* Similar Events */
        .similar-events {
            display: flex;
            gap: 15px;
            justify-content: left;
            max-width: 870px;
            margin: auto;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        .event-card img {
            width:80%;
	    height:150px;
            border-radius: 8px;
        }
        .event-card button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .event-container {
                flex-direction: column;
                text-align: center;
            }
            .event-details, .event-waitlist {
                width: 100%;
            }
            .similar-events {
                flex-direction: column;
                align-items: center;
            }
        }

    </style>
</head>
<body>
    <div class="event-container">
        <div class="event-details">
            <img src="https://d1csarkz8obe9u.cloudfront.net/posterpreviews/spring-music-festival-poster-design-template-b08b14431a1307dd55b8de20e24f2f16_screen.jpg?ts=1698502392" 
                 alt="Spring Music Festival">
            <h2>Spring Music Festival</h2>
            <p>📅 March 17, 2025 &nbsp; ⏰ 9:00 AM - 6:00 PM &nbsp; 📍 HELP Subang 2</p>
        </div>
        <div class="event-waitlist">
            <h3>Event Sold Out</h3>
            <p>Join the waitlist to get notified when tickets become available</p>
            <input type="email" placeholder="Email Address">
            <input type="tel" placeholder="Phone Number">
            <button>Join Waitlist</button>
	     <p class="waitlist-count">10 people on waitlist </p>
        </div>
    </div>

    <h3 class="similar-title">Similar Events You Might Like</h3>
    <div class="similar-events">
        <div class="event-card">
            <img src="https://d1csarkz8obe9u.cloudfront.net/posterpreviews/spring-music-festival-poster-design-template-b08b14431a1307dd55b8de20e24f2f16_screen.jpg?ts=1698502392" 
                 alt="Spring Music Festival">
            <h4>Spring Music Festival</h4>
            <p>📅 March 17, 2025</p>
            <button>View Event</button>
        </div>

        <div class="event-card">
            <img src="https://www.euro-dance-festival.com/wp-content/uploads/2024/12/edf_rust_home_04.jpg" 
                 alt="HELP Dance Festival">
            <h4>HELP Dance Festival</h4>
            <p>📅 March 24, 2025</p>
            <button>View Event</button>
        </div>

        <div class="event-card">
            <img src="https://static01.nyt.com/images/2009/05/18/theater/Jackson600.jpg?quality=75&auto=webp&disable=upscale" 
                 alt="Love Emo Story Theatre">
            <h4>Love Emo Story Theatre</h4>
            <p>📅 March 31, 2025</p>
            <button>View Event</button>
        </div>
    </div>
</body>
</html>
