<?php
include 'fetchuserinfo.php';
include("./header.php");


if (!isset($_GET['spot_id']) || !is_numeric($_GET['spot_id'])) {
    die("Invalid parking spot ID.");
}

$spot_id = intval($_GET['spot_id']);


$conn->begin_transaction();

try {
  
    $query = $conn->prepare("DELETE FROM registrationparkingspots WHERE spot_id = ? AND user_id = ?");
    if (!$query) {
        throw new Exception("Error preparing query: " . $conn->error);
    }
    $query->bind_param("ii", $spot_id, $userID);
    if (!$query->execute()) {
        throw new Exception("Error deleting parking spot from registrationparkingspots: " . $conn->error);
    }

    
    $query = $conn->prepare("DELETE FROM reservation WHERE spot_id = ?");
    if (!$query) {
        throw new Exception("Error preparing query: " . $conn->error);
    }
    $query->bind_param("i", $spot_id);
    if (!$query->execute()) {
        throw new Exception("Error deleting parking spot from reservation: " . $conn->error);
    }

   
    $query = $conn->prepare("DELETE FROM spots WHERE id = ?");
    if (!$query) {
        throw new Exception("Error preparing query: " . $conn->error);
    }
    $query->bind_param("i", $spot_id);
    if (!$query->execute()) {
        throw new Exception("Error deleting parking spot from spots: " . $conn->error);
    }


    $conn->commit();

   
    header("Location: profile.php?success=1");
    exit;

} catch (Exception $e) {
   
    $conn->rollback();
    echo $e->getMessage();
}

include("./footer.php");
?>
