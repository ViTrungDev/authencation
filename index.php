<?php
session_start();
require 'connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin người dùng từ session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Khởi tạo biến tìm kiếm
$search = $_GET['search'] ?? '';

// Lấy dữ liệu từ bảng tùy thuộc vào vai trò
if ($role == 'admin') {
    // Hiển thị danh sách người dùng cho admin
    $sql_users = "SELECT * FROM users WHERE username LIKE '%$search%'";
    $result_users = $conn->query($sql_users);

    // Truy vấn sinh viên cho admin
    $sql_students = "SELECT id, name, class, DiemTK, xepLoai FROM students WHERE id LIKE '%$search%' OR name LIKE '%$search%'";
    $result_students = $conn->query($sql_students);
} elseif ($role == 'editor') {
    // Hiển thị chỉ danh sách sinh viên cho editor
    $sql_students = "SELECT id, name, class, DiemTK, xepLoai FROM students WHERE id LIKE '%$search%' OR name LIKE '%$search%'";
    $result_students = $conn->query($sql_students);
} if ($role == 'viewer') {
    // Lọc tất cả sinh viên nhưng loại trừ chính mình (bởi vì viewer là user, không phải student)
    $sql_students = "SELECT id, name, class, DiemTK, xepLoai FROM students WHERE id != '$user_id'"; // Không cho phép xem chính mình
    $result_students = $conn->query($sql_students);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Trang chính</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 20px;
        }
        .navbar a {
            float: left;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar .right {
            float: right;
        }
        .content {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            padding: 8px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-box input[type="submit"] {
            padding: 8px 15px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .search-box input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <!-- Thanh Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Nội dung chính -->
    <div class="content">
        <h2>Chào mừng bạn đến với trang chính!</h2>
        
        <!-- Form tìm kiếm -->
        <div class="search-box">
            <form action="index.php" method="get">
                <input type="text" name="search" placeholder="Tìm kiếm theo ID hoặc tên..." value="<?= htmlspecialchars($search) ?>">
                <input type="submit" value="Tìm kiếm">
            </form>
        </div>

        <!-- Hiển thị danh sách người dùng cho admin -->
        <?php if ($role == 'admin'): ?>
            <h3>Danh sách người dùng:</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

        <!-- Hiển thị danh sách sinh viên -->
        <h3>Danh sách sinh viên:</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Lớp</th>
                <th>Điểm Tổng Kết</th>
                <th>Xếp Loại</th>
            </tr>
            <?php
            // Kiểm tra kết quả của người dùng và hiển thị danh sách sinh viên
            if ($result_students && $result_students->num_rows > 0):
                while ($student = $result_students->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['id']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['class']) ?></td>
                        <td><?= htmlspecialchars($student['DiemTK']) ?></td>
                        <td><?= htmlspecialchars($student['xepLoai']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Không có sinh viên nào để hiển thị.</td>
                </tr>
            <?php endif; ?>
        </table>

    </div>
</body>
</html>
