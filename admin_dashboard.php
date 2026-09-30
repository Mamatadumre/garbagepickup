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


/* =========================
   BASIC COUNTS
   ========================= */

$user_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$user_data = mysqli_fetch_assoc($user_result);
$total_users = (int)$user_data['total'];


$worker_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM workers");
$worker_data = mysqli_fetch_assoc($worker_result);
$total_workers = (int)$worker_data['total'];


$request_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pickup_requests");
$request_data = mysqli_fetch_assoc($request_result);
$total_requests = (int)$request_data['total'];


$pending_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pickup_requests
     WHERE status = 'Pending'"
);

$pending_data = mysqli_fetch_assoc($pending_result);
$total_pending = (int)$pending_data['total'];


$collected_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pickup_requests
     WHERE status = 'Collected'"
);

$collected_data = mysqli_fetch_assoc($collected_result);
$total_collected = (int)$collected_data['total'];


$completed_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pickup_requests
     WHERE status = 'Completed'"
);

$completed_data = mysqli_fetch_assoc($completed_result);
$total_completed = (int)$completed_data['total'];


/* =========================
   WORKERS
   ========================= */

$workers_query = mysqli_query(
    $conn,
    "SELECT id, name, email, phone
     FROM workers
     ORDER BY name ASC"
);


/* =========================
   PICKUP REQUESTS
   ========================= */

$requests_query = mysqli_query(
    $conn,
    "SELECT
        pickup_requests.id,
        pickup_requests.garbage_type,
        pickup_requests.address,
        pickup_requests.pickup_date,
        pickup_requests.status,
        pickup_requests.started_at,
        pickup_requests.completed_at,
        users.name AS user_name,
        workers.name AS worker_name,

        CASE
            WHEN pickup_requests.started_at IS NOT NULL
                 AND pickup_requests.completed_at IS NOT NULL
            THEN TIMESTAMPDIFF(
                MINUTE,
                pickup_requests.started_at,
                pickup_requests.completed_at
            )

            WHEN pickup_requests.started_at IS NOT NULL
                 AND pickup_requests.status = 'Collected'
            THEN TIMESTAMPDIFF(
                MINUTE,
                pickup_requests.started_at,
                NOW()
            )

            ELSE NULL
        END AS work_minutes

     FROM pickup_requests

     LEFT JOIN users
        ON pickup_requests.user_id = users.id

     LEFT JOIN workers
        ON pickup_requests.worker_id = workers.id

     ORDER BY pickup_requests.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - Garbage Pickup System</title>


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


        /* ================= SIDEBAR ================= */

        .sidebar {
            width: 250px;
            height: 100vh;

            position: fixed;
            left: 0;
            top: 0;

            background: #075944;
            color: white;

            padding-top: 30px;
        }


        .logo {
            text-align: center;
            margin-bottom: 40px;
        }


        .logo-icon {
            font-size: 45px;
            margin-bottom: 8px;
        }


        .logo h2 {
            font-size: 21px;
            line-height: 1.2;
        }


        .logo p {
            color: #bdebdc;
            font-size: 13px;
            margin-top: 7px;
        }


        .menu {
            padding: 0 18px;
        }


        .menu a {
            display: block;

            color: white;
            text-decoration: none;

            padding: 13px 15px;

            margin-bottom: 7px;

            border-radius: 9px;

            font-size: 14px;
        }


        .menu a:hover {
            background: rgba(255,255,255,0.12);
        }


        .menu a.active {
            background: white;
            color: #075944;
            font-weight: bold;
        }


        /* ================= MAIN ================= */

        .main {
            margin-left: 250px;
            padding: 30px;

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


        .admin-name {
            background: white;

            padding: 11px 16px;

            border-radius: 10px;

            color: #075944;

            font-weight: bold;

            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }


        /* ================= STAT CARDS ================= */

        .stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 18px;

            margin-bottom: 30px;
        }


        .stat-card {
            background: white;

            padding: 22px;

            border-radius: 14px;

            box-shadow: 0 5px 18px rgba(0,0,0,0.05);
        }


        .stat-card .icon {
            font-size: 28px;
            margin-bottom: 10px;
        }


        .stat-card h3 {
            color: #777;

            font-size: 13px;

            font-weight: normal;
        }


        .stat-card .number {
            color: #075944;

            font-size: 28px;

            font-weight: bold;

            margin-top: 5px;
        }


        /* ================= SECTION ================= */

        .section {
            margin-bottom: 30px;
        }


        .section-title {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 15px;
        }


        .section-title h2 {
            color: #075944;
            font-size: 21px;
        }


        .section-title p {
            color: #777;
            font-size: 13px;
            margin-top: 4px;
        }


        /* ================= WORKER CARDS ================= */

        .workers {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }


        .worker-card {
            background: white;

            border-radius: 15px;

            padding: 22px;

            box-shadow: 0 5px 18px rgba(0,0,0,0.05);
        }


        .worker-header {
            display: flex;

            align-items: center;

            gap: 15px;

            padding-bottom: 17px;

            border-bottom: 1px solid #e5ebe8;

            margin-bottom: 18px;
        }


        .worker-icon {
            width: 55px;
            height: 55px;

            border-radius: 50%;

            background: #dff4ec;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 29px;
        }


        .worker-header h3 {
            color: #075944;

            font-size: 18px;

            margin-bottom: 4px;
        }


        .worker-header p {
            color: #777;
            font-size: 12px;
        }


        .worker-stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 8px;
        }


        .worker-stat {
            background: #f5f9f7;

            border-radius: 8px;

            padding: 10px 5px;

            text-align: center;
        }


        .worker-stat strong {
            display: block;

            color: #075944;

            font-size: 19px;
        }


        .worker-stat span {
            color: #777;

            font-size: 10px;
        }


        /* ================= PROGRESS ================= */

        .progress-area {
            margin-top: 18px;
        }


        .progress-top {
            display: flex;

            justify-content: space-between;

            margin-bottom: 7px;

            font-size: 12px;

            color: #555;
        }


        .progress-bar {
            width: 100%;

            height: 9px;

            background: #e6ece9;

            border-radius: 10px;

            overflow: hidden;
        }


        .progress-fill {
            height: 100%;

            background: #07865f;

            border-radius: 10px;
        }


        .remaining {
            margin-top: 10px;

            font-size: 12px;

            color: #777;
        }


        /* ================= TABLE ================= */

        .table-container {
            background: white;

            border-radius: 15px;

            padding: 20px;

            box-shadow: 0 5px 18px rgba(0,0,0,0.05);

            overflow-x: auto;
        }


        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 1100px;
        }


        th {
            background: #075944;

            color: white;

            padding: 13px 10px;

            text-align: left;

            font-size: 12px;
        }


        td {
            padding: 13px 10px;

            border-bottom: 1px solid #e8eeeb;

            font-size: 12px;

            color: #333;

            vertical-align: middle;
        }


        tr:hover {
            background: #f6faf8;
        }


        /* ================= STATUS ================= */

        .status {
            display: inline-block;

            padding: 6px 10px;

            border-radius: 15px;

            font-size: 11px;

            font-weight: bold;
        }


        .pending {
            background: #fff3cd;
            color: #946c00;
        }


        .collected {
            background: #dceeff;
            color: #1769aa;
        }


        .completed {
            background: #dff5e9;
            color: #087443;
        }


        /* ================= ACTION ================= */

        .action {
            display: inline-block;

            padding: 6px 10px;

            border-radius: 15px;

            font-size: 11px;

            font-weight: bold;

            white-space: nowrap;
        }


        .action-waiting {
            background: #fff3cd;
            color: #946c00;
        }


        .action-working {
            background: #dceeff;
            color: #1769aa;
        }


        .action-completed {
            background: #dff5e9;
            color: #087443;
        }


        /* ================= WORKER ================= */

        .not-assigned {
            color: #c62828;

            font-weight: bold;
        }


        /* ================= TIME ================= */

        .time-text {
            font-size: 11px;

            color: #666;

            line-height: 1.4;
        }


        .work-time {
            font-weight: bold;

            color: #075944;

            white-space: nowrap;
        }


        .running-time {
            font-weight: bold;

            color: #1769aa;

            white-space: nowrap;
        }


        /* ================= EMPTY ================= */

        .empty {
            background: white;

            padding: 35px;

            border-radius: 14px;

            text-align: center;

            color: #777;
        }


        /* ================= QUICK LINKS ================= */

        .quick-links {
            display: flex;

            gap: 12px;

            flex-wrap: wrap;

            margin-top: 10px;
        }


        .quick-links a {
            text-decoration: none;

            background: #07865f;

            color: white;

            padding: 11px 16px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: bold;
        }


        .quick-links a:hover {
            background: #056f4f;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }


            .workers {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 750px) {

            .sidebar {
                width: 210px;
            }


            .main {
                margin-left: 210px;
                padding: 20px;
            }


            .stats {
                grid-template-columns: 1fr;
            }


            .topbar {
                display: block;
            }


            .admin-name {
                display: inline-block;

                margin-top: 12px;
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


<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            ♻️
        </div>


        <h2>
            Garbage Pickup<br>
            System
        </h2>


        <p>
            Admin Panel
        </p>

    </div>


    <div class="menu">

        <a
            href="admin_dashboard.php"
            class="active"
        >
            🏠 Dashboard
        </a>


        <a href="view_users.php">
            👥 Manage Users
        </a>


        <a href="view_requests.php">
            📋 Pickup Requests
        </a>


        <a href="manage_workers.php">
            👷 Manage Workers
        </a>


        <a href="assign_worker.php">
            🚚 Assign Worker
        </a>


        <a href="logout.php">
            🚪 Logout
        </a>

    </div>

</div>


<!-- ================= MAIN ================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>
                Admin Dashboard
            </h1>


            <p>
                Monitor users, workers and garbage pickup activities
            </p>

        </div>


        <div class="admin-name">

            👤

            <?php

            if (isset($_SESSION['admin_name'])) {

                echo htmlspecialchars($_SESSION['admin_name']);

            } else {

                echo "Administrator";

            }

            ?>

        </div>

    </div>


    <!-- ================= STATISTICS ================= -->

    <div class="stats">


        <div class="stat-card">

            <div class="icon">
                👥
            </div>


            <h3>
                Total Users
            </h3>


            <div class="number">
                <?php echo $total_users; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                👷
            </div>


            <h3>
                Total Workers
            </h3>


            <div class="number">
                <?php echo $total_workers; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                📋
            </div>


            <h3>
                Total Requests
            </h3>


            <div class="number">
                <?php echo $total_requests; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                ⏳
            </div>


            <h3>
                Pending Work
            </h3>


            <div class="number">
                <?php echo $total_pending; ?>
            </div>

        </div>

    </div>


    <!-- ================= WORKER MONITORING ================= -->

    <div class="section">

        <div class="section-title">

            <div>

                <h2>
                    👷 Worker Monitoring
                </h2>


                <p>
                    Track each worker's assigned work and progress
                </p>

            </div>

        </div>


        <div class="workers">


            <?php if (mysqli_num_rows($workers_query) > 0) { ?>


                <?php while ($worker = mysqli_fetch_assoc($workers_query)) { ?>


                    <?php

                    $worker_id = (int)$worker['id'];


                    $count_query = mysqli_query(
                        $conn,
                        "SELECT
                            COUNT(*) AS total,

                            SUM(
                                CASE
                                    WHEN status = 'Pending'
                                    THEN 1
                                    ELSE 0
                                END
                            ) AS pending,

                            SUM(
                                CASE
                                    WHEN status = 'Collected'
                                    THEN 1
                                    ELSE 0
                                END
                            ) AS collected,

                            SUM(
                                CASE
                                    WHEN status = 'Completed'
                                    THEN 1
                                    ELSE 0
                                END
                            ) AS completed

                         FROM pickup_requests

                         WHERE worker_id = $worker_id"
                    );


                    $counts = mysqli_fetch_assoc($count_query);


                    $total = (int)($counts['total'] ?? 0);

                    $pending = (int)($counts['pending'] ?? 0);

                    $collected = (int)($counts['collected'] ?? 0);

                    $completed = (int)($counts['completed'] ?? 0);


                    if ($total > 0) {

                        $progress = round(
                            ($completed / $total) * 100
                        );

                    } else {

                        $progress = 0;

                    }


                    $remaining = $pending + $collected;

                    ?>


                    <div class="worker-card">


                        <div class="worker-header">


                            <div class="worker-icon">
                                👷
                            </div>


                            <div>

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $worker['name']
                                    );
                                    ?>
                                </h3>


                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $worker['email']
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>


                        <div class="worker-stats">


                            <div class="worker-stat">

                                <strong>
                                    <?php echo $total; ?>
                                </strong>

                                <span>
                                    Total
                                </span>

                            </div>


                            <div class="worker-stat">

                                <strong>
                                    <?php echo $pending; ?>
                                </strong>

                                <span>
                                    Pending
                                </span>

                            </div>


                            <div class="worker-stat">

                                <strong>
                                    <?php echo $collected; ?>
                                </strong>

                                <span>
                                    Collected
                                </span>

                            </div>


                            <div class="worker-stat">

                                <strong>
                                    <?php echo $completed; ?>
                                </strong>

                                <span>
                                    Completed
                                </span>

                            </div>

                        </div>


                        <div class="progress-area">


                            <div class="progress-top">

                                <span>
                                    Completion Progress
                                </span>


                                <strong>
                                    <?php echo $progress; ?>%
                                </strong>

                            </div>


                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: <?php echo $progress; ?>%;"
                                ></div>

                            </div>


                            <div class="remaining">

                                ⏳ Remaining work:

                                <strong>
                                    <?php echo $remaining; ?>
                                </strong>

                                request(s)

                            </div>

                        </div>


                    </div>


                <?php } ?>


            <?php } else { ?>


                <div class="empty">

                    👷 No workers found.

                </div>


            <?php } ?>


        </div>

    </div>


    <!-- ================= PICKUP WORK MONITORING ================= -->

    <div class="section">


        <div class="section-title">

            <div>

                <h2>
                    📊 Pickup Work Monitoring
                </h2>


                <p>
                    See worker status, action and work time
                </p>

            </div>

        </div>


        <div class="table-container">


            <?php if (mysqli_num_rows($requests_query) > 0) { ?>


                <table>


                    <thead>

                        <tr>

                            <th>
                                Request
                            </th>


                            <th>
                                User
                            </th>


                            <th>
                                Worker
                            </th>


                            <th>
                                Garbage Type
                            </th>


                            <th>
                                Pickup Date
                            </th>


                            <th>
                                Status
                            </th>


                            <th>
                                Action
                            </th>


                            <th>
                                Started
                            </th>


                            <th>
                                Completed
                            </th>


                            <th>
                                Work Time
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while ($request = mysqli_fetch_assoc($requests_query)) { ?>


                            <tr>


                                <!-- REQUEST -->

                                <td>

                                    #<?php
                                    echo $request['id'];
                                    ?>

                                </td>


                                <!-- USER -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $request['user_name']
                                        ?? 'Unknown'
                                    );

                                    ?>

                                </td>


                                <!-- WORKER -->

                                <td>

                                    <?php if (!empty($request['worker_name'])) { ?>

                                        <?php

                                        echo htmlspecialchars(
                                            $request['worker_name']
                                        );

                                        ?>

                                    <?php } else { ?>

                                        <span class="not-assigned">
                                            Not Assigned
                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- GARBAGE TYPE -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $request['garbage_type']
                                    );

                                    ?>

                                </td>


                                <!-- PICKUP DATE -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $request['pickup_date']
                                    );

                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td>


                                    <?php

                                    $status_class = "pending";


                                    if (
                                        $request['status']
                                        == "Collected"
                                    ) {

                                        $status_class = "collected";

                                    }


                                    if (
                                        $request['status']
                                        == "Completed"
                                    ) {

                                        $status_class = "completed";

                                    }

                                    ?>


                                    <span
                                        class="status
                                        <?php
                                        echo $status_class;
                                        ?>"
                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            $request['status']
                                        );

                                        ?>

                                    </span>


                                </td>


                                <!-- ACTION -->

                                <td>


                                    <?php if (
                                        $request['status']
                                        == "Pending"
                                    ) { ?>


                                        <span
                                            class="action
                                            action-waiting"
                                        >

                                            ⏳ Waiting for Worker

                                        </span>


                                    <?php } elseif (
                                        $request['status']
                                        == "Collected"
                                    ) { ?>


                                        <span
                                            class="action
                                            action-working"
                                        >

                                            🚚 Worker is Working

                                        </span>


                                    <?php } elseif (
                                        $request['status']
                                        == "Completed"
                                    ) { ?>


                                        <span
                                            class="action
                                            action-completed"
                                        >

                                            ✅ Work Completed

                                        </span>


                                    <?php } ?>


                                </td>


                                <!-- STARTED -->

                                <td>


                                    <?php

                                    if (
                                        !empty(
                                            $request['started_at']
                                        )
                                    ) {

                                        echo '<span class="time-text">'
                                            . date(
                                                "Y-m-d h:i A",
                                                strtotime(
                                                    $request['started_at']
                                                )
                                            )
                                            . '</span>';

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                                <!-- COMPLETED -->

                                <td>


                                    <?php

                                    if (
                                        !empty(
                                            $request['completed_at']
                                        )
                                    ) {

                                        echo '<span class="time-text">'
                                            . date(
                                                "Y-m-d h:i A",
                                                strtotime(
                                                    $request['completed_at']
                                                )
                                            )
                                            . '</span>';

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                                <!-- WORK TIME -->

                                <td>


                                    <?php

                                    if (
                                        $request['work_minutes']
                                        !== null
                                    ) {


                                        $minutes = (int)
                                            $request['work_minutes'];


                                        if (
                                            $minutes < 0
                                        ) {

                                            $minutes = 0;

                                        }


                                        $hours = floor(
                                            $minutes / 60
                                        );


                                        $remaining_minutes =
                                            $minutes % 60;


                                        if (
                                            $request['status']
                                            == "Completed"
                                        ) {


                                            echo '<span class="work-time">';

                                            echo $hours
                                                . "h "
                                                . $remaining_minutes
                                                . "m";


                                            echo '</span>';


                                        } else {


                                            echo '<span class="running-time">';

                                            echo $hours
                                                . "h "
                                                . $remaining_minutes
                                                . "m running";


                                            echo '</span>';

                                        }


                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </td>


                            </tr>


                        <?php } ?>


                    </tbody>

                </table>


            <?php } else { ?>


                <div class="empty">

                    📋 No pickup requests found.

                </div>


            <?php } ?>


        </div>

    </div>


    <!-- ================= QUICK ACTIONS ================= -->

    <div class="section">


        <div class="section-title">

            <h2>
                Quick Actions
            </h2>

        </div>


        <div class="quick-links">


            <a href="manage_workers.php">
                👷 Manage Workers
            </a>


            <a href="assign_worker.php">
                🚚 Assign Worker
            </a>


            <a href="view_requests.php">
                📋 View Requests
            </a>


            <a href="view_users.php">
                👥 View Users
            </a>


        </div>

    </div>


</div>


</body>

</html>

<?php

mysqli_close($conn);

?>