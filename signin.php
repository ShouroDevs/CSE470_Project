<?php
session_start();
include 'dbconnect.php';

$username = $_POST['username'];
$password = $_POST['password'];


if ($username === "admin" && $password === "12345") {
    $_SESSION['username'] = $username;
    header('Location: admin.php');
    exit();
}


$sql = "SELECT userID FROM users WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userID = $row['userID'];

    $_SESSION['user_id'] = $userID;
    $_SESSION['username'] = $username;

    header('Location: home.php');
    exit();
} else {

    header('Location: index.php?error=1');
    exit();
}

$stmt->close();
$conn->close();
?>
