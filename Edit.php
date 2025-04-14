<?php
session_start();
require_once 'connect.php';

if (!checkPermission($_SESSION['role'], 'update')) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $query = "UPDATE students SET name = '$name', class = '$class' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: index.php");
}

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id = $id"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Student</h1>
    <form method="POST">
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
        <input type="text" name="class" value="<?php echo $student['class']; ?>" required>
        <button type="submit">Save</button>
    </form>
</body>
</html>