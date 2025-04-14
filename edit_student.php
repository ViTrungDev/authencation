<?php
// Kết nối cơ sở dữ liệu
include('connect.php'); // Giả sử bạn có tệp db_connect.php để kết nối với cơ sở dữ liệu

// Kiểm tra nếu có id từ URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Lấy thông tin sinh viên từ cơ sở dữ liệu
    $sql = "SELECT name, class, ngaySinh, diaChi, soDT, DiemTK, xepLoai FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id); // Gán ID sinh viên
    $stmt->execute();
    $result = $stmt->get_result();

    // Nếu tìm thấy sinh viên trong cơ sở dữ liệu
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        // Nếu không tìm thấy sinh viên, chuyển hướng về trang quản lý sinh viên
        header("Location: student_management.php");
        exit;
    }
} else {
    // Nếu không có ID, chuyển hướng về trang quản lý sinh viên
    header("Location: student_management.php");
    exit;
}

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $class = $_POST['class'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $soDT = $_POST['soDT'];
    $diemTK = $_POST['DiemTK'];
    $xepLoai = $_POST['xepLoai'];

    // Cập nhật thông tin sinh viên vào cơ sở dữ liệu
    $sql = "UPDATE students SET name = ?, class = ?, ngaySinh = ?, diaChi = ?, soDT = ?, DiemTK = ?, xepLoai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $class, $dob, $address, $soDT, $diemTK, $xepLoai, $student_id);

    if ($stmt->execute()) {
        // Chuyển hướng về trang quản lý sinh viên sau khi cập nhật thành công
        header("Location: student_management.php");
        exit;
    } else {
        echo "Lỗi khi cập nhật thông tin sinh viên!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            text-decoration: none;
            color: #333;
        }

        a:hover {
            color: #007BFF;
        }
    </style>
</head>
<body>
    <h1>Chỉnh sửa thông tin sinh viên</h1>
    <form method="POST">
        <label for="name">Họ và tên:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" required><br><br>

        <label for="class">Lớp:</label>
        <input type="text" name="class" id="class" value="<?php echo htmlspecialchars($student['class'] ?? ''); ?>" required><br><br>

        <label for="dob">Ngày sinh:</label>
        <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($student['ngaySinh'] ?? ''); ?>" required><br><br>

        <label for="address">Địa chỉ:</label>
        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($student['diaChi'] ?? ''); ?>"><br><br>

        <label for="soDT">Số điện thoại:</label>
        <input type="text" name="soDT" id="soDT" value="<?php echo htmlspecialchars($student['soDT'] ?? ''); ?>"><br><br>

        <label for="DiemTK">Điểm tổng kết:</label>
        <input type="number" step="0.01" name="DiemTK" id="DiemTK" value="<?php echo htmlspecialchars($student['DiemTK'] ?? ''); ?>"><br><br>

        <label for="xepLoai">Xếp loại:</label>
        <select name="xepLoai" id="xepLoai" required>
            <option value="Yếu" <?php echo (isset($student['xepLoai']) && $student['xepLoai'] == 'Yếu') ? 'selected' : ''; ?>>Yếu</option>
            <option value="Trung Bình" <?php echo (isset($student['xepLoai']) && $student['xepLoai'] == 'Trung Bình') ? 'selected' : ''; ?>>Trung Bình</option>
            <option value="Khá" <?php echo (isset($student['xepLoai']) && $student['xepLoai'] == 'Khá') ? 'selected' : ''; ?>>Khá</option>
            <option value="Giỏi" <?php echo (isset($student['xepLoai']) && $student['xepLoai'] == 'Giỏi') ? 'selected' : ''; ?>>Giỏi</option>
            <option value="Suất Sắc" <?php echo (isset($student['xepLoai']) && $student['xepLoai'] == 'Suất Sắc') ? 'selected' : ''; ?>>Suất Sắc</option>
        </select><br><br>

        <button type="submit">Sửa thông tin</button>
    </form>
    <br>
    <a href="student_management.php">Quay lại</a>
</body>
</html>
