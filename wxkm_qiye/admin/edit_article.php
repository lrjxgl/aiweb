<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
requireAdminLogin();
$conn = getDBConnection();
$admin = getAdminInfo($conn);

// 验证文章ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: articles.php');
    exit;
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// 获取文章信息
try {
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    
    if (!$article) {
        header('Location: articles.php');
        exit;
    }
} catch(Exception $e) {
    die("获取文章信息时出错: " . $e->getMessage());
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $slug = sanitizeInput($_POST['slug']);
    $content = sanitizeInput($_POST['content']);
    $excerpt = sanitizeInput($_POST['excerpt']);
    $status = sanitizeInput($_POST['status']);

    // 验证表单数据
    if (empty($title) || empty($slug) || empty($content)) {
        $error = '请填写所有必填字段';
    } else {
        try {
            $published_at = $article['published_at'];
            if ($status === 'published' && $article['status'] !== 'published') {
                $published_at = date('Y-m-d H:i:s');
            } else if ($status !== 'published') {
                $published_at = null;
            }

            $stmt = $conn->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, excerpt = ?, status = ?, published_at = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $title, $slug, $content, $excerpt, $status, $published_at, $id);
            $stmt->execute();
            $success = '文章更新成功';
            // 重新获取更新后的文章信息
            $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $article = $result->fetch_assoc();
        } catch(Exception $e) {
            $error = '更新文章时出错: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑文章</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .slug-preview {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .slug-preview a {
            color: #3498db;
            text-decoration: none;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.15s;
        }
        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-btn:hover {
            color: #495057;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
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
                    <li><a href="articles.php">文章管理</a></li>
                    <li><a href="users.php">管理员</a></li>
                    <li><a href="settings.php">系统设置</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>编辑文章</h1>
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
                        <label for="title">标题 *</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="slug">URL别名 *</label>
                        <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($article['slug']); ?>" required>
                        <div class="slug-preview">预览: <a href="#" id="slug-preview-link">/article/<?php echo htmlspecialchars($article['slug']); ?></a></div>
                    </div>

                    <div class="form-group">
                        <label for="content">内容 *</label>
                        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="excerpt">摘要</label>
                        <textarea id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">状态</label>
                        <select id="status" name="status">
                            <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>草稿</option>
                            <option value="published" <?php echo $article['status'] === 'published' ? 'selected' : ''; ?>>发布</option>
                            <option value="archived" <?php echo $article['status'] === 'archived' ? 'selected' : ''; ?>>归档</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">更新文章</button>
                </form>

                <a href="articles.php" class="back-btn">返回文章列表</a>
            </div>
        </div>
    </div>

    <script>
        // 自动生成slug预览
        document.getElementById('title').addEventListener('input', function() {
            if (!document.getElementById('slug').value) {
                const slug = this.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
                updateSlugPreview();
            }
        });

        document.getElementById('slug').addEventListener('input', updateSlugPreview);

        function updateSlugPreview() {
            const slug = document.getElementById('slug').value;
            document.getElementById('slug-preview-link').textContent = '/article/' + (slug || 'slug');
        }
    </script>
</body>
</html>