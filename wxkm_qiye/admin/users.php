<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 获取所有管理员
$users = [];
try {
    $result = $conn->query("SELECT id, username, full_name, created_at, updated_at FROM admin_users ORDER BY created_at DESC");
    $users = $result->fetch_all(MYSQLI_ASSOC);
} catch(Exception $e) {
    $error = '获取管理员列表时出错: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员管理</title>
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
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .edit-btn {
            background-color: var(--secondary-color);
            color: white;
        }
        .edit-btn:hover {
            background-color: #27ae60;
        }
        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .add-btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .add-btn:hover {
            background-color: #218838;
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
                    <li><a href="contacts.php">联系表单</a></li>
                    <li><a href="articles.php">文章管理</a></li>
                    <li><a href="users.php" class="active">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>管理员管理</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <a href="add_user.php" class="add-btn">添加新管理员</a>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>姓名</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($user['updated_at']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">编辑</a>
                            <?php if ($user['id'] != $_SESSION['admin_user_id']): ?>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="action-btn delete-btn" onclick="return confirm('确定要删除这个管理员吗？')">删除</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>