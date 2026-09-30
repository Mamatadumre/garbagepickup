<?php
$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";
$message_type = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Name validation
    if (!preg_match("/^[A-Za-z ]+$/", $name)) {

        $message = "Name should contain letters and spaces only.";
        $message_type = "error";

    // Email validation
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address with @.";
        $message_type = "error";

    // Phone validation
    } elseif (!preg_match("/^(97|98)[0-9]{8}$/", $phone)) {

        $message = "Phone number must be 10 digits and start with 97 or 98.";
        $message_type = "error";

    // Password length
    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    // Password match
    } elseif ($password !== $confirm_password) {

        $message = "Password and Confirm Password do not match.";
        $message_type = "error";

    } else {

        // Check duplicate email
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);

        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0) {

            $message = "This email is already registered.";
            $message_type = "error";

        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (name, email, phone, address, password)
                VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $name,
                $email,
                $phone,
                $address,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful! You can now login.";
                $message_type = "success";

            } else {

                $message = "Registration failed. Please try again.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>User Registration - Garbage Pickup System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f1f8f4;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
        }

        .container {
            width: 460px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        h2 {
            text-align: center;
            color: #198754;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 6px;
            color: #222;
        }

        .required {
            color: red;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #198754;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            border: none;
            border-radius: 6px;
            background: #2e7d32;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #256b2a;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        .login-text {
            text-align: center;
            margin-top: 18px;
            font-size: 15px;
        }

        .login-text a {
            color: #198754;
            font-weight: bold;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #198754;
            text-decoration: none;
            font-weight: bold;
        }

        .back-home:hover {
            text-decoration: underline;
        }

        .hint {
            font-size: 12px;
            color: #777;
            margin-top: 4px;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>User Registration</h2>

    <?php if (!empty($message)) { ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <!-- Full Name -->
        <label>
            Full Name <span class="required">*</span>
        </label>

        <input
            type="text"
            name="name"
            placeholder="Enter your full name"
            pattern="[A-Za-z ]+"
            title="Name should contain letters and spaces only"
            required
        >

        <!-- Email -->
        <label>
            Email Address <span class="required">*</span>
        </label>

        <input
            type="email"
            name="email"
            placeholder="example@gmail.com"
            required
        >

        <div class="hint">
            Example: example@gmail.com
        </div>

        <!-- Phone -->
        <label>
            Phone Number <span class="required">*</span>
        </label>

        <input
            type="tel"
            name="phone"
            placeholder="98XXXXXXXX"
            maxlength="10"
            pattern="(97|98)[0-9]{8}"
            title="Phone number must be 10 digits and start with 97 or 98"
            required
        >

        <div class="hint">
            Must be 10 digits and start with 97 or 98.
        </div>

        <!-- Address -->
        <label>
            Address <span class="required">*</span>
        </label>

        <input
            type="text"
            name="address"
            placeholder="Enter your address"
            required
        >

        <!-- Password -->
        <label>
            Password <span class="required">*</span>
        </label>

        <input
            type="password"
            name="password"
            placeholder="Enter password"
            minlength="6"
            required
        >

        <div class="hint">
            Password must be at least 6 characters.
        </div>

        <!-- Confirm Password -->
        <label>
            Confirm Password <span class="required">*</span>
        </label>

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm password"
            minlength="6"
            required
        >

        <!-- Register -->
        <button type="submit" name="register">
            Register
        </button>

    </form>

    <div class="login-text">

        Already have an account?

        <a href="login.php">
            Login Here
        </a>

    </div>

    <a href="../index.php" class="back-home">
        ← Back to Home
    </a>

</div>

</body>

</html>