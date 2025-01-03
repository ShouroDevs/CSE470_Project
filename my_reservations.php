<?php
include("./header.php");  
include 'fetchuserinfo.php';  


$reservedParkingQuery = $conn->prepare("
    SELECT rp.spot_id, rp.name, rp.description, rp.latitude, rp.longitude, rp.price, rp.status
    FROM reservation r
    JOIN registrationparkingspots rp ON r.spot_id = rp.spot_id
    WHERE r.user_id = ? 
");

if ($reservedParkingQuery === false) {
    die('Error preparing statement: ' . $conn->error); 
}

$reservedParkingQuery->bind_param("i", $viewingUser); 
$reservedParkingQuery->execute();
$reservedParkingResult = $reservedParkingQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reserved Parking Spots</title>
    <style>
     
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f9;
            color: #333;
            min-height: 100vh;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

    
        .container {
            width: 100%;
            max-width: 1200px;
            padding: 40px;
            margin-top: 80px; 
            background: #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            align-self: center;
        }

        .section-title {
            font-size: 2rem;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .parking-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .item-box {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .item-box h2 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .item-box p {
            font-size: 1rem;
            line-height: 1.6;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .price {
            font-size: 1.2rem;
            color: #e74c3c;
            font-weight: bold;
            margin-top: 10px;
        }

        .status {
            font-size: 1rem;
            font-weight: bold;
            color: #27ae60;
            margin-top: 10px;
        }

        .btn-secondary {
            text-align: center;
            padding: 12px;
            width: 100%;
            background-color: #3498db;
            color: white;
            border: none;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 30px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .item-box {
                padding: 15px;
            }

            .item-box h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="section-title">Your Reserved Parking Spots</h1>
        <div class="parking-list">
            <?php if ($reservedParkingResult->num_rows > 0): ?>
                <?php while ($row = $reservedParkingResult->fetch_assoc()): ?>
                    <div class="item-box">
                        <h2><?= htmlspecialchars($row['name']) ?></h2>
                        <p><b>Description:</b> <?= htmlspecialchars($row['description']) ?></p>
                        <p><b>Location:</b> Latitude <?= htmlspecialchars($row['latitude']) ?>, Longitude <?= htmlspecialchars($row['longitude']) ?></p>
                        <p class="price"><b>Price:</b> $<?= htmlspecialchars($row['price']) ?></p>
                        <p class="status"><b>Status:</b> <?= htmlspecialchars($row['status'] == 1 ? 'Active' : 'active') ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reserved parking spots found.</p>
            <?php endif; ?>
        </div>

        <a href="home.php" class="btn-secondary">Back to Home</a>
    </div>

<?php include("./footer.php"); ?>
</body>
</html>


$reservedParkingQuery->close();
$conn->close();
?>
