<?php
include 'fetchuserinfo.php'; 
$reservationsQuery = "
    SELECT 
        r.user_id,
        r.spot_id,
        u.username,
        u.email,
        u.first_name,
        u.last_name,
        rp.name AS spot_name,
        rp.description,
        rp.price,
        rp.status
    FROM 
        reservation r
    JOIN 
        users u 
    ON 
        r.user_id = u.userID
    JOIN 
        registrationparkingspots rp
    ON 
        r.spot_id = rp.spot_id
";
$reservationsResult = $conn->query($reservationsQuery);


$ownersQuery = "
    SELECT 
        rp.spot_id,
        rp.name AS spot_name,
        rp.description,
        rp.price,
        rp.status,
        u.userID AS owner_id,
        u.username,
        u.email,
        u.first_name,
        u.last_name
    FROM 
        registrationparkingspots rp
    JOIN 
        users u 
    ON 
        rp.user_id = u.userID
";
$ownersResult = $conn->query($ownersQuery);

$complaintsQuery = "
    SELECT 
        c.complaint_id,
        u.username,
        u.email,
        rp.name AS spot_name,
        c.complaint_text,
        c.status
    FROM 
        complaints c
    JOIN 
        users u 
    ON 
        c.user_id = u.userID
    JOIN 
        registrationparkingspots rp
    ON 
        c.spot_id = rp.spot_id
";
$complaintsResult = $conn->query($complaintsQuery);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_reservation'])) {
        $userIDToDelete = $_POST['delete_user_id'];
        $spotIDToDelete = $_POST['delete_spot_id'];
        $deleteReservationQuery = $conn->prepare("DELETE FROM reservation WHERE user_id = ? AND spot_id = ?");
        $deleteReservationQuery->bind_param("ii", $userIDToDelete, $spotIDToDelete);
        if ($deleteReservationQuery->execute()) {
            echo "<script>alert('Reservation deleted successfully.'); window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting reservation.');</script>";
        }
        $deleteReservationQuery->close();
    } elseif (isset($_POST['delete_owner'])) {
        $spotIDToDelete = $_POST['delete_spot_id'];
        $deleteOwnerQuery = $conn->prepare("DELETE FROM registrationparkingspots WHERE spot_id = ?");
        $deleteOwnerQuery->bind_param("i", $spotIDToDelete);
        if ($deleteOwnerQuery->execute()) {
            echo "<script>alert('Parking spot deleted successfully.'); window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting parking spot.');</script>";
        }
        $deleteOwnerQuery->close();
    } elseif (isset($_POST['delete_complaint'])) {
        $complaintIDToDelete = $_POST['delete_complaint_id'];
        $deleteComplaintQuery = $conn->prepare("DELETE FROM complaints WHERE complaint_id = ?");
        $deleteComplaintQuery->bind_param("i", $complaintIDToDelete);
        if ($deleteComplaintQuery->execute()) {
            echo "<script>alert('Complaint deleted successfully.'); window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting complaint.');</script>";
        }
        $deleteComplaintQuery->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users, Owners, and Complaints</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 90%;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #3498db;
            color: #fff;
        }
        .delete-button {
            padding: 8px 15px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
<div style="position: absolute; top: 10px; right: 10px;">
        <form method="POST" action="logout.php">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>
</div>

<div class="container">
    <h1>Manage Reservations, Parking Spot Owners, and Complaints</h1>

    <h2>Reservations</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th>Spot ID</th>
                <th>Spot Name</th>
                <th>Spot Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reservationsResult->num_rows > 0): ?>
                <?php while ($row = $reservationsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['spot_id']) ?></td>
                        <td><?= htmlspecialchars($row['spot_name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <?php 
                                switch ($row['status']) {
                                    case 0: echo 'Inactive'; break;
                                    case 1: echo 'Available'; break;
                                    case 2: echo 'Reserved'; break;
                                    case 3: echo 'Occupied'; break;
                                    default: echo 'Unknown'; break;
                                }
                            ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_user_id" value="<?= $row['user_id'] ?>">
                                <input type="hidden" name="delete_spot_id" value="<?= $row['spot_id'] ?>">
                                <button type="submit" name="delete_reservation" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No reservations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Parking Spot Owners</h2>
    <table>
        <thead>
            <tr>
                <th>Owner ID</th>
                <th>Owner Name</th>
                <th>Owner Email</th>
                <th>Spot ID</th>
                <th>Spot Name</th>
                <th>Spot Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ownersResult->num_rows > 0): ?>
                <?php while ($row = $ownersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['owner_id']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['spot_id']) ?></td>
                        <td><?= htmlspecialchars($row['spot_name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <?php 
                                switch ($row['status']) {
                                    case 0: echo 'Inactive'; break;
                                    case 1: echo 'Available'; break;
                                    case 2: echo 'Reserved'; break;
                                    case 3: echo 'Occupied'; break;
                                    default: echo 'Unknown'; break;
                                }
                            ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_spot_id" value="<?= $row['spot_id'] ?>">
                                <button type="submit" name="delete_owner" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No parking spots found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Complaints</h2>
    <table>
        <thead>
            <tr>
                <th>Complaint ID</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>Spot Name</th>
                <th>Complaint</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($complaintsResult->num_rows > 0): ?>
                <?php while ($row = $complaintsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['complaint_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['spot_name']) ?></td>
                        <td><?= htmlspecialchars($row['complaint_text']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_complaint_id" value="<?= $row['complaint_id'] ?>">
                                <button type="submit" name="delete_complaint" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No complaints found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
