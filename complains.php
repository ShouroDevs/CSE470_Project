<?php

include 'fetchuserinfo.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $spotId = $_POST['spot_id'];
    $complaintText = $_POST['complaint_text']; 


    $insertComplaintQuery = $conn->prepare("
        INSERT INTO complaints (user_id, spot_id, complaint_text, status, complaint_date)
        VALUES (?, ?, ?, 'Pending', NOW())
    ");
    
 
    $insertComplaintQuery->bind_param("iis", $userId, $spotId, $complaintText);

 
    if ($insertComplaintQuery->execute()) {
        echo "<script>alert('Complaint submitted successfully.'); window.location.href = 'complains.php';</script>";
    } else {
        echo "<script>alert('Error submitting complaint.');</script>";
    }
    $insertComplaintQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Complaint</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, textarea, button {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .back-button {
            position: absolute;
            top: 10px;
            
            background-color:rgb(85, 85, 171);
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div style="position: absolute; top: 10px; left: 10px;">
    <button class="back-button" onclick="window.history.back();">← Go Back</button>
</div>
<div class="container">
    <h1>Submit a Complaint</h1>
    <form method="POST">
        <input type="hidden" name="user_id" value="1"> 
        <input type="hidden" name="spot_id" value="1"> 
        <textarea name="complaint_text" rows="5" placeholder="Write your complaint here..." required></textarea>
        <button type="submit">Submit Complaint</button>
    </form>
</div>
</body>
</html>
