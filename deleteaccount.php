<?php
include 'fetchuserinfo.php';


$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userID) {
    header('Location: index.php');
    exit();
}


$conn->begin_transaction();

try {
    
    $deleteFeedback = $conn->prepare("DELETE FROM feedback WHERE userID = ?");
    $deleteFeedback->bind_param("i", $userID);
    $deleteFeedback->execute();
    $deleteFeedback->close();

  
    $deleteSwaps = $conn->prepare("DELETE FROM swap WHERE requesterID = ? OR ownerID = ?");
    $deleteSwaps->bind_param("ii", $userID, $userID);
    $deleteSwaps->execute();
    $deleteSwaps->close();

    
    $deleteCollections = $conn->prepare("DELETE FROM collection WHERE userID = ?");
    $deleteCollections->bind_param("i", $userID);
    $deleteCollections->execute();
    $deleteCollections->close();

  
    $deleteBooks = $conn->prepare("DELETE FROM books WHERE bookID IN (SELECT bookID FROM collection WHERE userID = ?)");
    $deleteBooks->bind_param("i", $userID);
    $deleteBooks->execute();
    $deleteBooks->close();

    $deleteUser = $conn->prepare("DELETE FROM users WHERE userID = ?");
    $deleteUser->bind_param("i", $userID);
    $deleteUser->execute();
    $deleteUser->close();

    
    $conn->commit();


    session_destroy();
    header('Location: index.php');
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "Error deleting account: " . $e->getMessage();
}
?>