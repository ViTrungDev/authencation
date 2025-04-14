<?php
session_start();
require 'connect.php'; // Kết nối database

// Hàm kiểm tra xem username hoặc email đã tồn tại chưa
function checkExistingUser($conn, $username, $email) {
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if (isset($_POST['submit'])) {
    // Lấy và làm sạch dữ liệu từ form
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải dài ít nhất 6 ký tự!";
    } elseif (checkExistingUser($conn, $username, $email)) {
        $error = "Username hoặc email đã tồn tại!";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Mặc định role là 'viewer' cho người dùng mới
        $role = 'viewer';
        
        // Sử dụng prepared statement để tránh SQL Injection
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $success = "Đăng ký thành công!";
            header("refresh:3;url=login.php");
        } else {
            $error = "Lỗi khi đăng ký: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Đăng Ký</h2>
    
    <?php 
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    if (isset($success)) {
        echo "<p class='success'>$success</p>";
    }
    ?>
    
    <form action="register.php" method="POST">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" autocomplete="off" required>

        <input type="submit" name="submit" value="Đăng Ký">
        <p>Đã có tài khoản? <a href="./login.php">Đăng nhập</a></p>
    </form>
</div>
</body>
</html>