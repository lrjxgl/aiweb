<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 这里添加设置保存逻辑
    $success = '设置已保存';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .settings-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
        }
        .submit-btn:hover {
            background-color: #27ae60;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #d4edda;
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
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php" class="active">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>系统设置</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <?php if (isset($success)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <div class="settings-form">
                <form method="POST">
                    <div class="form-group">
                        <label for="site_name">网站名称</label>
                        <input type="text" id="site_name" name="site_name" class="form-control" value="企业网站管理系统">
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email">管理员邮箱</label>
                        <input type="email" id="admin_email" name="admin_email" class="form-control" value="admin@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="items_per_page">每页显示项目数</label>
                        <select id="items_per_page" name="items_per_page" class="form-control">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">时区设置</label>
                        <select id="timezone" name="timezone" class="form-control">
                            <option value="Asia/Shanghai" selected>亚洲/上海 (UTC+8)</option>
                            <option value="America/New_York">美国/纽约 (UTC-5)</option>
                            <option value="Europe/London">欧洲/伦敦 (UTC+0)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn">保存设置</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>