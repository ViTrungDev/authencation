<?php
session_start();
require 'connect.php';
require 'permissions.php';

// Kiểm tra xem người dùng đã đăng nhập và có quyền Admin chưa
if (!isset($_SESSION['user_id']) || !checkPermission($_SESSION['role'], 'admin')) {
    header("Location: login.php");
    exit;
}

// Xử lý cập nhật role
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $allowed_roles = ['viewer', 'editor', 'admin'];
    if (in_array($new_role, $allowed_roles)) {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_role, $user_id);

        if ($stmt->execute()) {
            $success = "Cập nhật quyền thành công!";
        } else {
            $error = "Lỗi khi cập nhật: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Quyền không hợp lệ!";
    }
}

// Xác nhận thực hiện xóa
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success = "Xóa người dùng thành công!";
    } else {
        $error = "Lỗi khi xóa người dùng: " . $conn->error;
    }
    $stmt->close();
}

// Lấy danh sách người dùng
$sql = "SELECT id, username, email, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Quản lý người dùng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th { background-color: #f2f2f2; }
        .error { color: red; }
        .success { color: green; }
        .current-role {
            font-weight: bold;
            color: #333;
        }
        .delete-button {
            background-color: #ff6666;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #ff3333;
        }

        /* Modal Style */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .modal-box {
            background: #fff;
            padding: 20px;
            width: 1000px;
            height: 300px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            text-align: center;
            position: relative;
        }

        .modal-box h3 {
            margin-bottom: 30px;
        }

        .modal-box button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .modal-box .confirm-delete {
            background-color: #ff3333;
            color: white;
        }

        .modal-box .cancel-delete {
            background-color: #ccc;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Quản lý người dùng</h2>

    <?php 
    if (isset($error)) echo "<p class='error'>$error</p>";
    if (isset($success)) echo "<p class='success'>$success</p>";
    ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role hiện tại</th>
            <th>Thay đổi Role</th>
            <th>Thao tác</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id']; ?></td>
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td class="current-role"><?= htmlspecialchars($user['role']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                        <select name="role">
                            <option value="viewer" <?= $user['role'] == 'viewer' ? 'selected' : ''; ?>>Viewer</option>
                            <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : ''; ?>>Editor</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <!-- Nút mở modal -->
                    <button type="button" class="delete-button" onclick="showModal(<?= $user['id']; ?>)">Xóa</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="index.php">⬅ Quay lại trang cá nhân</a></p>
</div>

<!-- Modal xác nhận xóa -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Bạn có chắc muốn xóa người dùng này?</h3>
        <form method="POST">
            <input type="hidden" name="user_id" id="deleteUserId">
            <button type="submit" name="delete_user" class="confirm-delete">Xác nhận</button>
            <button type="button" class="cancel-delete" onclick="hideModal()">Hủy</button>
        </form>
    </div>
</div>

<script>
function showModal(userId) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function hideModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>

</body>
</html>
