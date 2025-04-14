<?php
session_start();

// Xóa toàn bộ dữ liệu session
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: login.php");
exit;
?>