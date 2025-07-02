<?php
require_once '../config.php';
require_once 'functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 验证管理员ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$id = intval($_GET['id']);

// 获取管理员信息
try {
    $stmt = $conn->prepare("SELECT id, username, full_name FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        header('Location: users.php');
        exit;
    }
} catch(Exception $e) {
    die("获取管理员信息时出错: " . $e->getMessage());
}

$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $full_name = sanitizeInput($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 验证表单数据
    if (empty($username) || empty($full_name)) {
        $error = '请填写所有必填字段';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = '两次输入的密码不一致';
    } elseif (!empty($password) && strlen($password) < 8) {
        $error = '密码长度不能少于8个字符';
    } else {
        try {
            // 检查用户名是否已被其他管理员使用
            $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = '用户名已被其他管理员使用';
            } else {
                // 更新管理员信息
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE admin_users SET username = ?, full_name = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->bind_param("sssi", $username, $full_name, $hashed_password, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE admin_users SET username = ?, full_name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->bind_param("ssi", $username, $full_name, $id);
                }
                $stmt->execute();
                $success = '管理员信息更新成功';
                // 重新获取更新后的管理员信息
                $stmt = $conn->prepare("SELECT id, username, full_name FROM admin_users WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            }
        } catch(Exception $e) {
            $error = '更新管理员信息时出错: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑管理员</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        .admin-sidebar {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
        }
        .admin-main {
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .admin-nav ul {
            list-style: none;
        }
        .admin-nav li {
            margin-bottom: 10px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
        }
        .admin-nav a:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .admin-nav a.active {
            background-color: var(--secondary-color);
        }
        .logout-btn {
            color: white;
            background-color: #dc3545;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .submit-btn:hover {
            background-color: var(--primary-color);
        }
        .back-btn {
            display: inline-block;
            color: var(--secondary-color);
            text-decoration: none;
            margin-top: 20px;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
        }
        .password-note {
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 5px;
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
                <h1>编辑管理员</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>

            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="username">用户名 *</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="full_name">姓名 *</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">新密码</label>
                        <input type="password" id="password" name="password">
                        <p class="password-note">如果不修改密码，请留空</p>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">确认新密码</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <button type="submit" class="submit-btn">更新管理员信息</button>
                </form>

                <a href="users.php" class="back-btn">返回管理员列表</a>
            </div>
        </div>
    </div>
</body>
</html>