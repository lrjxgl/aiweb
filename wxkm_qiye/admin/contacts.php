<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 获取所有联系表单
$contacts = [];
try {
    $result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $result->fetch_all(MYSQLI_ASSOC);
} catch(Exception $e) {
    $error = '获取联系表单时出错: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>联系表单管理</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .admin-table tr:hover {
            background-color: #f5f5f5;
        }
        .view-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 8px;
            font-size: 14px;
            background-color: var(--secondary-color);
            color: white;
            transition: all 0.2s ease;
        }
        .view-btn:hover {
            background-color: #27ae60;
        }
        .delete-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            background-color: var(--danger-color);
            color: white;
            transition: all 0.2s ease;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .error-message {
            color: var(--danger-color);
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
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
                    <li><a href="contacts.php" class="active">联系表单</a></li>
                    <li><a href="articles.php">文章管理</a></li>
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>联系表单管理</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>主题</th>
                        <th>时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['id']); ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                        <td><?php echo htmlspecialchars($contact['created_at']); ?></td>
                        <td>
                            <a href="view_contact.php?id=<?php echo $contact['id']; ?>" class="view-btn">查看</a>
                            <a href="delete_contact.php?id=<?php echo $contact['id']; ?>" class="delete-btn" onclick="return confirm('确定要删除这条联系表单吗？')">删除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>