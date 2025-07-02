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

    $user_id = $_SESSION['user_id'];

    // 获取购物车商品
    $stmt = $pdo->prepare("SELECT product_id, quantity FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        echo "购物车为空。";
        exit;
    }

    // 创建订单
    $pdo->beginTransaction();
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $item['product_id']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->execute();
    }

    // 清空购物车
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $pdo->commit();
    echo "订单已生成！";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "订单生成失败: " . $e->getMessage();
}
?>