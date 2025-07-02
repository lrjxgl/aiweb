<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();

// 验证联系表单ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: contacts.php');
    exit;
}

$id = intval($_GET['id']);

// 删除联系表单
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: contacts.php?deleted=1');
    exit;
} catch(Exception $e) {
    die("删除联系表单时出错: " . $e->getMessage());
}