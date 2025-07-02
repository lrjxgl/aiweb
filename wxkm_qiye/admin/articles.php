<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 获取文章列表
$articles = [];
try {
    $result = $conn->query("
        SELECT a.*, u.username as author_name 
        FROM articles a
        JOIN admin_users u ON a.author_id = u.id
        ORDER BY a.created_at DESC
    ");
    $articles = $result->fetch_all(MYSQLI_ASSOC);
} catch(Exception $e) {
    $error = '获取文章列表时出错: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .article-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            color: white;
        }
        .status-draft {
            background-color: #f39c12;
        }
        .status-published {
            background-color: #2ecc71;
        }
        .status-archived {
            background-color: #95a5a6;
        }
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
        }
        .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .admin-table tr:hover td {
            background-color: #f8f9fa;
        }
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            margin-right: 5px;
            transition: all 0.2s;
        }
        .edit-btn {
            background-color: #3498db;
            color: white;
        }
        .edit-btn:hover {
            background-color: #2980b9;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .add-btn {
            display: inline-flex;
            align-items: center;
            background-color: #2ecc71;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        .add-btn:hover {
            background-color: #27ae60;
            transform: translateY(-1px);
        }
        .add-btn::before {
            content: "+";
            margin-right: 5px;
            font-size: 16px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .admin-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .action-btn {
                margin-bottom: 5px;
            }
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
                    <li><a href="articles.php" class="active">文章管理</a></li>
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>文章管理</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <a href="add_article.php" class="add-btn">添加新文章</a>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>标题</th>
                        <th>作者</th>
                        <th>状态</th>
                        <th>发布时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article['id']); ?></td>
                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                        <td><?php echo htmlspecialchars($article['author_name']); ?></td>
                        <td>
                            <span class="article-status status-<?php echo htmlspecialchars($article['status']); ?>">
                                <?php 
                                    $statusText = [
                                        'draft' => '草稿',
                                        'published' => '已发布',
                                        'archived' => '已归档'
                                    ];
                                    echo $statusText[$article['status']];
                                ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($article['published_at'] ?? '-'); ?></td>
                        <td>
                            <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="action-btn edit-btn">编辑</a>
                            <a href="delete_article.php?id=<?php echo $article['id']; ?>" class="action-btn delete-btn" onclick="return confirm('确定要删除这篇文章吗？')">删除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>