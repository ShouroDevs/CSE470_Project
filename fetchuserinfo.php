<?php
session_start();
include 'dbconnect.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

if (!$userID) {
    header('Location: index.php');
    exit();
}


$viewingUser = isset($_GET['userID']) ? intval($_GET['userID']) : $userID;


$fetch = $conn->prepare("SELECT first_name, last_name, email, date_of_birth, username FROM users WHERE userID = ?");
$fetch->bind_param("i", $viewingUser);
$fetch->execute();
$result = $fetch->get_result();


if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

$fetch->close();


$collections = $conn->prepare("SELECT books.bookID, books.title, books.author, books.year_published, collection.showcase FROM collection JOIN books ON collection.bookID = books.bookID WHERE collection.userID = ?");
$collections->bind_param("i", $viewingUser);
$collections->execute();
$collectionResult = $collections->get_result();

$books = [];
$showcasedBooks = []; 

if ($collectionResult->num_rows > 0) {
    while ($row = $collectionResult->fetch_assoc()) {
        $books[] = $row; 
        if ($row['showcase'] == 1) {
            $showcasedBooks[] = $row; 
        }
    }
}

$collections->close();
?>
