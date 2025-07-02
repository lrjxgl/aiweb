<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
requireAdminLogin();

$sql = file_get_contents(__DIR__ . '/create_articles_table.sql');

try {
    $conn = getDBConnection();
    $conn->multi_query($sql);
    echo "文章表创建成功";
} catch(Exception $e) {
    die("创建文章表时出错: " . $e->getMessage());
}
?>
