<?php
require_once 'config.php';
require_once 'functions.php';

// 验证文章ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: article_list.php');
    exit;
}

$id = intval($_GET['id']);

try {
    $conn = getDBConnection();
    
    // 获取文章详情
    $stmt = $conn->prepare("
        SELECT a.*, u.username as author_name 
        FROM articles a
        JOIN admin_users u ON a.author_id = u.id
        WHERE a.id = ? AND a.status='published'
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    
    if (!$article) {
        header('Location: article_list.php');
        exit;
    }
    
    // 更新浏览计数
    $conn->query("UPDATE articles SET views = views + 1 WHERE id = $id");
    $conn->close();
} catch(Exception $e) {
    die("获取文章详情时出错: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - 企业网站</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .article-detail {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .article-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .article-title {
            font-size: 32px;
            margin-bottom: 15px;
            color: #333;
            line-height: 1.3;
        }
        .article-meta {
            color: #777;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .article-meta span {
            margin-right: 15px;
        }
        .article-content {
            line-height: 1.8;
            color: #444;
            font-size: 16px;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .article-content p {
            margin-bottom: 25px;
        }
        .article-content h2, 
        .article-content h3 {
            margin: 30px 0 15px;
            color: #333;
        }
        .article-content ul,
        .article-content ol {
            margin-bottom: 25px;
            padding-left: 30px;
        }
        .article-content li {
            margin-bottom: 8px;
        }
        .article-actions {
            margin: 40px 0;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .back-to-list {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .back-to-list:hover {
            background-color: #e9ecef;
        }
        .edit-article {
            display: inline-block;
            margin-left: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .edit-article:hover {
            background-color: #2980b9;
        }
        .related-articles {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .related-articles h2 {
            margin-bottom: 20px;
            font-size: 22px;
        }
        .related-article {
            margin-bottom: 15px;
        }
        .related-article a {
            color: #3498db;
            text-decoration: none;
        }
        .related-article a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="article-detail">
            <div class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
        <div class="article-meta">
            <span>作者: <?php echo htmlspecialchars($article['author_name']); ?></span>
            <span>发布时间: <?php echo date('Y-m-d H:i', strtotime($article['published_at'])); ?></span>
            <span>浏览: <?php echo $article['views'] ?? 0; ?></span>
        </div>
            </div>
            
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>
            
            <div class="article-actions">
                <a href="article_list.php" class="back-to-list">&laquo; 返回文章列表</a>
                <?php if (isAdminLoggedIn()): ?>
                    <a href="admin/edit_article.php?id=<?php echo $article['id']; ?>" class="edit-article">编辑文章</a>
                <?php endif; ?>
            </div>
            
            <?php
            // 获取相关文章
            try {
                $conn = getDBConnection();
                $sql = "SELECT id, title FROM articles 
                        WHERE status='published' AND id != ? 
                        ORDER BY published_at DESC LIMIT 5";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $article['id']);
                $stmt->execute();
                $related = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $conn->close();
                
                if (count($related) > 0): ?>
                <div class="related-articles">
                    <h2>相关文章</h2>
                    <?php foreach ($related as $item): ?>
                        <div class="related-article">
                            <a href="article_detail.php?id=<?php echo $item['id']; ?>">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif;
            } catch(Exception $e) {
                // 静默失败，不影响主内容
            }
            ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>