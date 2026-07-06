<?php
/**
 * PASSWORD HASHING UTILITY
 * 
 * This script helps generate password hashes for the users table.
 * Run this file once to generate a hash, then copy it to your database.
 * 
 * SECURITY NOTE: Delete this file after use in production!
 */

include("db.php");

if(isset($_POST['generate'])){
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Generate password hash
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Update or insert user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = ?");
    mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $hash);
    
    if(mysqli_stmt_execute($stmt)){
        $message = "Password hash generated and stored for user: " . htmlspecialchars($username);
        $message .= "<br>Hash: " . htmlspecialchars($hash);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Hash Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Password Hash Generator</h2>
    
    <p style="color: red; font-weight: bold;">
        WARNING: Delete this file after use in production!
    </p>

    <?php if(isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button name="generate">Generate Hash & Store</button>
    </form>
    
    <br>
    <a href="login.php">Back to Login</a>
</div>

</body>
</html>
