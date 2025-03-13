<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f0f4ff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .header {
            width: 850px;
            text-align: left;
            margin-bottom: 10px;
        }

        .header h2 {
            color: #222;
            font-size: 24px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .container {
            width: 850px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 80px;
        }

        .file-upload {
            padding: 20px;
            border: 2px dashed #ccc;
            text-align: center;
            color: #666;
            border-radius: 5px;
            cursor: pointer;
            background: #f9f9f9;
        }

        .file-upload span {
            color: #007bff;
            cursor: pointer;
        }

        .promo-code {
            display: flex;
            gap: 10px;
        }

        .promo-code input {
            flex: 1;
        }

        .category {
            display: flex;
            gap: 10px;
        }

        .category input {
            flex: 1;
        }

        .add-category {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            text-align: left;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .cancel {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .create {
            background: #4a3aff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Create New Event</h2>
        <p>Fill in the details below to create your event</p>
    </div>

    <div class="container">
        <form action="#" method="post" enctype="multipart/form-data">
            
            <div class="form-group">
                <div>
                    <label>Event Name</label>
                    <input type="text" name="event_name" required>
                </div>
                <div class="file-upload">
                    <p>Drop your event poster here or <span>browse files</span></p>
                    <input type="file" name="event_poster" accept="image/*" hidden>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label>Date</label>
                    <input type="date" name="event_date" required>
                </div>
                <div>
                    <label>Time</label>
                    <input type="time" name="event_time" required>
                </div>
            </div>

            <label>Event Description</label>
            <textarea name="event_description"></textarea>

            <div class="form-group">
                <div>
                    <label>Promotional Codes</label>
                    <div class="promo-code">
                        <input type="text" name="promo_code" placeholder="Code">
                        <input type="number" name="discount" placeholder="Discount %" min="0" max="100">
                    </div>
                    <button type="button" class="add-category">+ Add Promo Code</button>
                </div>

                <div>
                    <label>Seating Arrangement</label>
                    <select name="seating_arrangement">
                        <option value="Theater Style">Theater Style</option>
                        <option value="Classroom Style">Classroom Style</option>
                        <option value="Banquet Style">Banquet Style</option>
                    </select>
                </div>
            </div>

            <label>Ticket Categories</label>
            <div class="category">
                <input type="text" name="category_name" placeholder="Category Name">
                <input type="number" name="price" placeholder="Price">
                <input type="number" name="quantity" placeholder="Quantity">
            </div>
            <button type="button" class="add-category">+ Add Another Category</button>

            <div class="buttons">
                <button type="reset" class="cancel">Cancel</button>
                <button type="submit" class="create">Create Event</button>
            </div>

        </form>
    </div>

</body>
</html>
