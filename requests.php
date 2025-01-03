<?php
include("./header.php");
include 'fetchuserinfo.php';

$userID = $_SESSION['user_id']; 

if (!isset($userID)) {
    header("Location: home.php"); 
    exit;
}

$spotsQuery = $conn->prepare("SELECT spot_id, name FROM registrationparkingspots WHERE user_id = ?");
$spotsQuery->bind_param("i", $userID);
$spotsQuery->execute();
$spotsResult = $spotsQuery->get_result();

if ($spotsResult->num_rows === 0) {
    echo "<div class='no-spot-message'>";
    echo "<h3>No Parking Spots Registered</h3>";
    echo "<p>It looks like you haven't registered any parking spots yet. Please feel free to create one and start offering parking to others.</p>";
    echo "<a href='registerbook.php' class='create-spot-button'>Create a Parking Spot</a>";
    echo "</div>";
    
}

$totalRequests = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Parking Spot Reservation Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f3f4f8, #e2e6ef);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 2.4em;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }

        .spot-section {
            margin-bottom: 40px;
            position: relative;
            border-bottom: 2px solid #ececec;
            padding-bottom: 20px;
            transition: all 0.3s ease-in-out;
        }

        .spot-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #3498db;
            color: white;
            padding: 18px 30px;
            border-radius: 8px;
            font-size: 1.3em;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .spot-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .request-list {
            margin-top: 20px;
        }

        .request-card {
            background-color: #f7f8fa;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .request-card p {
            font-size: 1.1em;
            margin: 10px 0;
            color: #555;
        }

        .request-card p b {
            color: #2980b9;
        }

        .no-requests {
            text-align: center;
            font-style: italic;
            color: #888;
            font-size: 1.1em;
        }

        .back-button {
            display: inline-block;
            margin: 30px auto 0;
            padding: 12px 25px;
            background-color: #2980b9;
            color: white;
            font-size: 1.2em;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #3498db;
        }

        .total-request-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #e74c3c;
            color: white;
            padding: 15px 25px;
            border-radius: 50%;
            font-size: 1.8em;
            font-weight: bold;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .total-request-counter:hover {
            background-color: #c0392b;
        }

        .spot-header .spot-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #f2f2f2;
        }

        .request-card .card-content {
            font-size: 1.1em;
            margin: 10px 0;
            color: #555;
        }

        .no-spot-message {
            text-align: center;
            background-color: #f9f9f9;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
        }

        .no-spot-message h3 {
            font-size: 2em;
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .no-spot-message p {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .create-spot-button {
            display: inline-block;
            padding: 15px 40px;
            background-color: #2980b9;
            color: white;
            font-size: 1.2em;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .create-spot-button:hover {
            background-color: #3498db;
            transform: scale(1.05);
        }

        .create-spot-button:focus {
            outline: none;
        }

        @media screen and (max-width: 600px) {
            .no-spot-message {
                padding: 30px 20px;
            }

            .no-spot-message h3 {
                font-size: 1.8em;
            }

            .no-spot-message p {
                font-size: 1.1em;
            }

            .create-spot-button {
                padding: 12px 30px;
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Parking Spot Reservation Requests</h2>

    <div class="total-request-counter">
        <?php
        while ($spot = $spotsResult->fetch_assoc()) {
            $spotID = $spot['spot_id'];

            $requestsQuery = $conn->prepare("
                SELECT u.userID AS reserver_id, u.username, u.email, u.first_name, u.last_name
                FROM registrationparkingspots rp
                JOIN users u ON rp.user_id = u.userID
                WHERE rp.spot_id = ? AND rp.status > 0"); 
            $requestsQuery->bind_param("i", $spotID);
            $requestsQuery->execute();
            $requestsResult = $requestsQuery->get_result();

            $totalRequests += $requestsResult->num_rows;

            $requestsQuery->close();
        }

        echo $totalRequests; 
        ?>
    </div>

    <?php
    $spotsResult->data_seek(0); 

    while ($spot = $spotsResult->fetch_assoc()) {
        $spotID = $spot['spot_id'];
        $spotName = $spot['name'];

        $requestsQuery = $conn->prepare("
            SELECT u.userID AS reserver_id, u.username, u.email, u.first_name, u.last_name
            FROM registrationparkingspots rp
            JOIN users u ON rp.user_id = u.userID
            WHERE rp.spot_id = ? AND rp.status > 0");
        $requestsQuery->bind_param("i", $spotID);
        $requestsQuery->execute();
        $requestsResult = $requestsQuery->get_result();

        echo "<div class='spot-section'>";
        echo "<div class='spot-header'><h3 class='spot-name'>" . htmlspecialchars($spotName) . "</h3></div>";

        if ($requestsResult->num_rows > 0) {
            echo "<div class='request-list'>";
            while ($request = $requestsResult->fetch_assoc()) {
                echo "<div class='request-card'>";
                echo "<div class='card-content'>";
                echo "<p><b>Request from:</b> " . htmlspecialchars($request['username']) . "</p>";
                echo "<p><b>Email:</b> " . htmlspecialchars($request['email']) . "</p>";
                echo "<p><b>Full Name:</b> " . htmlspecialchars($request['first_name']) . " " . htmlspecialchars($request['last_name']) . "</p>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p class='no-requests'>No pending requests for this spot.</p>";
        }

        echo "</div>";
    }
    if ($spotsResult->num_rows > 0) {
        $spotsQuery->close();
        $requestsQuery->close();
        $conn->close();

    }

    
    ?>

    <a href="home.php" class="back-button">Back to Dashboard</a>
</div>

</body>
</html>
