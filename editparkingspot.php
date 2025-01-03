<?php
include 'fetchuserinfo.php';
include("./header.php");

if (!isset($_GET['spot_id']) || !is_numeric($_GET['spot_id'])) {
    die("Invalid parking spot ID.");
}

$spot_id = intval($_GET['spot_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $available = isset($_POST['available']) ? 1 : 0;
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);

    $updateQuery = $conn->prepare("UPDATE registrationparkingspots SET name = ?, description = ?, available = ?, latitude = ?, longitude = ? WHERE spot_id = ? AND user_id = ?");
    if (!$updateQuery) {
        die("Error preparing query: " . $conn->error);
    }

    $updateQuery->bind_param("ssisiii", $name, $description, $available, $latitude, $longitude, $spot_id, $userID);

    if ($updateQuery->execute()) {
        header("Location: profile.php?success=1");
        exit;
    } else {
        echo "Error updating parking spot: " . $conn->error;
    }
}

$query = $conn->prepare("SELECT name, description, available, latitude, longitude FROM registrationparkingspots WHERE spot_id = ? AND user_id = ?");
if (!$query) {
    die("Error preparing query: " . $conn->error);
}

$query->bind_param("ii", $spot_id, $userID);
$query->execute();
$result = $query->get_result();

if ($result->num_rows !== 1) {
    die("Parking spot not found or access denied.");
}

$parkingSpot = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/design.css">
    <title>Edit Parking Spot</title>
</head>
<body>
<div class="wholebody">
    <section class="userinfo">
        <h1>Edit Parking Spot</h1>
        <form method="POST" action="">
            <label for="name">Spot Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($parkingSpot['name']) ?>" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($parkingSpot['description']) ?></textarea><br><br>

            <label for="available">Available:</label>
            <input type="checkbox" id="available" name="available" <?= $parkingSpot['available'] ? 'checked' : '' ?>><br><br>

            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($parkingSpot['latitude']) ?>" required><br><br>

            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($parkingSpot['longitude']) ?>" required><br><br>

            <button type="submit" class="btn-primary">Save Changes</button>
            <button type="button" class="btn-primary" onclick="window.location.href='parkingmanager.php'">Cancel</button>
        </form>
    </section>
</div>
<?php include("./footer.php"); ?>
</body>
</html>
