<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
requireAdminLogin();

// 验证文章ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: articles.php');
    exit;
}

$id = intval($_GET['id']);

// 删除文章
try {
    $conn = getDBConnection();
    
    // 获取文章信息以便删除相关图片
    $stmt = $conn->prepare("SELECT featured_image FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    
    // 删除文章
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // 删除相关图片
    if (!empty($article['featured_image']) && file_exists('../' . $article['featured_image'])) {
        unlink('../' . $article['featured_image']);
    }
    
    header('Location: articles.php?deleted=1');
    exit;
} catch(Exception $e) {
    die("删除文章时出错: " . $e->getMessage());
}
?>