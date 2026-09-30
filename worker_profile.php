<?php
session_start();

if (!isset($_SESSION['worker_id'])) {
    header("Location: worker_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$worker_id = $_SESSION['worker_id'];

$stmt = mysqli_prepare($conn, 
    "SELECT id, name, email, phone, address, created_at 
     FROM workers 
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$worker = mysqli_fetch_assoc($result);

if (!$worker) {
    session_destroy();
    header("Location: worker_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Worker</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f1f6f4;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #075944;
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            padding-top: 35px;
        }

        .logo {
            text-align: center;
            margin-bottom: 45px;
        }

        .logo-icon {
            font-size: 48px;
            margin-bottom: 8px;
        }

        .logo h2 {
            font-size: 21px;
            line-height: 1.2;
        }

        .logo p {
            font-size: 13px;
            color: #bdebdc;
            margin-top: 7px;
        }

        .menu {
            padding: 0 20px;
        }

        .menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 14px 17px;
            margin-bottom: 8px;
            border-radius: 10px;
            font-size: 15px;
        }

        .menu a:hover {
            background: rgba(255,255,255,0.12);
        }

        .menu a.active {
            background: white;
            color: #075944;
            font-weight: bold;
        }

        /* MAIN */
        .main {
            margin-left: 250px;
            padding: 35px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar h1 {
            color: #075944;
            font-size: 28px;
        }

        .topbar p {
            color: #777;
            margin-top: 5px;
        }

        .profile-container {
            background: white;
            border-radius: 18px;
            padding: 30px;
            max-width: 1000px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 25px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 25px;
        }

        .profile-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #dff4ec;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 43px;
        }

        .profile-header h2 {
            color: #075944;
            margin-bottom: 5px;
        }

        .profile-header p {
            color: #777;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-box {
            background: #f6faf8;
            border: 1px solid #dce9e4;
            border-radius: 12px;
            padding: 18px;
        }

        .info-box.full {
            grid-column: 1 / 3;
        }

        .label {
            color: #718078;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .value {
            color: #222;
            font-size: 16px;
            font-weight: bold;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 12px;
        }

        .btn {
            text-decoration: none;
            padding: 13px 22px;
            border-radius: 9px;
            font-weight: bold;
            display: inline-block;
        }

        .change-btn {
            background: #07865f;
            color: white;
        }

        .change-btn:hover {
            background: #056f4f;
        }

        .logout-btn {
            background: #ffe1e1;
            color: #c62828;
        }

        .logout-btn:hover {
            background: #ffd0d0;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                padding: 20px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .info-box.full {
                grid-column: auto;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding-bottom: 20px;
            }

            .main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

<!-- LEFT SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        <div class="logo-icon">♻️</div>
        <h2>Garbage Pickup<br>System</h2>
        <p>Worker Panel</p>
    </div>

    <div class="menu">

        <a href="worker_dashboard.php">
            🏠 Dashboard
        </a>

        <a href="worker_requests.php">
            📋 My Requests
        </a>

        <a href="worker_profile.php" class="active">
            👤 My Profile
        </a>

        <a href="change_password.php">
            🔐 Change Password
        </a>

        <a href="logout.php">
            🚪 Logout
        </a>

    </div>

</div>


<!-- MAIN CONTENT -->
<div class="main">

    <div class="topbar">
        <div>
            <h1>My Profile</h1>
            <p>View your worker account information</p>
        </div>
    </div>

    <div class="profile-container">

        <div class="profile-header">

            <div class="profile-icon">
                👨‍🔧
            </div>

            <div>
                <h2><?php echo htmlspecialchars($worker['name']); ?></h2>
                <p>Garbage Collection Worker</p>
            </div>

        </div>


        <div class="details">

            <div class="info-box">
                <div class="label">Worker ID</div>
                <div class="value">
                    #<?php echo htmlspecialchars($worker['id']); ?>
                </div>
            </div>


            <div class="info-box">
                <div class="label">Full Name</div>
                <div class="value">
                    <?php echo htmlspecialchars($worker['name']); ?>
                </div>
            </div>


            <div class="info-box">
                <div class="label">Email Address</div>
                <div class="value">
                    <?php echo htmlspecialchars($worker['email']); ?>
                </div>
            </div>


            <div class="info-box">
                <div class="label">Phone Number</div>
                <div class="value">
                    <?php echo htmlspecialchars($worker['phone']); ?>
                </div>
            </div>


            <div class="info-box full">
                <div class="label">Address</div>
                <div class="value">
                    <?php echo htmlspecialchars($worker['address']); ?>
                </div>
            </div>


            <div class="info-box full">
                <div class="label">Account Created</div>
                <div class="value">
                    <?php echo htmlspecialchars($worker['created_at']); ?>
                </div>
            </div>

        </div>


        <div class="buttons">

            <a href="change_password.php" class="btn change-btn">
                🔐 Change Password
            </a>

            <a href="logout.php" class="btn logout-btn">
                🚪 Logout
            </a>

        </div>

    </div>

</div>

</body>
</html>