<?php
require_once 'config.php';
require_once 'functions.php';

// 验证文章slug
if (!isset($_GET['slug'])) {
    header('Location: index.php');
    exit;
}

$slug = sanitizeInput($_GET['slug']);

// 获取文章信息
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT a.*, u.username as author_name 
        FROM articles a
        JOIN admin_users u ON a.author_id = u.id
        WHERE a.slug = ? AND a.status = 'published'
    ");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    
    if (!$article) {
        header('Location: index.php');
        exit;
    }
} catch(Exception $e) {
    die("获取文章信息时出错: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .article-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .article-title {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        .article-content {
            line-height: 1.8;
            font-size: 1.1em;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border-radius: 4px;
        }
        .article-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <article class="article">
            <header class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <span>作者: <?php echo htmlspecialchars($article['author_name']); ?></span> | 
                    <span>发布时间: <?php echo date('Y-m-d H:i', strtotime($article['published_at'])); ?></span>
                </div>
            </header>

            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>

            <footer class="article-footer">
                <a href="index.php" class="back-btn">返回首页</a>
            </footer>
        </article>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>