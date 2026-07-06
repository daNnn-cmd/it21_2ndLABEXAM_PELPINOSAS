<?php
session_start();
include("db.php");

// Check if user is logged in
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// Session timeout check (30 minutes)
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)){
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Validate ID parameter
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);

// Use prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: dashboard.php");
exit();
?>
