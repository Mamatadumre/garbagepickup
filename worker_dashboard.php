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

$worker_id = (int) $_SESSION['worker_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email FROM workers WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$worker = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$worker) {
    session_destroy();
    header("Location: worker_login.php");
    exit();
}

/* REQUEST COUNTS */

$total = 0;
$pending = 0;
$collected = 0;
$completed = 0;

$stmt = mysqli_prepare(
    $conn,
    "SELECT 
        COUNT(*) AS total,
        SUM(status = 'Pending') AS pending,
        SUM(status = 'Collected') AS collected,
        SUM(status = 'Completed') AS completed
     FROM pickup_requests
     WHERE worker_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$count = mysqli_fetch_assoc($result);

$total = (int)($count['total'] ?? 0);
$pending = (int)($count['pending'] ?? 0);
$collected = (int)($count['collected'] ?? 0);
$completed = (int)($count['completed'] ?? 0);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Worker Dashboard | Garbage Pickup System</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    background: #f1f6f3;
    min-height: 100vh;
}

/* ================= SIDEBAR ================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #064e3b;
    color: white;
    padding: 30px 20px;
}

.logo {
    text-align: center;
    margin-bottom: 35px;
}

.logo-icon {
    font-size: 45px;
}

.logo h2 {
    font-size: 20px;
    line-height: 1.3;
    margin-top: 8px;
}

.logo p {
    color: #b7e4d0;
    font-size: 12px;
    margin-top: 6px;
}

.menu a {
    display: block;
    text-decoration: none;
    color: #d8eee5;
    padding: 14px 16px;
    margin-bottom: 9px;
    border-radius: 10px;
    transition: 0.3s;
    font-size: 15px;
}

.menu a:hover {
    background: #0b6b50;
    color: white;
}

.menu a.active {
    background: white;
    color: #064e3b;
    font-weight: bold;
}

/* ================= MAIN ================= */

.main {
    margin-left: 250px;
    padding: 35px;
}

/* ================= HEADER ================= */

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h1 {
    color: #064e3b;
    font-size: 30px;
}

.header p {
    color: #777;
    margin-top: 6px;
}

.profile-btn {
    text-decoration: none;
    background: white;
    color: #064e3b;
    padding: 11px 18px;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 3px 12px rgba(0,0,0,0.06);
}

.profile-btn:hover {
    background: #e8f5ef;
}

/* ================= WELCOME ================= */

.welcome {
    background: linear-gradient(135deg, #064e3b, #0b7656);
    color: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
}

.welcome h2 {
    font-size: 25px;
    margin-bottom: 8px;
}

.welcome p {
    color: #d9f3e7;
    font-size: 14px;
}

.welcome-icon {
    position: absolute;
    right: 45px;
    top: 25px;
    font-size: 75px;
    opacity: 0.9;
}

/* ================= STAT CARDS ================= */

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 30px;
}

.card {
    background: white;
    padding: 22px;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #e7eee9;
}

.card-icon {
    font-size: 30px;
    margin-bottom: 12px;
}

.card h3 {
    font-size: 28px;
    color: #064e3b;
    margin-bottom: 5px;
}

.card p {
    color: #777;
    font-size: 14px;
}

/* ================= QUICK ACTION ================= */

.section-title {
    color: #064e3b;
    font-size: 20px;
    margin-bottom: 15px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.action {
    background: white;
    padding: 23px;
    border-radius: 14px;
    text-decoration: none;
    color: #333;
    border: 1px solid #e7eee9;
    transition: 0.3s;
}

.action:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 20px rgba(0,0,0,0.08);
}

.action-icon {
    font-size: 30px;
    margin-bottom: 12px;
}

.action h3 {
    color: #064e3b;
    margin-bottom: 6px;
}

.action p {
    color: #777;
    font-size: 13px;
    line-height: 1.5;
}

/* ================= RESPONSIVE ================= */

@media (max-width: 1000px) {

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .quick-actions {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 750px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 25px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .welcome-icon {
        display: none;
    }

}

</style>

</head>

<body>

<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <div class="logo">

        <div class="logo-icon">♻️</div>

        <h2>
            Garbage Pickup<br>
            System
        </h2>

        <p>Worker Panel</p>

    </div>

    <div class="menu">

        <a href="worker_dashboard.php" class="active">
            🏠 Dashboard
        </a>

        <a href="worker_requests.php">
            📋 My Requests
        </a>

        <a href="worker_profile.php">
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


<!-- ================= MAIN ================= -->

<div class="main">

    <!-- HEADER -->

    <div class="header">

        <div>

            <h1>Worker Dashboard</h1>

            <p>
                Manage your assigned garbage pickup requests
            </p>

        </div>

        <a href="worker_profile.php" class="profile-btn">
            👤 My Profile
        </a>

    </div>


    <!-- WELCOME -->

    <div class="welcome">

        <h2>
            Welcome, <?php echo htmlspecialchars($worker['name']); ?> 👋
        </h2>

        <p>
            Keep your assigned pickup requests updated and help maintain a cleaner city.
        </p>

        <div class="welcome-icon">
            🚛
        </div>

    </div>


    <!-- STAT CARDS -->

    <div class="cards">

        <div class="card">

            <div class="card-icon">
                📋
            </div>

            <h3>
                <?php echo $total; ?>
            </h3>

            <p>Total Requests</p>

        </div>


        <div class="card">

            <div class="card-icon">
                ⏳
            </div>

            <h3>
                <?php echo $pending; ?>
            </h3>

            <p>Pending Requests</p>

        </div>


        <div class="card">

            <div class="card-icon">
                🚛
            </div>

            <h3>
                <?php echo $collected; ?>
            </h3>

            <p>Collected</p>

        </div>


        <div class="card">

            <div class="card-icon">
                ✅
            </div>

            <h3>
                <?php echo $completed; ?>
            </h3>

            <p>Completed</p>

        </div>

    </div>


    <!-- QUICK ACTIONS -->

    <h2 class="section-title">
        Quick Actions
    </h2>

    <div class="quick-actions">

        <a href="worker_requests.php" class="action">

            <div class="action-icon">
                📋
            </div>

            <h3>My Pickup Requests</h3>

            <p>
                View assigned pickup requests and update their status.
            </p>

        </a>


        <a href="worker_profile.php" class="action">

            <div class="action-icon">
                👤
            </div>

            <h3>My Profile</h3>

            <p>
                View your worker account information.
            </p>

        </a>


        <a href="change_password.php" class="action">

            <div class="action-icon">
                🔐
            </div>

            <h3>Change Password</h3>

            <p>
                Change your account password securely.
            </p>

        </a>

    </div>

</div>

</body>

</html>