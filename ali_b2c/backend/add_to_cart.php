<?php
require 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo "请先登录。";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // 检查商品是否存在
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "商品不存在。";
            exit;
        }

        // 插入或更新购物车
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity) 
                               ON DUPLICATE KEY UPDATE quantity = quantity + :quantity");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);

        if ($stmt->execute()) {
            echo "商品已添加到购物车！";
        } else {
            echo "添加失败。";
        }
    }
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage();
}
?>