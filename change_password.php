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

$message = "";
$message_type = "";

$worker_id = (int) $_SESSION['worker_id'];

if (isset($_POST['change_password'])) {

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Please fill all fields.";
        $message_type = "error";

    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
        $message_type = "error";

    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters.";
        $message_type = "error";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT password FROM workers WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $worker_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $worker = mysqli_fetch_assoc($result);

        if ($worker && password_verify($old_password, $worker['password'])) {

            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            $update = mysqli_prepare(
                $conn,
                "UPDATE workers SET password = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "si",
                $hashed_password,
                $worker_id
            );

            if (mysqli_stmt_execute($update)) {
                $message = "Password changed successfully.";
                $message_type = "success";
            } else {
                $message = "Failed to change password.";
                $message_type = "error";
            }

            mysqli_stmt_close($update);

        } else {
            $message = "Old password is incorrect.";
            $message_type = "error";
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
    <title>Change Password - Worker</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f0fdf4;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 420px;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #166534;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #333;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #16a34a;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            border-radius: 6px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #166534;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Change Password</h2>
    <p class="subtitle">Worker Account</p>

    <?php if (!empty($message)) { ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST">

        <label>Current Password</label>
        <input
            type="password"
            name="old_password"
            placeholder="Enter current password"
            required
        >

        <label>New Password</label>
        <input
            type="password"
            name="new_password"
            placeholder="Enter new password"
            minlength="6"
            required
        >

        <label>Confirm New Password</label>
        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm new password"
            minlength="6"
            required
        >

        <button type="submit" name="change_password">
            Change Password
        </button>

    </form>

    <a href="worker_dashboard.php" class="back">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>