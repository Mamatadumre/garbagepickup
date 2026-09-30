<?php
session_start();

if (isset($_SESSION['worker_id'])) {
    header("Location: worker_dashboard.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "Please enter both email and password.";

    } else {

        $stmt = mysqli_prepare($conn,
            "SELECT id, name, email, password 
             FROM workers 
             WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $worker = mysqli_fetch_assoc($result);

            if (password_verify($password, $worker['password'])) {

                $_SESSION['worker_id'] = $worker['id'];
                $_SESSION['worker_name'] = $worker['name'];
                $_SESSION['worker_email'] = $worker['email'];

                header("Location: worker_dashboard.php");
                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Worker account not found.";

        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Worker Login | Garbage Pickup System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f7f6;
        }

        /* LEFT SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 250px;
            height: 100vh;

            background: #075944;
            color: white;

            padding-top: 35px;
        }

        .logo {
            text-align: center;
            margin-bottom: 55px;
        }

        .logo-icon {
            font-size: 50px;
            margin-bottom: 8px;
        }

        .logo h2 {
            font-size: 21px;
            line-height: 1.25;
        }

        .logo p {
            margin-top: 8px;
            font-size: 13px;
            color: #b9eadb;
        }

        .menu {
            padding: 0 20px;
        }

        .menu a {
            display: block;

            text-decoration: none;
            color: white;

            padding: 14px 18px;
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

        /* MAIN AREA */

        .main {
            margin-left: 250px;
            min-height: 100vh;

            display: flex;
            background: white;
        }

        /* LOGIN SECTION */

        .login-section {
            width: 55%;

            padding: 95px 7% 50px 7%;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-icon {
            width: 64px;
            height: 64px;

            background: #e2f3ed;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 32px;

            margin-bottom: 20px;
        }

        .login-section h1 {
            font-size: 34px;
            color: #075944;

            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            font-weight: bold;
            font-size: 14px;

            margin-bottom: 9px;

            color: #333;
        }

        .form-group input {
            width: 100%;

            padding: 14px 15px;

            border: 1px solid #d0d9d6;

            border-radius: 9px;

            font-size: 14px;

            outline: none;
        }

        .form-group input:focus {
            border-color: #07865f;
        }

        .login-btn {
            width: 100%;

            padding: 14px;

            border: none;
            border-radius: 9px;

            background: #07865f;
            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;

            margin-top: 2px;
        }

        .login-btn:hover {
            background: #056f4f;
        }

        .error {
            background: #ffe7e7;
            color: #c62828;

            padding: 11px 14px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .back-home {
            text-align: center;
            margin-top: 22px;
        }

        .back-home a {
            text-decoration: none;
            color: #07865f;
            font-weight: bold;
            font-size: 14px;
        }

        .back-home a:hover {
            text-decoration: underline;
        }

        /* RIGHT SECTION */

        .right-section {
            width: 45%;

            background: linear-gradient(
                135deg,
                #075944,
                #0d7a5b
            );

            color: white;

            position: relative;

            overflow: hidden;

            display: flex;
            flex-direction: column;

            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 40px;
        }

        .circle {
            position: absolute;

            border-radius: 50%;

            background: rgba(255,255,255,0.06);
        }

        .circle.one {
            width: 280px;
            height: 280px;

            top: -80px;
            right: -80px;
        }

        .circle.two {
            width: 200px;
            height: 200px;

            bottom: -100px;
            left: -80px;
        }

        .circle.three {
            width: 100px;
            height: 100px;

            right: 50px;
            bottom: 80px;
        }

        .worker-icon {
            font-size: 80px;

            margin-bottom: 20px;

            position: relative;
            z-index: 2;
        }

        .right-section h2 {
            font-size: 32px;

            margin-bottom: 18px;

            position: relative;
            z-index: 2;
        }

        .right-section p {
            max-width: 430px;

            line-height: 1.7;

            font-size: 15px;

            color: #e1f4ee;

            position: relative;
            z-index: 2;
        }

        .info-box {
            margin-top: 30px;

            width: 85%;

            padding: 18px;

            border: 1px solid rgba(255,255,255,0.25);

            background: rgba(255,255,255,0.08);

            border-radius: 12px;

            position: relative;
            z-index: 2;
        }

        .info-box strong {
            display: block;

            font-size: 16px;

            margin-bottom: 7px;
        }

        .info-box span {
            font-size: 13px;
            color: #d9f0e9;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
            }

            .login-section {
                width: 55%;
                padding: 60px 5%;
            }

            .right-section {
                width: 45%;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                position: relative;

                width: 100%;
                height: auto;

                padding-bottom: 20px;
            }

            .main {
                margin-left: 0;

                display: block;
            }

            .login-section {
                width: 100%;
                padding: 50px 30px;
            }

            .right-section {
                width: 100%;
                min-height: 450px;
            }

        }

    </style>

</head>

<body>

    <!-- LEFT SIDEBAR -->

    <div class="sidebar">

        <div class="logo">

            <div class="logo-icon">♻️</div>

            <h2>
                Garbage Pickup<br>
                System
            </h2>

            <p>
                Clean · Green · Better
            </p>

        </div>


        <div class="menu">

            <a href="../index.php">
                🏠 Home
            </a>

            <a href="../user/login.php">
                👤 User Login
            </a>

            <a href="../admin/admin_login.php">
                🛡️ Admin Login
            </a>

            <a href="worker_login.php" class="active">
                🚚 Worker Login
            </a>

        </div>

    </div>


    <!-- MAIN -->

    <div class="main">

        <!-- LOGIN FORM -->

        <div class="login-section">

            <div class="login-icon">
                🚚
            </div>

            <h1>
                Worker Login
            </h1>

            <p class="subtitle">
                Login to manage your garbage pickup requests
            </p>


            <?php if ($error != "") { ?>

                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php } ?>


            <form method="POST">

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter worker email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="login-btn"
                >
                    🔐 Login as Worker
                </button>

            </form>


            <div class="back-home">

                <a href="../index.php">
                    ← Back to Home
                </a>

            </div>

        </div>


        <!-- RIGHT SIDE -->

        <div class="right-section">

            <div class="circle one"></div>
            <div class="circle two"></div>
            <div class="circle three"></div>


            <div class="worker-icon">
                🚚
            </div>

            <h2>
                Keep the City Clean
            </h2>

            <p>
                Workers help keep our community clean by
                collecting garbage and updating pickup
                requests efficiently.
            </p>


            <div class="info-box">

                <strong>
                    🚛 Worker Panel
                </strong>

                <span>
                    Manage assigned pickup requests
                    and update collection status.
                </span>

            </div>

        </div>

    </div>

</body>
</html>