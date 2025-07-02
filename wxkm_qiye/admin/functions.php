<?php
function requireAdminLogin() {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function getAdminInfo($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, username, full_name FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['admin_user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch(Exception $e) {
        die("获取管理员信息失败: " . $e->getMessage());
    }
}