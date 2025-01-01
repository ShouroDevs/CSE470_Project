<?php
include 'fetchuserinfo.php'; // Ensure user authentication and connection setup

// Retrieve the logged-in user’s ID from the session
$userID = $_SESSION['user_id']; // Make sure the session contains the user ID

// Check if the user is logged in
if (!isset($userID)) {
    header("Location: home.php"); // Redirect to login page if user is not logged in
    exit;
}

// Fetch all parking spots that belong to the logged-in user (owner)
$spotsQuery = $conn->prepare("SELECT spot_id, name FROM registrationparkingspots WHERE user_id = ?");
$spotsQuery->bind_param("i", $userID);
$spotsQuery->execute();
$spotsResult = $spotsQuery->get_result();

// If no spots are found for this user
if ($spotsResult->num_rows === 0) {
    echo "No parking spots found for your user ID.";
    exit;
}

// Initialize total request counter
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

        /* Total Request Counter */
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
    </style>
</head>
<body>

<div class="container">
    <h2>Parking Spot Reservation Requests</h2>

    <!-- Total Request Counter -->
    <div class="total-request-counter">
        <?php
        // Iterate over each spot to count total requests
        while ($spot = $spotsResult->fetch_assoc()) {
            $spotID = $spot['spot_id'];

            // Get the reservation requests (pending or other status) for the current spot
            $requestsQuery = $conn->prepare("
                SELECT u.userID AS reserver_id, u.username, u.email, u.first_name, u.last_name
                FROM registrationparkingspots rp
                JOIN users u ON rp.user_id = u.userID
                WHERE rp.spot_id = ? AND rp.status > 0"); // Filter spots with any status (booked/reserved)
            $requestsQuery->bind_param("i", $spotID);
            $requestsQuery->execute();
            $requestsResult = $requestsQuery->get_result();

            // Update the total request count
            $totalRequests += $requestsResult->num_rows;

            // Reset the request query for the next iteration
            $requestsQuery->close();
        }

        echo $totalRequests; // Display total request count
        ?>
    </div>

    <?php
    // Reset the result pointer and display details of each spot
    $spotsResult->data_seek(0); // Reset the result pointer to start from the beginning

    while ($spot = $spotsResult->fetch_assoc()) {
        $spotID = $spot['spot_id'];
        $spotName = $spot['name'];

        // Get the reservation requests (pending or other status) for the current spot
        $requestsQuery = $conn->prepare("
            SELECT u.userID AS reserver_id, u.username, u.email, u.first_name, u.last_name
            FROM registrationparkingspots rp
            JOIN users u ON rp.user_id = u.userID
            WHERE rp.spot_id = ? AND rp.status > 0");
        $requestsQuery->bind_param("i", $spotID);
        $requestsQuery->execute();
        $requestsResult = $requestsQuery->get_result();

        // Display spot and reservation details
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

    $spotsQuery->close();
    $requestsQuery->close();
    $conn->close();
    ?>

    <a href="dashboard.php" class="back-button">Back to Dashboard</a>
</div>

</body>
</html>
