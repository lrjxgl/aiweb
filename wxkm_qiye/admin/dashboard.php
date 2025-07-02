<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理系统</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
        }
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
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h2>后台管理</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php" class="active">控制台</a></li>
                    <li><a href="products.php">产品管理</a></li>
                    <li><a href="contacts.php">联系表单</a></li>
                    <li><a href="articles.php">文章管理</a></li>
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>欢迎回来，<?php echo htmlspecialchars($admin['full_name']); ?></h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>产品数量</h3>
                    <p>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) FROM products");
                        echo $result->fetch_row()[0];
                        ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>联系表单</h3>
                    <p>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) FROM contacts");
                        echo $result->fetch_row()[0];
                        ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>管理员</h3>
                    <p>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) FROM admin_users");
                        echo $result->fetch_row()[0];
                        ?>
                    </p>
                </div>
            </div>

            <div class="recent-contacts">
                <h2>最近联系表单</h2>
                <?php
                $result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
                if ($result->num_rows > 0) {
                    echo '<table class="admin-table">';
                    echo '<tr><th>姓名</th><th>邮箱</th><th>主题</th><th>时间</th></tr>';
                    while($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
                        echo '<td>' . $row['created_at'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>暂无联系表单记录</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>