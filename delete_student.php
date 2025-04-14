<?php
// Kết nối cơ sở dữ liệu
include('connect.php'); // Giả sử bạn có tệp db_connect.php để kết nối với cơ sở dữ liệu

// Kiểm tra nếu có id sinh viên cần xóa
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Kiểm tra xem người dùng có thực sự muốn xóa sinh viên không
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Nếu người dùng chọn 'Xóa', thực hiện xóa
        if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
            // Xóa sinh viên khỏi cơ sở dữ liệu
            $sql = "DELETE FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student_id);

            if ($stmt->execute()) {
                // Chuyển hướng về trang quản lý sinh viên sau khi xóa thành công
                header("Location: student_management.php");
                exit;
            } else {
                echo "Lỗi khi xóa sinh viên!";
            }
        } else {
            // Nếu người dùng không xác nhận xóa, chuyển hướng về trang quản lý sinh viên
            header("Location: student_management.php");
            exit;
        }
    }
} else {
    // Nếu không có id, chuyển hướng về trang quản lý sinh viên
    header("Location: student_management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        .container {
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .buttons {
          display: flex;
          justify-content: center;
          margin-top: 20px;
          gap: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[name="confirm_delete"][value="yes"] {
            background-color: #e74c3c;
            color: white;
        }

        button[name="confirm_delete"][value="no"] {
            background-color: #2ecc71;
            color: white;
        }

        a {
            display: block;
            text-align: center;
            margin-left: -50px;
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
        p{
          text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Xác nhận xóa sinh viên</h1>
        <p >Bạn có chắc chắn muốn xóa sinh viên này không?</p>
        <form method="POST">
            <div class="buttons">
                <button type="submit" name="confirm_delete" value="yes">Xóa</button>
                <button type="submit" name="confirm_delete" value="no">Không xóa</button>
            </div>
        </form>
        <br>
        <a href="student_management.php">Quay lại</a>
    </div>
</body>
</html>
