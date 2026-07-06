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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Welcome <?php echo htmlspecialchars($_SESSION['user']); ?></h2>

<a href="add_student.php">Add Student</a> |
<a href="logout.php">Logout</a>

<h3>Student List</h3>

<table border="1">
<tr>
    <th>ID</th>
    <th>Student ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Action</th>
</tr>

<?php
// Use prepared statement with JOIN to get course information
$stmt = mysqli_prepare($conn, "SELECT s.id, s.student_id, s.fullname, s.email, c.course_name 
                                FROM students s 
                                JOIN courses c ON s.course_id = c.id");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($result)){
?>
<tr>
    <td><?php echo htmlspecialchars($row['id']); ?></td>
    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
    <td>
        <a href="delete_student.php?id=<?php echo htmlspecialchars($row['id']); ?>">
            Delete
        </a>
    </td>
</tr>
<?php } 
mysqli_stmt_close($stmt);
?>

</table>

</body>
</html>
