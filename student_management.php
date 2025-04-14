<?php
session_start();
require 'connect.php';

// Kiểm tra xem người dùng có quyền admin không
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'editor') {
    header("Location: index.php"); // Nếu không phải admin, chuyển hướng về trang chủ
    exit();
}

// Kiểm tra nếu có ID trong URL để biết là đang sửa
$student_id = isset($_GET['id']) ? $_GET['id'] : null;
$student_data = null;

// Nếu có ID, truy vấn thông tin sinh viên cần sửa
if ($student_id) {
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
}

// Nếu gửi form sửa, thực hiện cập nhật dữ liệu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_student'])) {
    $name = !empty($_POST['name']) ? $_POST['name'] : NULL;
    $class = !empty($_POST['class']) ? $_POST['class'] : NULL;
    $ngaySinh = !empty($_POST['ngaySinh']) ? $_POST['ngaySinh'] : NULL;
    $diaChi = !empty($_POST['diaChi']) ? $_POST['diaChi'] : NULL;
    $soDT = !empty($_POST['soDT']) ? $_POST['soDT'] : NULL;
    $DiemTK = ($_POST['DiemTK'] !== "") ? floatval($_POST['DiemTK']) : NULL;
    $xepLoai = !empty($_POST['xepLoai']) ? $_POST['xepLoai'] : NULL;

    $sql = "UPDATE students SET name = ?, class = ?, ngaySinh = ?, diaChi = ?, soDT = ?, DiemTK = ?, xepLoai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdsi", $name, $class, $ngaySinh, $diaChi, $soDT, $DiemTK, $xepLoai, $student_id);
    $stmt->execute();
    $stmt->close();

    header("Location: student_management.php");
    exit();
}

// Nếu gửi form thêm, thực hiện thêm sinh viên mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = !empty($_POST['name']) ? $_POST['name'] : NULL;
    $class = !empty($_POST['class']) ? $_POST['class'] : NULL;
    $ngaySinh = !empty($_POST['ngaySinh']) ? $_POST['ngaySinh'] : NULL;
    $diaChi = !empty($_POST['diaChi']) ? $_POST['diaChi'] : NULL;
    $soDT = !empty($_POST['soDT']) ? $_POST['soDT'] : NULL;
    $DiemTK = ($_POST['DiemTK'] !== "") ? floatval($_POST['DiemTK']) : NULL;
    $xepLoai = !empty($_POST['xepLoai']) ? $_POST['xepLoai'] : NULL;

    $sql = "INSERT INTO students (name, class, ngaySinh, diaChi, soDT, DiemTK, xepLoai) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssd", $name, $class, $ngaySinh, $diaChi, $soDT, $DiemTK, $xepLoai);
    $stmt->execute();
    $stmt->close();

    header("Location: student_management.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .student-table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .student-table th, .student-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .student-table th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        .student-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .student-table tr:hover {
            background-color: #f1f1f1;
        }

        .student-table td {
            font-size: 14px;
            color: #555;
        }

        .student-table tr:first-child th {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .student-table tr:last-child td {
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .btn {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 1000px;
            height: 600px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 14px 20px;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .btn_add_sinhvien {
            margin-left: 150px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h2>Quản lý sinh viên</h2>

    <!-- Nút thêm sinh viên -->
    <button class="btn btn_add_sinhvien" onclick="document.getElementById('addModal').style.display='block'">Thêm sinh viên</button>

    <!-- Modal thêm sinh viên -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
            <h3>Thêm sinh viên mới</h3>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Tên sinh viên" required>
                <input type="text" name="class" placeholder="Lớp" required>
                <input type="date" name="ngaySinh" placeholder="Ngày sinh" required>
                <input type="text" name="diaChi" placeholder="Địa chỉ" required>
                <input type="text" name="soDT" placeholder="Số điện thoại" required>
                <input type="number" step="0.01" name="DiemTK" placeholder="Điểm tổng kết" required>
                <select name="xepLoai" required>
                    <option value="">--Chọn xếp loại--</option>
                    <option value="Yếu">Yếu</option>
                    <option value="Trung Bình">Trung Bình</option>
                    <option value="Khá">Khá</option>
                    <option value="Giỏi">Giỏi</option>
                    <option value="Suất Sắc">Suất Sắc</option>
                </select>
                <button type="submit" name="add_student" class="btn">Thêm</button>
            </form>
        </div>
    </div>

    <!-- Danh sách sinh viên -->
    <?php
    $sql = "SELECT * FROM students";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='student-table'>
                <tr><th>ID</th><th>Tên</th><th>Lớp</th><th>Ngày Sinh</th><th>Địa Chỉ</th><th>Số Điện Thoại</th><th>Điểm TK</th><th>Xếp Loại</th><th>Hành Động</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['class']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ngaySinh']) . "</td>";
            echo "<td>" . htmlspecialchars($row['diaChi']) . "</td>";
            echo "<td>" . htmlspecialchars($row['soDT']) . "</td>";
            echo "<td>" . htmlspecialchars($row['DiemTK']) . "</td>";
            echo "<td>" . htmlspecialchars($row['xepLoai']) . "</td>";
            echo "<td>
            <a href='edit_student.php?id=" . htmlspecialchars($row['id']) . "' class='btn'>Chỉnh sửa</a>
            <a href='delete_student.php?id=" . htmlspecialchars($row['id']) . "' class='btn'>Xóa</a>
            </td>";
    echo "</tr>";
    
        }
        echo "</table>";
    } else {
        echo "<p>Hiện tại không có sinh viên nào trong hệ thống.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
