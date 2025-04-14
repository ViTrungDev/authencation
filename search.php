<?php
require 'connect.php';

$id = $_GET['id'] ?? '';

// Tự động chọn bảng từ URL (admin.php cho user, student_management.php cho sinh viên)
$from = $_GET['from'] ?? '';

if ($from === 'admin') {
    $sql = "SELECT * FROM users WHERE id = ?";
} elseif ($from === 'student_management') {
    $sql = "SELECT * FROM students WHERE id = ?";
} else {
    echo "Chưa xác định trang tìm kiếm!";
    exit;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Kết quả tìm kiếm</h2>";
echo "<table border='1'>
        <tr><th>ID</th><th>Tên</th><th>Email</th><th>Điện thoại</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $val) {
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
