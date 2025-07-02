<?php
session_start();
require_once '../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    try {
        $conn = getDBConnection();
        
        // 调试信息：检查连接是否成功
        if ($conn->connect_error) {
            throw new Exception("数据库连接失败: " . $conn->connect_error);
        }

        $sql = "SELECT id, password FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        // 调试信息：检查prepare是否成功
        if ($stmt === false) {
            throw new Exception("SQL准备失败: " . $conn->error . " | SQL: " . $sql);
        }

        $bind_result = $stmt->bind_param("s", $username);
        
        // 调试信息：检查bind_param是否成功
        if ($bind_result === false) {
            throw new Exception("参数绑定失败: " . $stmt->error);
        }

        $execute_result = $stmt->execute();
        
        // 调试信息：检查execute是否成功
        if ($execute_result === false) {
            throw new Exception("SQL执行失败: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            }
        }
        $error = '用户名或密码错误';
    } catch(Exception $e) {
        $error = '登录时出错: ' . $e->getMessage();
        error_log($e->getMessage()); // 记录错误到日志
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-title {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .login-btn:hover {
            background-color: var(--primary-color);
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">管理员登录</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form class="login-form" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="用户名" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="密码" required>
            </div>
            <button type="submit" class="login-btn">登录</button>
        </form>
    </div>
</body>
</html>