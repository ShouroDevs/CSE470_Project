<?php
include("./header.php");
include 'fetchuserinfo.php';

// Fetch available parking spots
$spotsQuery = $conn->prepare("
    SELECT id, name, latitude, longitude, available
    FROM spots
    WHERE available = 1
");
$spotsQuery->execute();
$spotsResult = $spotsQuery->get_result();
$totalSpots = $spotsResult->num_rows; // Count the number of available spots
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/design.css">
    <title>Park & GO - Available Spots</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #eaeaea;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            text-align: center;
            padding: 10px;
        }

        .info-text {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #00fff0;
            text-shadow: 0 0 8px #00fff0, 0 0 16px #00fff0;
        }

        .map-container {
            margin: 20px auto;
            width: 85%; /* Slightly less wide */
            height: 440px; /* 40px taller */
            border-radius: 10px;
            overflow: hidden;
            background: #0f2027;
            box-shadow: 0 0 20px rgba(0, 255, 240, 0.6), 0 0 40px rgba(0, 255, 240, 0.4);
        }

        #map {
            width: 100%;
            height: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>
    <div class="container">
        <p class="info-text">
            <?= $totalSpots > 0 ? "Available parking spots: $totalSpots" : "No parking spots available." ?>
        </p>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <?php include("./footer.php"); ?>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        const spots = [
            <?php while ($spot = $spotsResult->fetch_assoc()): ?>
            {
                name: "<?= htmlspecialchars($spot['name']) ?>",
                lat: <?= $spot['latitude'] ?>,
                lng: <?= $spot['longitude'] ?>
            },
            <?php endwhile; ?>
        ];

        const map = L.map('map').setView([23.8103, 90.4125], 12); // Centered on Dhaka

        // Adding tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '',
        }).addTo(map);

        // Adding markers
        spots.forEach(spot => {
            L.marker([spot.lat, spot.lng]).addTo(map)
                .bindPopup(`<strong>${spot.name}</strong>`);
        });
    </script>
</body>
</html>

<?php
$spotsQuery->close();
$conn->close();
?>
