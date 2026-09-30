<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit_request'])) {

    $garbage_type = $_POST['garbage_type'];
    $address = $_POST['address'];
    $pickup_date = $_POST['pickup_date'];
    $description = $_POST['description'];

    // आजदेखि 7 दिनसम्मको date मात्र अनुमति
    $today = date("Y-m-d");
    $last_date = date("Y-m-d", strtotime("+7 days"));

    if ($pickup_date < $today) {

        $message = "Past date select गर्न मिल्दैन!";

    } elseif ($pickup_date > $last_date) {

        $message = "आजदेखि बढीमा 7 दिनसम्मको date मात्र select गर्नुहोस्!";

    } else {

        $sql = "INSERT INTO pickup_requests
                (user_id, garbage_type, address, pickup_date, description, status)
                VALUES
                ('$user_id', '$garbage_type', '$address', '$pickup_date', '$description', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            $message = "Pickup request submitted successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Request Garbage Pickup</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f7f3;
            margin: 0;
            padding: 30px;
        }

        .box {
            width: 480px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px #ccc;
        }

        h2 {
            text-align: center;
            color: green;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 90px;
            resize: vertical;
        }

        .garbage-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
        }

        .garbage-options label {
            font-weight: normal;
            margin: 0;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error {
            color: red;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 25px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: darkgreen;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: blue;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="box">

        <h2>Request Garbage Pickup</h2>

        <?php if ($message != ""): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Garbage Type</label>

            <div class="garbage-options">
                <label>
                    <input type="radio" name="garbage_type" value="Organic Waste" required>
                    Organic Waste
                </label>

                <label>
                    <input type="radio" name="garbage_type" value="Plastic Waste">
                    Plastic Waste
                </label>

                <label>
                    <input type="radio" name="garbage_type" value="Paper Waste">
                    Paper Waste
                </label>

                <label>
                    <input type="radio" name="garbage_type" value="Glass Waste">
                    Glass Waste
                </label>

                <label>
                    <input type="radio" name="garbage_type" value="Metal Waste">
                    Metal Waste
                </label>

                <label>
                    <input type="radio" name="garbage_type" value="Electronic Waste">
                    Electronic Waste
                </label>
            </div>

            <label>Pickup Address</label>
            <input
                type="text"
                name="address"
                placeholder="Enter pickup address"
                required
            >

            <label>Pickup Date</label>

            <input
                type="date"
                name="pickup_date"
                min="<?php echo date('Y-m-d'); ?>"
                max="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
                required
            >

            <small>
                You can select a date from today up to the next 7 days only.
            </small>

            <label>Description</label>

            <textarea
                name="description"
                placeholder="Enter additional details"
            ></textarea>

            <button type="submit" name="submit_request">
                Submit Request
            </button>

        </form>

        <a href="dashboard.php" class="back">
            Back to Dashboard
        </a>

    </div>

</body>
</html>