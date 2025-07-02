<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 验证联系表单ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: contacts.php');
    exit;
}

$id = intval($_GET['id']);

// 获取联系表单详情
try {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    
    if (!$contact) {
        header('Location: contacts.php');
        exit;
    }
} catch(Exception $e) {
    die("获取联系表单时出错: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看联系表单</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .contact-details {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .contact-field {
            margin-bottom: 25px;
        }
        .contact-field label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-size: 16px;
        }
        .contact-field p {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid var(--secondary-color);
            font-size: 15px;
            line-height: 1.6;
        }
        .back-btn {
            display: inline-block;
            color: var(--secondary-color);
            text-decoration: none;
            margin-top: 20px;
            padding: 8px 15px;
            border: 1px solid var(--secondary-color);
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        .back-btn:hover {
            color: white;
            background-color: var(--secondary-color);
        }
        .message-content {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h2>后台管理</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php">控制台</a></li>
                    <li><a href="products.php">产品管理</a></li>
                    <li><a href="contacts.php">联系表单</a></li>
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>联系表单详情</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <div class="contact-details">
                <div class="contact-field">
                    <label>姓名</label>
                    <p><?php echo htmlspecialchars($contact['name']); ?></p>
                </div>

                <div class="contact-field">
                    <label>邮箱</label>
                    <p><?php echo htmlspecialchars($contact['email']); ?></p>
                </div>

                <div class="contact-field">
                    <label>主题</label>
                    <p><?php echo htmlspecialchars($contact['subject']); ?></p>
                </div>

                <div class="contact-field">
                    <label>消息内容</label>
                    <p><?php echo nl2br(htmlspecialchars($contact['message'])); ?></p>
                </div>

                <div class="contact-field">
                    <label>提交时间</label>
                    <p><?php echo htmlspecialchars($contact['created_at']); ?></p>
                </div>
            </div>

            <a href="contacts.php" class="back-btn">返回联系表单列表</a>
        </div>
    </div>
</body>
</html>