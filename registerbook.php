<?php
session_start();
include 'dbconnect.php';
include("./header.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spotName = $_POST['spot_name'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $available = $_POST['available'];
    $description = $_POST['description']; 
    $price = $_POST['price']; 

    $userID = $_SESSION['user_id'];
    $sql = "SELECT COUNT(*) AS row_count FROM spots";
    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    $row_count = $row['row_count'];
    $row_count = $row_count + 1;
    $spotID = $row_count;
    $spotQuery = $conn->prepare("INSERT INTO spots (name, latitude, longitude, available) VALUES (?, ?, ?, ? )");
    $spotQuery->bind_param("ssdi", $spotName, $latitude, $longitude, $available);

    if ($spotQuery->execute()) {
        $spotID = $conn->insert_id;

        $registrationQuery = $conn->prepare("INSERT INTO registrationparkingspots (spot_id, user_id , name ,description, available , latitude , longitude , price) VALUES (?, ? , ? , ? , ? , ? , ?, ?)");
        $registrationQuery->bind_param("iissisdi", $spotID , $userID ,   $spotName ,$description , $available , $latitude, $longitude, $price);

        if ($registrationQuery->execute()) {
            header('Location: profile.php?success=1'); 
            exit();
        } else {
            $error = "Error linking the parking spot to your account: " . $registrationQuery->error;
        }

        $registrationQuery->close();
    } else {
        $error = "Error adding the parking spot: " . $spotQuery->error;
    }

    $spotQuery->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/signin.css">
    <title>Register Parking Spot</title>
    <style>
        body {
            background-image: url("./images/site.png");
            background-size: cover;
            background-color: black;
            color: white;
            background-repeat: no-repeat;
            height: 100vh;
            background-attachment: fixed;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            align-items: flex-start;
            gap: 20px;
        }

        .form-container {
            width: 48%;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .form-container h1 {
            text-align: center;
            color: #ff6347;
        }

        .form-container label {
            font-size: 18px;
            color: white;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        .form-container input{
            color: black;
        }
        .textbox{
            color: black;
        }

        .form-container button {
            width: 100%;
            padding: 15px;
            background-color: #ff6347;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #ff4500;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        .map-container {
            width: 48%;
            height: 400px;
            position: relative;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        #map {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }

    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h1>Add a New Parking Spot</h1>
            
            <form action="registerbook.php" method="post" id="parkingForm">
                <label for="spot_name">Parking Spot Name:</label>
                <input class="textbox" type="text" name="spot_name" required><br>

                <label for="description">Description:</label>
                <textarea class="textbox" name="description" required></textarea><br>

                <label for="available">Availability (1 for available, 0 for not available):</label>
                <input class="textbox" type="number" name="available" min="0" max="1" required><br>

                <label for="price">Price (per hour):</label>
                <input class="textbox" type="number" name="price" step="0.01" required><br>

                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <button class="button" type="submit">Register Spot</button>
            </form>

            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </div>

        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <?php include("./footer.php"); ?>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        const map = L.map('map').setView([23.8103, 90.4125], 12); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '',
        }).addTo(map);

        let marker;

        map.on('click', function (e) {
            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);

            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });

        const searchControl = L.Control.geocoder().addTo(map);
    </script>
</body>
</html>
