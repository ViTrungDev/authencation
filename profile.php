<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'connect.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang cá nhân</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .profile-info {
            margin-top: 20px;
        }

        .profile-info p {
            font-size: 18px;
            margin: 10px 0;
            color: #444;
        }

        .label {
            font-weight: bold;
            color: #007BFF;
        }

        .edit-btn {
            display: block;
            text-align: center;
            margin-top: 30px;
        }

        .edit-btn a {
            text-decoration: none;
            background: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .edit-btn a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="profile-container">
        <h1>Thông tin cá nhân</h1>

        <div class="profile-info">
            <p><span class="label">Tên người dùng:</span> <?= htmlspecialchars($row['username']) ?></p>
            <p><span class="label">Email:</span> <?= htmlspecialchars($row['email']) ?></p>
            <p><span class="label">Phone:</span> <?= htmlspecialchars($row['phone']) ?></p>
            <p><span class="label">Vai trò:</span> <?= htmlspecialchars($row['role']) ?></p>
        </div>

        <div class="edit-btn">
            <a href="edit_profile.php">✏️ Chỉnh sửa thông tin</a>
        </div>
    </div>

</body>
</html>
