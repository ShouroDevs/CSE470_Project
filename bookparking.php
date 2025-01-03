<?php
include 'fetchuserinfo.php'; 
include("./header.php");


if (!isset($_POST['spot_id']) || !is_numeric($_POST['spot_id'])) {
    die("Invalid parking spot ID.");
}

$spot_id = $_POST['spot_id'];  
$payment_method = $_POST['payment_method']; 


$query = "SELECT * FROM registrationparkingspots WHERE spot_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No parking spot found with the provided ID.");
}

$spot = $result->fetch_assoc();


$updateQuery = "UPDATE registrationparkingspots SET status = status + 1 WHERE spot_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("i", $spot_id);

if ($updateStmt->execute()) {
    
    echo "<script>alert('Booking successful! You will be charged via " . htmlspecialchars($payment_method) . "');</script>";
    echo "<script>window.location = 'home.php';</script>"; 
} else {
    echo "<script>alert('Booking failed. Please try again.');</script>";
}

include("./footer.php"); 
?>
