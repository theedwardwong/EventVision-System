<?php
require 'config.php';

// Check for POST request and JSON content type
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if ($contentType !== "application/json") {
    echo json_encode(["success" => false, "message" => "Invalid content type"]);
    exit();
}

// Read and decode JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit();
}

// Insert tickets into the database
try {
    $stmt = $pdo->prepare("INSERT INTO tickets (category_name, price, quantity, limit_per_order) VALUES (?, ?, ?, ?)");

    foreach ($data as $ticket) {
        $stmt->execute([
            $ticket['category_name'],
            $ticket['price'],
            $ticket['quantity'],
            $ticket['limit_per_order']
        ]);
    }

    echo json_encode(["success" => true, "message" => "Tickets inserted successfully"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>


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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        cursor: pointer;
    }
    .zoom-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    .zoomed-image {
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .close-popup {
        position: absolute;
        top: 20px;
        right: 40px;
        color: white;
        font-size: 40px;
        cursor: pointer;
    }
    .zoom-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
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
    <div class="container">
    <div class="section" id="ticket-categories">
            <h3>Ticket Categories</h3>
            <form id="ticketForm">
                <div id="category-list">
                    <div class="form-group">
                        <input type="text" name="category_name[]" placeholder="Category Name" value="General Admission">
                        <input type="number" name="price[]" placeholder="Price" value="99.00">
                        <input type="number" name="quantity[]" placeholder="Quantity" value="10">
                        <select name="limit_per_order[]">
                            <option>No Limit</option>
                            <option>4 per order</option>
                        </select>
                        <button type="button" onclick="deleteCategory(this)">&#128465;</button>
                    </div>
                </div>
                <button type="button" class="add-category" onclick="addCategory()">+ Add Another Category</button>
                <button type="button" onclick="setTickets()">Set Tickets</button>
            </form>
        </div>

        <div class="section seating-layout" id="seating-layout">
            <h3>Seating Layout</h3>
            <div class="seating-container">
                <img src="image/Picture1.png" alt="Seating Layout" id="seatingImg">
                <div class="seating-options" id="seating-options">
                    <div class="seat-assignment">
                        <label>Select Ticket Category:</label>
                        <select id="ticketCategory">
                            <option value="General Admission">General Admission</option>
                            <option value="VIP Access">VIP Access</option>
                        </select>
                    </div>
                    <div class="seat-range">
                        <label>Seat Range:</label>
                        <input type="text" id="seatStart" placeholder="Start (e.g. A1)">
                        <input type="text" id="seatEnd" placeholder="End (e.g. A10)">
                        <button onclick="assignSeats()">Assign</button>
                    </div>
                    <ul id="assignedSeatsList"></ul>
                </div>
            </div>
            <button class="add-category" onclick="addSeatingOption()">+ Add Seating Option</button>
        </div>

        <div class="section" id="promo-codes">
            <h3>Promotional Codes</h3>
            <div id="promo-code-list">
                <div class="form-group">
                    <input type="text" value="EARLY25">
                    <input type="number" value="25">
                    <input type="date">
                    <select>
                        <option>All Categories</option>
                    </select>
                    <button onclick="deletePromoCode(this)">&#128465;</button>
                </div>
            </div>
            <button class="add-category" onclick="addPromoCode()">+ Add Promotional Code</button>
        </div>

        <script>
            function addCategory() {
                const categoryList = document.getElementById('category-list');
                const newCategory = document.createElement('div');
                newCategory.classList.add('form-group');
                newCategory.innerHTML = `
                    <input type="text" name="category_name[]" placeholder="Category Name">
                    <input type="number" name="price[]" placeholder="Price">
                    <input type="number" name="quantity[]" placeholder="Quantity">
                    <select name="limit_per_order[]">
                        <option>No Limit</option>
                        <option>4 per order</option>
                    </select>
                    <button type="button" onclick="deleteCategory(this)">&#128465;</button>
                `;
                categoryList.appendChild(newCategory);
            }

            function deleteCategory(button) {
                const category = button.parentElement;
                category.remove();
            }

            function addSeatingOption() {
                const seatingOptions = document.getElementById('seating-options');
                const newOption = document.createElement('select');
                newOption.innerHTML = `
                    <option>New Section - General Admission</option>
                `;
                seatingOptions.appendChild(newOption);
            }

            function addPromoCode() {
                const promoCodeList = document.getElementById('promo-code-list');
                const newPromoCode = document.createElement('div');
                newPromoCode.classList.add('form-group');
                newPromoCode.innerHTML = `
                    <input type="text" placeholder="Promo Code">
                    <input type="number" placeholder="Discount">
                    <input type="date">
                    <select>
                        <option>All Categories</option>
                    </select>
                    <button onclick="deletePromoCode(this)">&#128465;</button>
                `;
                promoCodeList.appendChild(newPromoCode);
            }

            function deletePromoCode(button) {
                const promoCode = button.parentElement;
                promoCode.remove();
            }

            function setupZoomFeature() {
                const seatingImg = document.querySelector('.seating-layout img');
                seatingImg.style.cursor = 'pointer';
                
                seatingImg.addEventListener('click', () => {
                    const zoomPopup = document.createElement('div');
                    zoomPopup.className = 'zoom-popup';
                    zoomPopup.innerHTML = `
                        <div class="zoom-overlay"></div>
                        <img src="${seatingImg.src}" alt="Zoomed Seating Layout">
                        <span class="close-popup">&times;</span>
                    `;
                    document.body.appendChild(zoomPopup);

                    document.querySelector('.close-popup').onclick = () => zoomPopup.remove();
                    document.querySelector('.zoom-overlay').onclick = () => zoomPopup.remove();
                });
            }

            /const seatingOptions = document.getElementById('seating-options');

            function addSeatingOption() {
                const newOption = document.createElement('div');
                newOption.classList.add('form-group');
                newOption.innerHTML = `
                    <select onchange="updateSeats(this)">
                        <option value="VIP Access">VIP Access</option>
                        <option value="General Admission">General Admission</option>
                    </select>
                    <input type="text" placeholder="Start Seat (e.g. A1)">
                    <input type="text" placeholder="End Seat (e.g. A10)">
                    <button onclick="deleteSeatingOption(this)">&#128465;</button>
                `;
                seatingOptions.appendChild(newOption);
            }

            function deleteSeatingOption(button) {
                button.parentElement.remove();
            }

            function updateSeats(selectElement) {
                const selectedCategory = selectElement.value;
                const totalSeats = countAssignedSeats();
                const maxSeats = getMaxSeatsByCategory(selectedCategory);
                if (totalSeats > maxSeats) {
                    alert(`Total assigned seats for ${selectedCategory} exceed the limit of ${maxSeats}.`);
                }
            }

            function countAssignedSeats() {
                let totalSeats = 0;
                document.querySelectorAll('#seating-options .form-group').forEach(group => {
                    const start = group.children[1].value;
                    const end = group.children[2].value;
                    if (start && end) {
                        totalSeats += calculateSeatCount(start, end);
                    }
                });
                return totalSeats;
            }

            function calculateSeatCount(start, end) {
                const startNum = parseInt(start.substring(1));
                const endNum = parseInt(end.substring(1));
                return Math.abs(endNum - startNum) + 1;
            }

            function getMaxSeatsByCategory(category) {
                if (category === 'VIP Access') return 200;
                if (category === 'General Admission') return 1000;
                return 0;
            }
            function setTickets() {
                const form = document.getElementById('ticketForm');
                const formData = new FormData(form);
                const tickets = [];

                // Collect form data into a JSON object
                const categories = formData.getAll('category_name[]');
                const prices = formData.getAll('price[]');
                const quantities = formData.getAll('quantity[]');
                const limits = formData.getAll('limit_per_order[]');

                for (let i = 0; i < categories.length; i++) {
                    tickets.push({
                        category_name: categories[i],
                        price: parseFloat(prices[i]),
                        quantity: parseInt(quantities[i]),
                        limit_per_order: limits[i]
                    });
                }

                // Send data as JSON
                fetch('test.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(tickets)
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    alert(data.message || 'Tickets set successfully');
                })
                .catch(error => console.error('Error:', error));
            }


            // Call zoom setup after page loads
            window.onload = setupZoomFeature;
        </script>
    </div>
</body>
</html>
