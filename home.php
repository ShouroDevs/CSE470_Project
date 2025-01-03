<?php

include("./header.php");
include 'fetchuserinfo.php';
$spotsQuery = $conn->prepare("
    SELECT id, name, latitude, longitude, available
    FROM spots
    WHERE available = 1
");
$spotsQuery->execute();
$spotsResult = $spotsQuery->get_result();

$searchQuery = isset($_POST['q']) ? $_POST['q'] : '';  
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
            width: 85%;
            height: 440px;
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
        <p class="info-text" id="available-spots-info">
            Loading available parking spots...
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
            id: <?= $spot['id'] ?>,
            name: "<?= htmlspecialchars($spot['name']) ?>",
            lat: <?= $spot['latitude'] ?>,
            lng: <?= $spot['longitude'] ?>
        },
        <?php endwhile; ?>
    ];

    const map = L.map('map').setView([23.8103, 90.4125], 12); 


    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '',
    }).addTo(map);

    function calculateDistance(lat1, lng1, lat2, lng2) {
        const toRad = (value) => (value * Math.PI) / 180;
        const R = 6371; 
        const dLat = toRad(lat2 - lat1);
        const dLng = toRad(lng2 - lng1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; 
    }

  
    function displaySpots(centerLat, centerLng, radius) {
       
        map.eachLayer(layer => {
            if (layer instanceof L.Marker) {
                map.removeLayer(layer);
            }
        });

       
        const filteredSpots = spots.filter(spot => {
            const distance = calculateDistance(centerLat, centerLng, spot.lat, spot.lng);
            return distance <= radius;
        });

        const infoText = document.getElementById("available-spots-info");
        infoText.textContent = filteredSpots.length > 0
            ? `Available parking spots within ${radius}km: ${filteredSpots.length}`
            : `No parking spots available within ${radius}km.`;

       
        filteredSpots.forEach(spot => {
            const marker = L.marker([spot.lat, spot.lng]).addTo(map);
            marker.bindPopup(
                `<strong>${spot.name}</strong><br>Distance: ${calculateDistance(centerLat, centerLng, spot.lat, spot.lng).toFixed(2)} km<br><a href="spotdetails.php?id=${spot.id}">View Details</a>`
            );
        });
    }

  
    <?php if (!empty($searchQuery)): ?>
   
    const searchQuery = "<?= htmlspecialchars($searchQuery) ?>";
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery)}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const searchLat = parseFloat(data[0].lat);
                const searchLng = parseFloat(data[0].lon);

             
                map.setView([searchLat, searchLng], 10);

              
                displaySpots(searchLat, searchLng, 10);
            } else {
                console.error("No results found for the search query.");
            }
        })
        .catch(error => console.error("Error fetching search location:", error));
    <?php else: ?>
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            map.setView([userLat, userLng], 14);

           
            displaySpots(userLat, userLng, 10);
        }, error => {
            console.error("Error fetching geolocation:", error.message);

       
            displaySpots(23.8103, 90.4125, 10);
        });
    } else {
        console.error("Geolocation not supported by this browser.");

    
        displaySpots(23.8103, 90.4125, 10);
    }
    <?php endif; ?> 
</script>

</body>
</html>

<?php
$spotsQuery->close();
$conn->close();
?>

