<?php
function getProducts($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

function getProduct($conn, $id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function createUser($conn, $input) {
    $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $input['username'], $password, $input['email']);
    if ($stmt->execute()) {
        return ["message" => "User created successfully", "id" => $stmt->insert_id];
    }
    return ["error" => $stmt->error];
}

function getOrders($conn, $userId) {
    $sql = "SELECT o.*, oi.product_id, oi.quantity, oi.price 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orderId = $row['id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $orderId,
                    'total' => $row['total'],
                    'order_date' => $row['order_date'],
                    'items' => []
                ];
            }
            $orders[$orderId]['items'][] = [
                'product_id' => $row['product_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }
    }
    return array_values($orders);
}

function createOrder($conn, $input) {
    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO orders (user_id, total) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $input['user_id'], $input['total']);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($input['items'] as $item) {
            $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        $conn->commit();
        return ["message" => "Order created successfully", "id" => $orderId];
    } catch (Exception $e) {
        $conn->rollback();
        return ["error" => $e->getMessage()];
    }
}
?>