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

$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }

        .profile {
            width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px gray;
        }

        h2 {
            text-align: center;
            color: green;
        }

        p {
            font-size: 17px;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        a {
            display: block;
            text-align: center;
            background: green;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .edit {
            background: #2196F3;
        }
    </style>
</head>

<body>

<div class="profile">

    <h2>My Profile</h2>

    <p>
        <b>Name:</b>
        <?php echo $user['name']; ?>
    </p>

    <p>
        <b>Email:</b>
        <?php echo $user['email']; ?>
    </p>

    <p>
        <b>Phone:</b>
        <?php echo $user['phone']; ?>
    </p>

    <p>
        <b>Address:</b>
        <?php echo $user['address']; ?>
    </p>

    <a href="edit_profile.php" class="edit">
        Edit Profile
    </a>

    <a href="dashboard.php">
        Back to Dashboard
    </a>

</div>

</body>
</html>