<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Incorrect password.";

            }

        } else {

            $message = "Account not found.";

        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>User Login - Garbage Pickup System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f1f8f4;
        }

        /* MAIN */

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 270px;

            background: linear-gradient(
                180deg,
                #075e3b,
                #06452d
            );

            color: white;

            padding: 30px 18px;
        }

        .logo {
            text-align: center;

            margin-bottom: 45px;
        }

        .logo-icon {
            font-size: 45px;

            margin-bottom: 8px;
        }

        .logo h2 {
            font-size: 21px;

            line-height: 1.3;
        }

        .menu a {
            display: flex;

            align-items: center;

            gap: 15px;

            color: white;

            text-decoration: none;

            padding: 15px 18px;

            margin-bottom: 10px;

            border-radius: 10px;

            font-size: 16px;

            transition: 0.3s;
        }

        .menu a:hover {
            background: rgba(255,255,255,0.15);
        }

        .menu a.active {
            background: rgba(255,255,255,0.22);

            font-weight: bold;
        }

        .menu-icon {
            font-size: 21px;

            width: 28px;

            text-align: center;
        }

        /* CONTENT */

        .content {
            flex: 1;

            padding: 30px 40px;
        }

        .topbar {
            display: flex;

            justify-content: flex-end;

            align-items: center;

            color: #166534;

            font-weight: bold;

            font-size: 16px;

            margin-bottom: 20px;
        }

        .topbar span {
            margin: 0 12px;

            color: #999;
        }

        /* LOGIN AREA */

        .login-area {
            display: flex;

            align-items: center;

            justify-content: center;

            gap: 55px;

            max-width: 1150px;

            margin: 50px auto;
        }

        /* LOGIN BOX */

        .login-box {
            background: white;

            width: 500px;

            padding: 40px;

            border-radius: 15px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.10);
        }

        .login-title {
            display: flex;

            align-items: center;

            gap: 15px;

            color: #075e3b;

            margin-bottom: 10px;
        }

        .login-title .icon {
            font-size: 42px;
        }

        .login-title h1 {
            font-size: 30px;
        }

        .welcome {
            color: #64748b;

            margin-bottom: 30px;

            font-size: 16px;
        }

        /* ERROR MESSAGE */

        .message {
            background: #fee2e2;

            color: #b91c1c;

            padding: 12px;

            border-radius: 7px;

            text-align: center;

            margin-bottom: 20px;
        }

        /* INPUT */

        .input-box {
            margin-bottom: 22px;
        }

        label {
            display: block;

            font-weight: bold;

            color: #1e293b;

            margin-bottom: 8px;
        }

        .required {
            color: red;
        }

        input {
            width: 100%;

            padding: 14px;

            border: 1px solid #cbd5e1;

            border-radius: 8px;

            font-size: 15px;
        }

        input:focus {
            outline: none;

            border-color: #198754;

            box-shadow:
                0 0 0 2px rgba(25,135,84,0.10);
        }

        /* LOGIN BUTTON */

        .login-button {
            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background: #078c4f;

            color: white;

            font-size: 17px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }

        .login-button:hover {
            background: #056d3d;
        }

        /* REGISTER */

        .register-text {
            text-align: center;

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #e2e8f0;

            color: #475569;
        }

        .register-text a {
            color: #078c4f;

            font-weight: bold;

            text-decoration: none;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        /* BACK HOME */

        .back-home {
            text-align: center;

            margin-top: 18px;
        }

        .back-home a {
            color: #078c4f;

            text-decoration: none;

            font-weight: bold;
        }

        .back-home a:hover {
            text-decoration: underline;
        }

        /* RIGHT DESIGN */

        .illustration {
            width: 420px;

            text-align: center;
        }

        .illustration h2 {
            color: #087443;

            font-size: 28px;

            margin-bottom: 5px;
        }

        .illustration h3 {
            color: #078c4f;

            font-size: 38px;

            margin-bottom: 12px;
        }

        .illustration p {
            color: #475569;

            margin-bottom: 25px;
        }

        .scene {
            background: #dff5e8;

            border-radius: 25px;

            padding: 35px 20px;

            min-height: 270px;

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 15px;

            overflow: hidden;
        }

        .tree {
            font-size: 65px;
        }

        .truck {
            font-size: 105px;
        }

        /* TABLET */

        @media (max-width: 1000px) {

            .login-area {
                flex-direction: column;
            }

            .illustration {
                display: none;
            }
        }

        /* MOBILE */

        @media (max-width: 700px) {

            .main-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;

                padding: 20px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .menu {
                display: flex;

                justify-content: center;

                flex-wrap: wrap;

                gap: 5px;
            }

            .menu a {
                margin-bottom: 0;

                padding: 10px 12px;
            }

            .content {
                padding: 20px;
            }

            .login-box {
                width: 100%;

                padding: 25px;
            }
        }

    </style>

</head>

<body>

<div class="main-container">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">

            <div class="logo-icon">
                ♻
            </div>

            <h2>
                Garbage Pickup System
            </h2>

        </div>


        <div class="menu">

            <!-- HOME -->

            <a href="../index.php">

                <span class="menu-icon">
                    🏠
                </span>

                <span>
                    Home
                </span>

            </a>


            <!-- USER LOGIN -->

            <a href="login.php" class="active">

                <span class="menu-icon">
                    👤
                </span>

                <span>
                    User Login
                </span>

            </a>


            <!-- ADMIN LOGIN -->

            <a href="../admin/admin_login.php">

                <span class="menu-icon">
                    🛡
                </span>

                <span>
                    Admin Login
                </span>

            </a>


            <!-- WORKER LOGIN -->

            <a href="../worker/worker_login.php">

                <span class="menu-icon">
                    👷
                </span>

                <span>
                    Worker Login
                </span>

            </a>

        </div>

    </aside>


    <!-- CONTENT -->

    <main class="content">

        <div class="topbar">

            🍃 Cleaner City

            <span>|</span>

            Greener Future

        </div>


        <div class="login-area">

            <!-- LOGIN FORM -->

            <div class="login-box">

                <div class="login-title">

                    <div class="icon">
                        👤
                    </div>

                    <h1>
                        User Login
                    </h1>

                </div>


                <p class="welcome">
                    Welcome back! Please login to your account.
                </p>


                <?php if (!empty($message)) { ?>

                    <div class="message">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php } ?>


                <form method="POST">

                    <div class="input-box">

                        <label>

                            Email Address

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email address"
                            required
                        >

                    </div>


                    <div class="input-box">

                        <label>

                            Password

                            <span class="required">
                                *
                            </span>

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
                        name="login"
                        class="login-button"
                    >

                        ⇥ &nbsp; Login

                    </button>

                </form>


                <!-- REGISTER -->

                <div class="register-text">

                    Don't have an account?

                    <a href="register.php">
                        Register Here
                    </a>

                </div>


                <!-- BACK TO HOME -->

                <div class="back-home">

                    <a href="../index.php">
                        ← Back to Home
                    </a>

                </div>

            </div>


            <!-- RIGHT SIDE -->

            <div class="illustration">

                <h2>
                    Keep Your City Clean
                </h2>

                <h3>
                    Together
                </h3>

                <p>
                    Reduce waste &nbsp; | &nbsp;
                    Build a healthier tomorrow
                </p>


                <div class="scene">

                    <span class="tree">
                        🌳
                    </span>

                    <span class="truck">
                        🚛
                    </span>

                    <span class="tree">
                        🌳
                    </span>

                </div>

            </div>

        </div>

    </main>

</div>

</body>

</html>