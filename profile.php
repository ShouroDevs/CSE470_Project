<?php
include 'fetchuserinfo.php';
include("./header.php");

if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <p class="success-message">Parking space registered successfully!</p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/design.css">
    <title><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?> - Parking Manager</title>
    <style>
        body {
            background-image: url("./images/site.png");
            background-size: cover;
            background-color: black;
            color: white;
            background-repeat: no-repeat;
            height: 100vh;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
        }

        .wholebody {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .userinfo, .parking-spaces {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            width: 80%;
            max-width: 1200px;
        }

        .userinfo h1, .parking-spaces h2 {
            font-size: 2.5em;
            color: #00ff99;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(0, 255, 153, 0.7);
        }

        .parking-spaces ul {
            list-style-type: none;
            padding: 0;
        }

        .parking-spaces li {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .parking-spaces li:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            background: rgba(255, 255, 255, 0.15);
        }

        .parking-spaces li b {
            font-size: 1.2em;
            color: #00ff99;
            display: block;
            margin-bottom: 5px;
        }

        .parking-spaces li p {
            color: #ddd;
            font-size: 1.1em;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .btn-primary {
            background-color: #00ff99;
            color: black;
            border: none;
            padding: 12px 20px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #00cc80;
            transform: translateY(-3px);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 255, 153, 0.7);
        }

        .success-message {
            text-align: center;
            font-size: 1.5em;
            color: #00ff99;
            margin: 20px 0;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="wholebody">
        <section class="userinfo">
            <h1>Profile Information:</h1>
            <?php if ($user): ?>
                <p>Name: <b><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></b></p>
                <p>Email: <b><?= htmlspecialchars($user['email']) ?></b></p>
                <p>Date of Birth: <b><?= htmlspecialchars($user['date_of_birth']) ?></b></p>
                <p>Username: <b><?= htmlspecialchars($user['username']) ?></b></p>
                <?php if ($viewingUser == $userID): ?>
                    <button onclick="window.location.href='updateprofile.php'" class="btn-primary">Update Profile</button>
                    <button onclick="confirmDeleteAccount()" class="btn-primary">Delete Account</button>
                    <button onclick="window.location.href='logout.php'" class="btn-primary">Logout</button>
                <?php endif; ?>
            <?php else: ?>
                <p>User not found.</p>
            <?php endif; ?>
        </section>

        <section class="parking-spaces">
            <h2>Your Parking Spaces:</h2>

            <?php
            if (!$conn) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            if (!isset($userID) || !is_numeric($userID)) {
                die("Invalid user ID: " . htmlspecialchars($userID));
            }

            $parkingQuery = $conn->prepare("SELECT spot_id, name, description, available, latitude, longitude, created_at FROM registrationparkingspots WHERE user_id = ?");
            if (!$parkingQuery) {
                die("Error in query preparation: " . $conn->error);
            }

            $parkingQuery->bind_param("i", $userID);
            $parkingQuery->execute();
            $parkingResult = $parkingQuery->get_result();

            if ($parkingResult === false) {
                die("Error in query execution: " . $conn->error);
            }

            if ($parkingResult->num_rows > 0): ?>
                <ul>
                    <?php while ($parking = $parkingResult->fetch_assoc()): ?>
                        <li>
                            <b>Spot Name:</b> <?= htmlspecialchars($parking['name']) ?><br>
                            <b>Description:</b> <?= htmlspecialchars($parking['description']) ?><br>
                            <b>Available:</b> <?= $parking['available'] ? 'Yes' : 'No' ?><br>
                            <b>Location:</b> Latitude: <?= htmlspecialchars($parking['latitude']) ?>, Longitude: <?= htmlspecialchars($parking['longitude']) ?><br>
                            <b>Created At:</b> <?= htmlspecialchars($parking['created_at']) ?><br>
                            
                            <?php if ($viewingUser == $userID): ?>
                                <button onclick="window.location.href='editparkingspot.php?spot_id=<?= htmlspecialchars($parking['spot_id']) ?>'" class="btn-primary">Edit</button>
                                <button onclick="window.location.href='deleteparkingspot.php?spot_id=<?= htmlspecialchars($parking['spot_id']) ?>'" class="btn-primary">Delete</button>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No parking spaces registered yet.</p>
            <?php endif; ?>

            <?php if ($viewingUser == $userID): ?>
                <button onclick="window.location.href='registerparkingspot.php'" class="btn-primary">Register New Parking Space</button>
            <?php endif; ?>
        </section>
    </div>

    <script>
        function confirmDeleteAccount() {
            const confirmed = confirm("Are you sure you want to delete your account?");
            if (confirmed) {
                window.location.href = 'deleteaccount.php';
            }
        }
    </script>

<?php include("./footer.php"); ?>
</body>
</html>
