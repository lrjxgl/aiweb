<?php
require_once 'config.php';
require_once 'functions.php';

// 验证产品ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = intval($_GET['id']);

try {
    $conn = getDBConnection();
    
    // 获取产品详情
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: products.php');
        exit;
    }
    
    // 获取相关产品
    $related = $conn->query("
        SELECT id, name, image_url 
        FROM products 
        WHERE id != $id 
        ORDER BY RAND() 
        LIMIT 4
    ")->fetch_all(MYSQLI_ASSOC);
    
    $conn->close();
} catch(Exception $e) {
    die("获取产品详情时出错: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - 企业网站</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-detail {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }
        .product-image {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .product-image img {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-info {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .product-title {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }
        .product-price {
            font-size: 24px;
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .product-meta {
            color: #777;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .product-description {
            line-height: 1.8;
            color: #444;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .product-specs {
            margin-bottom: 30px;
        }
        .product-specs h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #333;
        }
        .product-specs ul {
            list-style-type: none;
            padding-left: 0;
        }
        .product-specs li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .product-specs li:before {
            content: "•";
            color: #3498db;
            position: absolute;
            left: 0;
        }
        .product-actions {
            margin-top: 30px;
        }
        .back-to-products {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .back-to-products:hover {
            background-color: #e9ecef;
        }
        .edit-product {
            display: inline-block;
            margin-left: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .edit-product:hover {
            background-color: #2980b9;
        }
        .related-products {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .related-products h2 {
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
            color: #333;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .related-item {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .related-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .related-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .related-item-info {
            padding: 15px;
        }
        .related-item-title {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        .related-item-link {
            display: inline-block;
            margin-top: 10px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
        .related-item-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="product-detail">
            <div class="product-container">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-price">¥<?php echo number_format($product['price'], 2); ?></div>
                    
                    <div class="product-meta">
                        <span>产品编号: <?php echo $product['id']; ?></span>
                        <span>库存: <?php echo $product['stock'] > 0 ? '有货' : '缺货'; ?></span>
                    </div>
                    
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                    
                    <?php if (!empty($product['specifications'])): ?>
                    <div class="product-specs">
                        <h3>产品规格</h3>
                        <ul>
                            <?php 
                            $specs = explode("\n", $product['specifications']);
                            foreach ($specs as $spec): 
                                if (trim($spec)): ?>
                                    <li><?php echo htmlspecialchars(trim($spec)); ?></li>
                                <?php endif;
                            endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-actions">
                        <a href="products.php" class="back-to-products">&laquo; 返回产品列表</a>
                        <?php if (isAdminLoggedIn()): ?>
                            <a href="admin/edit_product.php?id=<?php echo $product['id']; ?>" class="edit-product">编辑产品</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if (count($related) > 0): ?>
            <div class="related-products">
                <h2>相关产品推荐</h2>
                <div class="related-grid">
                    <?php foreach ($related as $item): ?>
                    <div class="related-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="related-item-info">
                            <div class="related-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                            <a href="product_detail.php?id=<?php echo $item['id']; ?>" class="related-item-link">查看详情 &raquo;</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>