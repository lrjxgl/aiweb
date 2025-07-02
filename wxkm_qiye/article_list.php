<?php
require_once 'config.php';
require_once 'functions.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

try {
    $conn = getDBConnection();
    
    // 获取文章总数
    $total_result = $conn->query("SELECT COUNT(*) as total FROM articles WHERE status='published'");
    $total = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total / $per_page);
    
    // 获取文章列表
    $sql = "SELECT a.*, u.username as author_name 
            FROM articles a
            JOIN admin_users u ON a.author_id = u.id
            WHERE a.status='published'
            ORDER BY a.published_at DESC
            LIMIT $per_page OFFSET $offset";
    $result = $conn->query($sql);
    $articles = $result->fetch_all(MYSQLI_ASSOC);
    
    $conn->close();
} catch(Exception $e) {
    die("获取文章列表时出错: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章资讯 - 企业网站</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .article-list {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .article-card {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .article-card:last-child {
            border-bottom: none;
        }
        .article-title {
            font-size: 22px;
            margin-bottom: 10px;
        }
        .article-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.2s;
        }
        .article-title a:hover {
            color: #3498db;
        }
        .article-meta {
            color: #777;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .article-excerpt {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .read-more {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            border: 1px solid #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        .pagination a:hover:not(.active) {
            background-color: #f1f1f1;
        }
        .pagination span {
            display: inline-block;
            padding: 8px 0;
            margin: 0 5px;
            color: #777;
        }
        .no-articles {
            text-align: center;
            padding: 40px 0;
        }
        .no-articles p {
            font-size: 18px;
            color: #777;
            margin-bottom: 20px;
        }
        .add-article-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .add-article-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="article-list">
            <h1>文章资讯</h1>
            
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <h2 class="article-title">
                        <a href="article_detail.php?id=<?php echo $article['id']; ?>">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h2>
                    <div class="article-meta">
                        <span>作者: <?php echo htmlspecialchars($article['author_name']); ?></span> | 
                        <span>发布时间: <?php echo date('Y-m-d', strtotime($article['published_at'])); ?></span>
                    </div>
                    <div class="article-excerpt">
                        <?php echo htmlspecialchars($article['excerpt'] ?: substr(strip_tags($article['content']), 0, 150) . '...'); ?>
                    </div>
                    <a href="article_detail.php?id=<?php echo $article['id']; ?>" class="read-more">阅读全文 &raquo;</a>
                </div>
                <?php endforeach; ?>
                
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="article_list.php?page=<?php echo $page - 1; ?>">&laquo; 上一页</a>
                    <?php endif; ?>
                    
                    <?php 
                    // 显示当前页前后各2页的页码
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<a href="article_list.php?page=1">1</a>';
                        if ($start > 2) echo '<span>...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        echo '<a href="article_list.php?page='.$i.'"'.($i == $page ? ' class="active"' : '').'>'.$i.'</a>';
                    }
                    
                    if ($end < $total_pages) {
                        if ($end < $total_pages - 1) echo '<span>...</span>';
                        echo '<a href="article_list.php?page='.$total_pages.'">'.$total_pages.'</a>';
                    }
                    ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="article_list.php?page=<?php echo $page + 1; ?>">下一页 &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-articles">
                    <p>暂无发布的文章</p>
                    <?php if (isAdminLoggedIn()): ?>
                        <a href="admin/add_article.php" class="add-article-btn">添加新文章</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>