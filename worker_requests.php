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

$message = "";
$message_type = "";


/* =========================
   UPDATE STATUS
   ========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $request_id = isset($_POST['request_id'])
        ? (int)$_POST['request_id']
        : 0;

    $new_status = isset($_POST['new_status'])
        ? $_POST['new_status']
        : "";


    /* Pending → Collected */

    if ($new_status == "Collected") {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pickup_requests
             SET status = 'Collected',
                 started_at = NOW()
             WHERE id = ?
             AND worker_id = ?
             AND status = 'Pending'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $request_id,
            $worker_id
        );

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {

            $message = "Collection started successfully.";
            $message_type = "success";

        } else {

            $message = "This request cannot be updated.";
            $message_type = "error";
        }

        mysqli_stmt_close($stmt);
    }


    /* Collected → Completed */

    elseif ($new_status == "Completed") {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pickup_requests
             SET status = 'Completed',
                 completed_at = NOW()
             WHERE id = ?
             AND worker_id = ?
             AND status = 'Collected'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $request_id,
            $worker_id
        );

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {

            $message = "Work completed successfully.";
            $message_type = "success";

        } else {

            $message = "This request cannot be completed.";
            $message_type = "error";
        }

        mysqli_stmt_close($stmt);
    }


    else {

        $message = "Invalid status update.";
        $message_type = "error";
    }
}


/* =========================
   GET WORKER REQUESTS
   ========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        pickup_requests.id,
        pickup_requests.garbage_type,
        pickup_requests.address,
        pickup_requests.pickup_date,
        pickup_requests.description,
        pickup_requests.status,
        pickup_requests.created_at,
        pickup_requests.started_at,
        pickup_requests.completed_at,
        users.name AS user_name,
        users.phone AS user_phone

     FROM pickup_requests

     INNER JOIN users
        ON pickup_requests.user_id = users.id

     WHERE pickup_requests.worker_id = ?

     ORDER BY pickup_requests.id DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $worker_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Requests - Worker</title>

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
    position: fixed;
    left: 0;
    top: 0;

    width: 250px;
    height: 100vh;

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
    padding: 0 20px;
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


.profile-btn {
    background: white;

    color: #075944;

    text-decoration: none;

    padding: 12px 18px;

    border-radius: 9px;

    font-weight: bold;

    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}


/* ================= MESSAGE ================= */

.message {
    padding: 14px 18px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 14px;

    font-weight: bold;
}


.success {
    background: #dff5e9;
    color: #087443;
    border-left: 5px solid #07865f;
}


.error {
    background: #ffe2e2;
    color: #b42318;
    border-left: 5px solid #d92d20;
}


/* ================= INFO ================= */

.info-box {
    background: #e7f5ef;

    border-left: 5px solid #07865f;

    padding: 16px 18px;

    border-radius: 10px;

    margin-bottom: 25px;

    color: #075944;

    font-size: 13px;

    line-height: 1.6;
}


/* ================= REQUEST GRID ================= */

.requests {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 20px;
}


/* ================= CARD ================= */

.request-card {
    background: white;

    border-radius: 15px;

    padding: 22px;

    box-shadow: 0 5px 18px rgba(0,0,0,0.05);
}


.request-header {
    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    margin-bottom: 18px;

    padding-bottom: 15px;

    border-bottom: 1px solid #e7ecea;
}


.request-number {
    color: #075944;

    font-size: 19px;

    font-weight: bold;
}


.request-number span {
    color: #777;

    font-size: 12px;

    font-weight: normal;
}


/* ================= STATUS ================= */

.status {
    padding: 7px 12px;

    border-radius: 20px;

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


/* ================= DETAILS ================= */

.detail {
    margin-bottom: 13px;
}


.detail label {
    display: block;

    color: #777;

    font-size: 11px;

    margin-bottom: 4px;
}


.detail strong {
    color: #222;

    font-size: 13px;
}


.description {
    background: #f5f9f7;

    padding: 11px;

    border-radius: 8px;

    color: #555;

    font-size: 12px;

    margin-top: 8px;
}


/* ================= TIME ================= */

.time-box {
    background: #f5f9f7;

    border-radius: 9px;

    padding: 12px;

    margin-top: 15px;

    font-size: 11px;

    line-height: 1.7;

    color: #555;
}


.time-box strong {
    color: #075944;
}


/* ================= ACTION ================= */

.action-area {
    margin-top: 18px;

    padding-top: 17px;

    border-top: 1px solid #e7ecea;
}


.action-area form {
    margin: 0;
}


.action-btn {
    width: 100%;

    border: none;

    padding: 12px;

    border-radius: 9px;

    color: white;

    font-size: 13px;

    font-weight: bold;

    cursor: pointer;
}


.start-btn {
    background: #07865f;
}


.start-btn:hover {
    background: #056f4f;
}


.complete-btn {
    background: #1769aa;
}


.complete-btn:hover {
    background: #12578d;
}


.completed-message {
    background: #dff5e9;

    color: #087443;

    text-align: center;

    padding: 12px;

    border-radius: 9px;

    font-size: 13px;

    font-weight: bold;
}


/* ================= EMPTY ================= */

.empty {
    background: white;

    padding: 50px 25px;

    border-radius: 15px;

    text-align: center;

    color: #777;

    box-shadow: 0 5px 18px rgba(0,0,0,0.05);

    grid-column: 1 / -1;
}


.empty-icon {
    font-size: 45px;

    margin-bottom: 12px;
}


/* ================= RESPONSIVE ================= */

@media (max-width: 1000px) {

    .requests {
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


    .topbar {
        display: block;
    }


    .profile-btn {
        display: inline-block;

        margin-top: 15px;
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
            Worker Panel
        </p>

    </div>


    <div class="menu">

        <a href="worker_dashboard.php">
            🏠 Dashboard
        </a>


        <a href="worker_requests.php"
           class="active">
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


    <div class="topbar">

        <div>

            <h1>
                My Pickup Requests
            </h1>

            <p>
                Manage your assigned garbage pickup work
            </p>

        </div>


        <a
            href="worker_profile.php"
            class="profile-btn"
        >
            👤 My Profile
        </a>

    </div>


    <?php if ($message != "") { ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <div class="info-box">

        💡 <strong>How to update your work:</strong><br>

        When you start collecting garbage, click
        <strong>Start Collection</strong>.

        After you finish the work, click
        <strong>Mark as Completed</strong>.

        A completed request cannot be changed back.

    </div>


    <div class="requests">


        <?php if (mysqli_num_rows($result) > 0) { ?>


            <?php while ($request = mysqli_fetch_assoc($result)) { ?>


                <div class="request-card">


                    <!-- HEADER -->

                    <div class="request-header">

                        <div class="request-number">

                            #<?php echo $request['id']; ?>

                            <span>
                                Pickup Request
                            </span>

                        </div>


                        <?php

                        $status_class = "pending";

                        if ($request['status'] == "Collected") {
                            $status_class = "collected";
                        }

                        if ($request['status'] == "Completed") {
                            $status_class = "completed";
                        }

                        ?>


                        <span
                            class="status <?php echo $status_class; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $request['status']
                            );
                            ?>

                        </span>

                    </div>


                    <!-- USER -->

                    <div class="detail">

                        <label>
                            Customer
                        </label>

                        <strong>

                            👤

                            <?php

                            echo htmlspecialchars(
                                $request['user_name']
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- PHONE -->

                    <div class="detail">

                        <label>
                            Phone Number
                        </label>

                        <strong>

                            📞

                            <?php

                            echo htmlspecialchars(
                                $request['user_phone']
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- GARBAGE -->

                    <div class="detail">

                        <label>
                            Garbage Type
                        </label>

                        <strong>

                            🗑️

                            <?php

                            echo htmlspecialchars(
                                $request['garbage_type']
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- ADDRESS -->

                    <div class="detail">

                        <label>
                            Pickup Address
                        </label>

                        <strong>

                            📍

                            <?php

                            echo htmlspecialchars(
                                $request['address']
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- DATE -->

                    <div class="detail">

                        <label>
                            Pickup Date
                        </label>

                        <strong>

                            📅

                            <?php

                            echo htmlspecialchars(
                                $request['pickup_date']
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- DESCRIPTION -->

                    <?php if (
                        !empty($request['description'])
                    ) { ?>

                        <div class="description">

                            <strong>
                                Description:
                            </strong>

                            <br>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $request['description']
                                )
                            );

                            ?>

                        </div>

                    <?php } ?>


                    <!-- TIME -->

                    <?php if (
                        !empty($request['started_at'])
                        ||
                        !empty($request['completed_at'])
                    ) { ?>

                        <div class="time-box">

                            <?php if (
                                !empty($request['started_at'])
                            ) { ?>

                                🕐 Started:

                                <strong>

                                    <?php

                                    echo date(
                                        "Y-m-d h:i A",
                                        strtotime(
                                            $request['started_at']
                                        )
                                    );

                                    ?>

                                </strong>

                                <br>

                            <?php } ?>


                            <?php if (
                                !empty($request['completed_at'])
                            ) { ?>

                                ✅ Completed:

                                <strong>

                                    <?php

                                    echo date(
                                        "Y-m-d h:i A",
                                        strtotime(
                                            $request['completed_at']
                                        )
                                    );

                                    ?>

                                </strong>

                            <?php } ?>

                        </div>

                    <?php } ?>


                    <!-- ACTION -->

                    <div class="action-area">


                        <?php if (
                            $request['status']
                            == "Pending"
                        ) { ?>


                            <form
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to start this collection?');"
                            >

                                <input
                                    type="hidden"
                                    name="request_id"
                                    value="<?php
                                    echo $request['id'];
                                    ?>"
                                >


                                <input
                                    type="hidden"
                                    name="new_status"
                                    value="Collected"
                                >


                                <button
                                    type="submit"
                                    class="action-btn start-btn"
                                >

                                    🚚 Start Collection

                                </button>

                            </form>


                        <?php } elseif (
                            $request['status']
                            == "Collected"
                        ) { ?>


                            <form
                                method="POST"
                                onsubmit="return confirm('Have you finished this pickup work?');"
                            >

                                <input
                                    type="hidden"
                                    name="request_id"
                                    value="<?php
                                    echo $request['id'];
                                    ?>"
                                >


                                <input
                                    type="hidden"
                                    name="new_status"
                                    value="Completed"
                                >


                                <button
                                    type="submit"
                                    class="action-btn complete-btn"
                                >

                                    ✅ Mark as Completed

                                </button>

                            </form>


                        <?php } else { ?>


                            <div class="completed-message">

                                ✅ Work Completed Successfully

                            </div>


                        <?php } ?>


                    </div>


                </div>


            <?php } ?>


        <?php } else { ?>


            <div class="empty">

                <div class="empty-icon">
                    📋
                </div>

                <h3>
                    No Pickup Requests
                </h3>

                <p>
                    No pickup requests have been assigned to you yet.
                </p>

            </div>


        <?php } ?>


    </div>


</div>


</body>

</html>

<?php

mysqli_stmt_close($stmt);

mysqli_close($conn);

?>