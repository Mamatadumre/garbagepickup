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

if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE users SET
            name='$name',
            phone='$phone',
            address='$address'
            WHERE id='$user_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['user_name'] = $name;
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile.";
    }
}

$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }

        .box {
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

        input {
            width: 95%;
            padding: 10px;
            margin: 8px 0 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .message {
            text-align: center;
            color: green;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: green;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Edit Profile</h2>

    <?php
    if (isset($message)) {
        echo "<p class='message'>$message</p>";
    }
    ?>

    <form method="POST">

        <label>Name</label>
        <input type="text" name="name"
               value="<?php echo $user['name']; ?>" required>

        <label>Phone</label>
        <input type="text" name="phone"
               value="<?php echo $user['phone']; ?>" required>

        <label>Address</label>
        <input type="text" name="address"
               value="<?php echo $user['address']; ?>" required>

        <button type="submit" name="update">
            Update Profile
        </button>

    </form>

    <a href="profile.php">View Profile</a>
    <a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>