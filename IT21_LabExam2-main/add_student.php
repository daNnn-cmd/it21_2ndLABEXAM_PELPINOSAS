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

if(isset($_POST['add'])){

    $student_id = trim($_POST['student_id']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $course_id = intval($_POST['course_id']);

    // Input validation
    $errors = [];
    
    if(empty($student_id)){
        $errors[] = "Student ID is required";
    } elseif(!preg_match('/^[A-Za-z0-9-]+$/', $student_id)){
        $errors[] = "Student ID contains invalid characters";
    }
    
    if(empty($fullname)){
        $errors[] = "Full Name is required";
    } elseif(strlen($fullname) < 2 || strlen($fullname) > 100){
        $errors[] = "Full Name must be between 2 and 100 characters";
    }
    
    if(empty($email)){
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format";
    }
    
    if(empty($course_id) || $course_id < 1){
        $errors[] = "Course is required";
    }
    
    if(empty($errors)){
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "INSERT INTO students (student_id, fullname, email, course_id) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $student_id, $fullname, $email, $course_id);
        
        if(mysqli_stmt_execute($stmt)){
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error adding student: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Add Student</h2>

    <?php if(!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        Student ID: <input type="text" name="student_id" required><br>
        Full Name: <input type="text" name="fullname" required><br>
        Email: <input type="email" name="email" required><br>
        Course: 
        <select name="course_id" required>
            <option value="">Select a course</option>
            <?php
            $course_stmt = mysqli_prepare($conn, "SELECT id, course_code, course_name FROM courses");
            mysqli_stmt_execute($course_stmt);
            $course_result = mysqli_stmt_get_result($course_stmt);
            while($course_row = mysqli_fetch_assoc($course_result)){
                echo '<option value="' . htmlspecialchars($course_row['id']) . '">' . 
                     htmlspecialchars($course_row['course_code'] . ' - ' . $course_row['course_name']) . 
                     '</option>';
            }
            mysqli_stmt_close($course_stmt);
            ?>
        </select><br>
        <button name="add">Add</button>
    </form>
    
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
