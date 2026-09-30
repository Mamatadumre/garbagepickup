<?php
$conn = mysqli_connect("localhost", "root", "", "garbagepickupsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$name = "Rashmi Karki";
$email = "rashmi1@gmail.com";
$password = password_hash("user456", PASSWORD_DEFAULT);

mysqli_query($conn, "DELETE FROM admins");

$stmt = mysqli_prepare($conn, "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>