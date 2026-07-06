<?php
session_start();
include("db.php");

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Input validation
    if(empty($username) || empty($password)){
        $error = "Username and password are required";
    } elseif(strlen($username) < 3 || strlen($username) > 50){
        $error = "Invalid username length";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            
            // Verify hashed password
            if(password_verify($password, $row['password'])){
                $_SESSION['user'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['last_activity'] = time();
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid Login";
            }
        } else {
            $error = "Invalid Login";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Admin Login</h2>

    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button name="login">Login</button>
    </form>
</div>

</body>
</html>
