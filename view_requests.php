<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql = "SELECT pickup_requests.*,
        users.name AS user_name,
        users.phone AS user_phone,
        workers.name AS worker_name
        FROM pickup_requests
        JOIN users ON pickup_requests.user_id = users.id
        LEFT JOIN workers ON pickup_requests.worker_id = workers.id
        ORDER BY pickup_requests.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Pickup Requests</title>

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

<h2>All Pickup Requests</h2>

<a href="admin_dashboard.php">Back to Dashboard</a>

<table>

    <tr>
        <th>Request ID</th>
        <th>User Name</th>
        <th>Phone</th>
        <th>Garbage Type</th>
        <th>Address</th>
        <th>Pickup Date</th>
        <th>Worker</th>
        <th>Status</th>
    </tr>

    <?php

    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {

    ?>

    <tr>

        <td><?php echo $row['id']; ?></td>

        <td><?php echo $row['user_name']; ?></td>

        <td><?php echo $row['user_phone']; ?></td>

        <td><?php echo $row['garbage_type']; ?></td>

        <td><?php echo $row['address']; ?></td>

        <td><?php echo $row['pickup_date']; ?></td>

        <td>
            <?php
            if ($row['worker_name']) {
                echo $row['worker_name'];
            } else {
                echo "Not Assigned";
            }
            ?>
        </td>

        <td><?php echo $row['status']; ?></td>

    </tr>

    <?php
        }

    } else {

        echo "<tr>
                <td colspan='8'>No pickup requests found.</td>
              </tr>";

    }

    ?>

</table>

</body>
</html>