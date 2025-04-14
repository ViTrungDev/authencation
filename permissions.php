<?php
function checkPermission($role, $action, $user_id = null) {
    // Định nghĩa quyền cho từng vai trò
    $permissions = [
        'viewer' => ['read' => true, 'edit_own' => true],  
        'editor' => ['read' => true, 'edit' => true],
        'admin'  => ['read' => true, 'edit' => true, 'admin' => true, 'manage_users' => true]
    ];

    // Kiểm tra quyền dựa trên vai trò
    if (isset($permissions[$role][$action]) && $permissions[$role][$action]) {
        // Nếu quyền là 'edit_own' (chỉnh sửa thông tin cá nhân), kiểm tra xem người dùng có thể chỉnh sửa thông tin của chính mình không
        if ($action === 'edit_own' && $user_id !== null) {
            return true;  // Nếu là 'edit_own', người dùng có thể chỉnh sửa chính mình
        }
        return true;
    }
    return false;
}
?>
