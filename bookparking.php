<?php
include 'fetchuserinfo.php'; // Include file to fetch user info
include("./header.php");

// Check if the 'spot_id' is passed via POST
if (!isset($_POST['spot_id']) || !is_numeric($_POST['spot_id'])) {
    die("Invalid parking spot ID.");
}

$spot_id = $_POST['spot_id'];  // Get the spot ID from the POST request
$payment_method = $_POST['payment_method']; // Get the selected payment method from the dropdown

// Fetch parking spot details from the database
$query = "SELECT * FROM registrationparkingspots WHERE spot_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No parking spot found with the provided ID.");
}

$spot = $result->fetch_assoc();

// Increment the status column to reflect the number of bookings
$updateQuery = "UPDATE registrationparkingspots SET status = status + 1 WHERE spot_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("i", $spot_id);

if ($updateStmt->execute()) {
    // Booking successful, redirect or show a success message
    echo "<script>alert('Booking successful! You will be charged via " . htmlspecialchars($payment_method) . "');</script>";
    echo "<script>window.location = 'home.php';</script>"; // Redirect to a "Thank You" page or booking confirmation page
} else {
    echo "<script>alert('Booking failed. Please try again.');</script>";
}

include("./footer.php"); 
?>
