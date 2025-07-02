<?php
require 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // 查询商品列表
        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 添加商品
        $name = $_POST['name'];
        $price = $_POST['price'];

        $stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (:name, :price)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            echo "商品添加成功！";
        } else {
            echo "商品添加失败。";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // 更新商品
        parse_str(file_get_contents("php://input"), $putData);
        $id = $putData['id'];
        $name = $putData['name'];
        $price = $putData['price'];

        $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            echo "商品更新成功！";
        } else {
            echo "商品更新失败。";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // 删除商品
        parse_str(file_get_contents("php://input"), $deleteData);
        $id = $deleteData['id'];

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "商品删除成功！";
        } else {
            echo "商品删除失败。";
        }
    }
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage();
}
?>