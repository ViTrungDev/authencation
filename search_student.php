<?php
session_start();
require 'connect.php';

$id = $_GET['id'] ?? '';
$from = $_GET['from'] ?? '';

// Nếu ID không phải là số hợp lệ, dừng lại và báo lỗi
if (!is_numeric($id) || empty($id)) {
    echo "ID không hợp lệ!";
    exit;
}

if ($from === 'admin') {
    // Tìm kiếm theo ID trong bảng users
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);  // Dùng "s" vì giả sử ID là string
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Kết quả tìm kiếm - Users</h2>";
    echo "<table border='1'>
            <tr><th>ID</th><th>Tên</th><th>Email</th><th>Điện thoại</th></tr>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $val) {
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Không tìm thấy kết quả.</td></tr>";
    }
    echo "</table>";

} elseif ($from === 'student_management') {
    // Tìm kiếm theo ID trong bảng students
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);  // Dùng "s" vì giả sử ID là string
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Kết quả tìm kiếm - Students</h2>";
    echo "<table border='1'>
            <tr><th>ID</th><th>Tên</th><th>Email</th><th>Điện thoại</th></tr>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $val) {
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Không tìm thấy kết quả.</td></tr>";
    }
    echo "</table>";

} else {
    echo "Chưa xác định trang tìm kiếm!";
    exit;
}

$conn->close();
?>
