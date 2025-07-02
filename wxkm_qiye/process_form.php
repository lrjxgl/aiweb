<?php
// 设置响应头
header('Content-Type: text/html; charset=utf-8');

// 引入配置文件
require_once 'config.php';

// 验证表单数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取并清理表单数据
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // 验证数据
    $errors = [];
    
    if (empty($name)) {
        $errors[] = '姓名不能为空';
    } elseif (strlen($name) > 100) {
        $errors[] = '姓名长度不能超过100个字符';
    }
    
    if (empty($email)) {
        $errors[] = '邮箱不能为空';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '邮箱格式不正确';
    } elseif (strlen($email) > 100) {
        $errors[] = '邮箱长度不能超过100个字符';
    }
    
    if (empty($subject)) {
        $errors[] = '主题不能为空';
    } elseif (strlen($subject) > 200) {
        $errors[] = '主题长度不能超过200个字符';
    }
    
    if (empty($message)) {
        $errors[] = '消息内容不能为空';
    }

    // 如果没有错误，处理表单
    if (empty($errors)) {
        try {
            $conn = getDBConnection();
            
            // 使用预处理语句防止SQL注入
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            
            if ($stmt->execute()) {
                // 重定向到成功页面
                header('Location: contact_success.php');
                exit;
            } else {
                $errors[] = '提交表单时出错，请稍后再试';
            }
            
            $stmt->close();
            $conn->close();
        } catch(Exception $e) {
            $errors[] = '数据库错误: ' . $e->getMessage();
        }
    }
}

// 如果有错误，显示错误页面
if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>表单提交错误</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <header>
            <div class="container">
                <h1>企业名称</h1>
                <nav>
                    <ul>
                        <li><a href="index.php">首页</a></li>
                        <li><a href="about.php">关于我们</a></li>
                        <li><a href="products.php">产品服务</a></li>
                        <li><a href="contact.php">联系我们</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="container">
            <section>
                <h2>表单提交错误</h2>
                <div class="error-messages">
                    <p>提交表单时遇到以下问题：</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="contact.php" class="back-btn">返回联系表单</a>
                </div>
            </section>
        </main>

        <footer>
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> 企业名称. 保留所有权利。</p>
            </div>
        </footer>
    </body>
    </html>
    <?php
    exit;
}
?>