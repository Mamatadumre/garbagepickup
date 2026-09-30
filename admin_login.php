<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password.";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password FROM admins WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $admin = mysqli_fetch_assoc($result);

            if (password_verify($password, $admin['password'])) {

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];

                header("Location: admin_dashboard.php");
                exit();

            } else {
                $error = "Incorrect password.";
            }

        } else {
            $error = "Admin account not found.";
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

<title>Admin Login | Garbage Pickup System</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    min-height: 100vh;
    background: #f1f6f3;
}

/* MAIN CONTAINER */
.container {
    display: flex;
    min-height: 100vh;
}

/* ================= SIDEBAR ================= */

.sidebar {
    width: 250px;
    background: #064e3b;
    color: white;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
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
    line-height: 1.3;
}

.logo p {
    font-size: 12px;
    color: #b7e4d0;
    margin-top: 6px;
}

/* MENU */

.menu {
    margin-top: 10px;
}

.menu a {
    display: block;
    text-decoration: none;
    color: #d8eee5;
    padding: 14px 16px;
    margin-bottom: 10px;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.3s;
}

.menu a:hover {
    background: #0b6b50;
    color: white;
    transform: translateX(3px);
}

.menu a.active {
    background: white;
    color: #064e3b;
    font-weight: bold;
}

/* ================= CONTENT ================= */

.content {
    flex: 1;
    display: flex;
    min-height: 100vh;
}

/* LOGIN SECTION */

.login-section {
    width: 55%;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px;
}

.login-box {
    width: 100%;
    max-width: 430px;
}

.top-icon {
    width: 65px;
    height: 65px;
    border-radius: 18px;
    background: #e6f4ee;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 32px;
    margin-bottom: 20px;
}

.login-box h1 {
    color: #064e3b;
    font-size: 32px;
    margin-bottom: 8px;
}

.subtitle {
    color: #777;
    font-size: 14px;
    margin-bottom: 30px;
}

/* ERROR */

.error {
    background: #fee2e2;
    color: #b91c1c;
    border-left: 4px solid #dc2626;
    padding: 12px 14px;
    border-radius: 7px;
    margin-bottom: 20px;
    font-size: 14px;
}

/* FORM */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.form-group input {
    width: 100%;
    padding: 14px 15px;
    border: 1px solid #d4ddd9;
    border-radius: 9px;
    outline: none;
    font-size: 14px;
    transition: 0.3s;
}

.form-group input:focus {
    border-color: #087f5b;
    box-shadow: 0 0 0 3px rgba(8,127,91,0.10);
}

/* LOGIN BUTTON */

.login-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 9px;
    background: #087f5b;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.login-btn:hover {
    background: #056044;
    transform: translateY(-1px);
}

/* BACK HOME */

.back-home {
    text-align: center;
    margin-top: 20px;
}

.back-home a {
    color: #087f5b;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
}

.back-home a:hover {
    text-decoration: underline;
}

/* ================= RIGHT SIDE ================= */

.visual-section {
    width: 45%;
    background: linear-gradient(135deg, #064e3b, #0b7656);
    color: white;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 40px;
}

.visual-content {
    position: relative;
    z-index: 2;
    max-width: 500px;
}

.visual-icon {
    font-size: 100px;
    margin-bottom: 20px;
}

.visual-content h2 {
    font-size: 32px;
    margin-bottom: 15px;
}

.visual-content p {
    color: #d9f3e7;
    line-height: 1.7;
    font-size: 15px;
}

/* DECORATION */

.circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
}

.circle.one {
    width: 300px;
    height: 300px;
    top: -100px;
    right: -100px;
}

.circle.two {
    width: 220px;
    height: 220px;
    bottom: -80px;
    left: -70px;
}

.circle.three {
    width: 100px;
    height: 100px;
    top: 35%;
    right: 10%;
    background: rgba(255,255,255,0.05);
}

/* SECURITY BOX */

.security-box {
    margin-top: 30px;
    padding: 15px 20px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 12px;
    background: rgba(255,255,255,0.08);
}

.security-box strong {
    display: block;
    margin-bottom: 5px;
}

.security-box span {
    font-size: 13px;
    color: #d9f3e7;
}

/* RESPONSIVE */

@media (max-width: 900px) {

    .sidebar {
        width: 210px;
    }

    .login-section {
        width: 55%;
    }

    .visual-section {
        width: 45%;
    }

    .visual-content h2 {
        font-size: 25px;
    }

}

@media (max-width: 700px) {

    .container {
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
        gap: 8px;
        flex-wrap: wrap;
    }

    .menu a {
        margin-bottom: 0;
        padding: 10px 12px;
    }

    .content {
        flex-direction: column;
    }

    .login-section,
    .visual-section {
        width: 100%;
    }

    .visual-section {
        min-height: 350px;
    }

}

</style>
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="logo">

            <div class="logo-icon">♻️</div>

            <h2>Garbage Pickup<br>System</h2>

            <p>Clean • Green • Better</p>

        </div>

        <div class="menu">

            <a href="../index.php">🏠 Home</a>

            <a href="../user/login.php">👤 User Login</a>

            <a href="admin_login.php" class="active">🛡️ Admin Login</a>

            <a href="../worker/worker_login.php">🚛 Worker Login</a>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">

        <!-- LOGIN -->

        <div class="login-section">

            <div class="login-box">

                <div class="top-icon">
                    🛡️
                </div>

                <h1>Admin Login</h1>

                <p class="subtitle">
                    Login to manage the Garbage Pickup System
                </p>


                <?php if (!empty($error)) { ?>

                    <div class="error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php } ?>


                <form method="POST">

                    <div class="form-group">

                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter admin email"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Password</label>

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
                        class="login-btn"
                    >
                        🔐 Login as Admin
                    </button>

                </form>


                <div class="back-home">

                    <a href="../index.php">
                        ← Back to Home
                    </a>

                </div>

            </div>

        </div>


        <!-- RIGHT VISUAL -->

        <div class="visual-section">

            <div class="circle one"></div>
            <div class="circle two"></div>
            <div class="circle three"></div>

            <div class="visual-content">

                <div class="visual-icon">
                    🏙️
                </div>

                <h2>Manage a Cleaner City</h2>

                <p>
                    Admins can manage users, workers and
                    garbage pickup requests from one place.
                </p>


                <div class="security-box">

                    <strong>🔒 Secure Administration</strong>

                    <span>
                        Authorized administrators only
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>