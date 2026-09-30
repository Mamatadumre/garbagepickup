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

/* Add Worker */
if (isset($_POST['add_worker'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query(
        $conn,
        "SELECT id FROM workers WHERE email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists!";
    } else {

        $sql = "INSERT INTO workers
                (name, email, phone, address, password)
                VALUES
                ('$name', '$email', '$phone', '$address', '$password')";

        if (mysqli_query($conn, $sql)) {
            $message = "Worker added successfully!";
        } else {
            $message = "Error adding worker: " . mysqli_error($conn);
        }
    }
}

/* Delete Worker */
if (isset($_GET['delete'])) {

    $worker_id = $_GET['delete'];

    $sql = "DELETE FROM workers WHERE id='$worker_id'";

    if (mysqli_query($conn, $sql)) {
        $message = "Worker deleted successfully!";
    } else {
        $message = "Error deleting worker.";
    }
}

/* Get Workers */
$result = mysqli_query($conn, "SELECT * FROM workers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Workers</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: green;
        }

        .box {
            width: 450px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px gray;
        }

        input {
            width: 95%;
            padding: 10px;
            margin: 7px 0 12px;
        }

        button {
            background: green;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 30px;
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

        .delete {
            background: #dc3545;
            color: white;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .back {
            display: inline-block;
            margin-bottom: 10px;
            color: green;
        }

    </style>

</head>

<body>

<h2>Manage Workers</h2>

<a class="back" href="admin_dashboard.php">
    Back to Dashboard
</a>

<?php

if (isset($message)) {
    echo "<p class='message'>$message</p>";
}

?>

<div class="box">

    <h3>Add New Worker</h3>

    <form method="POST">

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="text" name="phone" required>

        <label>Address</label>
        <input type="text" name="address" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="add_worker">
            Add Worker
        </button>

    </form>

</div>


<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Action</th>
    </tr>

<?php

if (mysqli_num_rows($result) > 0) {

    while ($worker = mysqli_fetch_assoc($result)) {

?>

    <tr>

        <td><?php echo $worker['id']; ?></td>

        <td><?php echo $worker['name']; ?></td>

        <td><?php echo $worker['email']; ?></td>

        <td><?php echo $worker['phone']; ?></td>

        <td><?php echo $worker['address']; ?></td>

        <td>

            <a
                class="delete"
                href="manage_workers.php?delete=<?php echo $worker['id']; ?>"
                onclick="return confirm('Are you sure you want to delete this worker?');"
            >
                Delete
            </a>

        </td>

    </tr>

<?php

    }

} else {

    echo "<tr>
            <td colspan='6'>No workers found.</td>
          </tr>";

}

?>

</table>

</body>
</html>