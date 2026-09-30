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

if (isset($_POST['assign'])) {
    $request_id = $_POST['request_id'];
    $worker_id = $_POST['worker_id'];

    $sql = "UPDATE pickup_requests 
            SET worker_id = '$worker_id'
            WHERE id = '$request_id'";

    if (mysqli_query($conn, $sql)) {
        $message = "Worker assigned successfully!";
    } else {
        $message = "Error assigning worker.";
    }
}

$requests = mysqli_query($conn, "SELECT * FROM pickup_requests");

$workers = mysqli_query($conn, "SELECT * FROM workers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Worker</title>

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

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
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

        select {
            padding: 7px;
        }

        button {
            background: green;
            color: white;
            border: none;
            padding: 7px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: green;
        }
    </style>
</head>

<body>

<h2>Assign Worker to Pickup Request</h2>

<a href="admin_dashboard.php">Back to Admin Dashboard</a>

<?php
if (isset($message)) {
    echo "<p class='message'>$message</p>";
}
?>

<table>
    <tr>
        <th>Request ID</th>
        <th>User ID</th>
        <th>Garbage Type</th>
        <th>Address</th>
        <th>Pickup Date</th>
        <th>Assign Worker</th>
    </tr>

    <?php while ($request = mysqli_fetch_assoc($requests)) { ?>

    <tr>
        <td><?php echo $request['id']; ?></td>
        <td><?php echo $request['user_id']; ?></td>
        <td><?php echo $request['garbage_type']; ?></td>
        <td><?php echo $request['address']; ?></td>
        <td><?php echo $request['pickup_date']; ?></td>

        <td>
            <form method="POST">
                <input type="hidden" name="request_id"
                       value="<?php echo $request['id']; ?>">

                <select name="worker_id" required>
                    <option value="">Select Worker</option>

                    <?php
                    mysqli_data_seek($workers, 0);

                    while ($worker = mysqli_fetch_assoc($workers)) {
                    ?>

                    <option value="<?php echo $worker['id']; ?>">
                        <?php echo $worker['name']; ?>
                    </option>

                    <?php } ?>
                </select>

                <button type="submit" name="assign">Assign</button>
            </form>
        </td>
    </tr>

    <?php } ?>

</table>

</body>
</html>