<?php
session_start();
require_once 'connect.php'; // Đảm bảo session đã được khởi động trong file connect.php

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = ""; // Biến chứa thông báo lỗi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy email và mật khẩu từ form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Sử dụng prepared statement để tăng bảo mật
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Thêm tên người dùng vào session
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email']; // Lưu email vào session
            $_SESSION['phone'] = $user['phone'];
            header("Location: index.php"); // Chuyển hướng tới trang chính
            exit;
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Đăng Nhập</title>
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
            width: 300px;
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
            margin-bottom: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Đăng nhập</h2>
    
    <!-- Hiển thị lỗi nếu có -->
    <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Email" required>
        
        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" placeholder="Password" required>
        
        <input type="submit" name="submit" value="Đăng nhập">
        <p>Chưa có tài khoản? <a href="./register.php">Đăng ký</a></p>
    </form>
</div>

</body>
</html>
