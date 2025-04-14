<?php
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
?>

<style>
    body {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
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
    .search-form {
        float: right;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .search-form input,
    .search-form button {
        padding: 5px;
        font-size: 14px;
    }
</style>

<div class="navbar">
    <a href="index.php">Trang chủ</a>
    <a href="profile.php">Trang cá nhân</a>
    
    <!-- Hiển thị các mục cho admin -->
    <?php if ($role == 'admin'): ?>
        <a href="admin.php">Quản lý người dùng</a>
        <a href="student_management.php">Quản lý sinh viên</a>
    <?php endif; ?>
    
    <!-- Hiển thị các mục cho editor -->
    <?php if ($role == 'editor'): ?>
        <a href="student_management.php">Quản lý sinh viên</a>
    <?php endif; ?>
    
    <div class="right">
        <a>Hello, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</a>
        <a href="logout.php">Đăng xuất</a>
    </div>
</div>

