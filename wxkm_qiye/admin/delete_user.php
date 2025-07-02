<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();

// 验证管理员ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$id = intval($_GET['id']);

// 防止删除当前登录的管理员
if ($id == $_SESSION['admin_user_id']) {
    header('Location: users.php?error=不能删除当前登录的管理员');
    exit;
}

// 删除管理员
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: users.php?deleted=1');
    exit;
} catch(Exception $e) {
    die("删除管理员时出错: " . $e->getMessage());
}