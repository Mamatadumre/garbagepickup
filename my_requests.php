<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM pickup_requests
        WHERE user_id = '$user_id'
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Pickup Requests</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: green;
            color: white;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: green;
        }
    </style>
</head>

<body>

<h2>My Pickup Requests</h2>

<a href="dashboard.php">Back to Dashboard</a>

<table>
    <tr>
        <th>Request ID</th>
        <th>Garbage Type</th>
        <th>Address</th>
        <th>Pickup Date</th>
        <th>Description</th>
        <th>Status</th>
    </tr>

    <?php
    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {
    ?>

    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['garbage_type']; ?></td>
        <td><?php echo $row['address']; ?></td>
        <td><?php echo $row['pickup_date']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>

    <?php
        }

    } else {
        echo "<tr>
                <td colspan='6'>No pickup requests found.</td>
              </tr>";
    }
    ?>

</table>

</body>
</html>