<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $phone, $hashedPassword, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $phone, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Cập nhật thành công!";
    } else {
        $message = "Lỗi khi cập nhật: " . $conn->error;
    }
}

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa thông tin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }
        .container {
            background: #fff;
            max-width: 500px;
            margin: auto;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            padding: 10px;
            color: white;
            background: #28a745;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chỉnh sửa thông tin cá nhân</h2>

    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Số điện thoại:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label>Mật khẩu mới (bỏ trống nếu không đổi):</label>
        <input type="password" name="password">

        <button type="submit">Cập nhật</button>
    </form>

    <div class="back-link">
        <a href="profile.php">⬅ Quay lại trang cá nhân</a>
    </div>
</div>

</body>
</html>
