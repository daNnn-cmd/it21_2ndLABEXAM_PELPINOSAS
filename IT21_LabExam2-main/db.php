<?php
// Secure database connection
$conn = mysqli_connect("localhost", "root", "", "infosec_lab");

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error. Please try again later.");
}

// Set charset to prevent character set attacks
mysqli_set_charset($conn, "utf8mb4");
?>
