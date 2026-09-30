<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
        }

        .header {
            background: green;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .welcome {
            text-align: center;
            margin-bottom: 30px;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            width: 220px;
            padding: 25px;
            background: white;
            text-align: center;
            text-decoration: none;
            color: black;
            border-radius: 10px;
            box-shadow: 0 0 8px gray;
            font-weight: bold;
        }

        .card:hover {
            background: #e8f5e9;
        }

        .logout {
            background: #dc3545;
            color: white;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>User Dashboard</h1>
</div>

<div class="container">

    <div class="welcome">
        <h2>
            Welcome,
            <?php echo $_SESSION['user_name']; ?>!
        </h2>
    </div>

    <div class="cards">

        <a href="pickup_request.php" class="card">
            Request Garbage Pickup
        </a>

        <a href="my_requests.php" class="card">
            My Pickup Requests
        </a>

        <a href="profile.php" class="card">
            My Profile
        </a>

        <a href="logout.php" class="card logout">
            Logout
        </a>

    </div>

</div>

</body>
</html>