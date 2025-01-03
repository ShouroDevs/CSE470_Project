<?php
include 'fetchuserinfo.php';
include("./header.php");

if (!isset($_GET['spot_id']) || !is_numeric($_GET['spot_id'])) {
    die("Invalid parking spot ID.");
}

$spot_id = intval($_GET['spot_id']);

$query = $conn->prepare("DELETE FROM registrationparkingspots WHERE spot_id = ? AND user_id = ?");
if (!$query) {
    die("Error preparing query: " . $conn->error);
}

$query->bind_param("ii", $spot_id, $userID);

if ($query->execute()) {
    header("Location: profile.php?success=1");
    exit;
} else {
    echo "Error deleting parking spot: " . $conn->error;
}

include("./footer.php");
?>
