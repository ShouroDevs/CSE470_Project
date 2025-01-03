<?php


include 'fetchuserinfo.php';
include("./header.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid parking spot ID. Please check the URL.");
}
$spot_id = $_GET['id'];  

$query = "SELECT * FROM registrationparkingspots WHERE spot_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spot_id);
$stmt->execute();
$result = $stmt->get_result();


$stmtQ = $conn->prepare("INSERT INTO reservation (user_id, spot_id) VALUES (?, ?)");
$stmtQ->bind_param("ii", $userID, $spot_id);
$stmtQ->execute();

if ($result->num_rows === 0) {
    die("No parking spot found with the provided ID.");
}

$spot = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/design.css">
    <title>Parking Spot Details - <?= htmlspecialchars($spot['name']) ?></title>
    <style>
        body {
            background-color: #f0f0f0;
            color: #333;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .spot-details {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .spot-info {
            margin-bottom: 20px;
        }

        .spot-info b {
            color: #333;
        }

        .spot-info p {
            color: #555;
        }

        .price {
            font-size: 1.5em;
            color: #27ae60;
            text-align: center;
        }

        .booking-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #27ae60;
            color: white;
            font-size: 1.2em;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .booking-btn:hover {
            background-color: #2ecc71;
        }

        @media (max-width: 1200px) {
            .spot-details {
                width: 90%;
                padding: 15px;
            }
            .price {
                font-size: 1.3em;
            }
        }

        @media (max-width: 768px) {
            .spot-details {
                width: 95%;
                padding: 10px;
            }
            h1 {
                font-size: 1.5em;
            }
            .spot-info p, .price {
                font-size: 1em;
            }
            .booking-btn {
                font-size: 1em;
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            .spot-details {
                width: 100%;
                padding: 10px;
            }
            h1 {
                font-size: 1.3em;
            }
            .spot-info b, .spot-info p {
                font-size: 0.9em;
            }
            .price {
                font-size: 1.2em;
            }
            .booking-btn {
                font-size: 1.1em;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="spot-details">
        <h1><?= htmlspecialchars($spot['name']) ?> - Parking Spot Details</h1>

        <div class="spot-info">
            <b>Location:</b>
            <p><?= htmlspecialchars($spot['description']) ?> - Latitude: <?= htmlspecialchars($spot['latitude']) ?>, Longitude: <?= htmlspecialchars($spot['longitude']) ?></p>

            <b>Availability:</b>
            <p><?= htmlspecialchars($spot['available'] == 1 ? 'Available' : 'Not Available') ?></p>

            <b>Created At:</b>
            <p><?= htmlspecialchars($spot['created_at']) ?></p>
        </div>

        <div class="price">
            <p>Price: $<?= htmlspecialchars($spot['price']) ?> per hour</p>
        </div>

        <form action="bookparking.php" method="POST">
            <input type="hidden" name="spot_id" value="<?= htmlspecialchars($spot['spot_id']) ?>">
            
            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="Bkash">Bkash</option>
                <option value="Nagad">Nagad</option>
                <option value="Rocket">Rocket</option>
                <option value="Bank">Bank</option>
            </select>
            
            <button type="submit" class="booking-btn">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>

<?php include("./footer.php"); ?> 

