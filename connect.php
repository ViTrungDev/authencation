<?php

$servername = "localhost";
$username = "root";  // User mặc định là 'root'
$password = "123456789";      // Mật khẩu mặc định để trống
$database = "Authencation";  // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
}

// Thiết lập bộ mã ký tự cho kết nối là utf8mb4
$conn->set_charset("utf8mb4");
?>
