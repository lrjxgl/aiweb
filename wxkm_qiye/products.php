<?php include 'header.php'; ?>

    <main class="container">
        <section>
            <h2>我们的产品与服务</h2>
            
            <div class="products-grid">
                <?php
                require_once 'config.php';
                try {
                    $conn = getDBConnection();
                    $sql = "SELECT * FROM products";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($product = $result->fetch_assoc()) {
                            echo '
                            <div class="product-card">
                                <a href="product_detail.php?id='.$product['id'].'">
                                    <img src="' . $product['image_url'] . '" alt="' . $product['name'] . '">
                                    <h3>' . $product['name'] . '</h3>
                                    <p>' . substr($product['description'], 0, 100) . '...</p>
                                    <div class="price">¥' . number_format($product['price'], 2) . '</div>
                                </a>
                            </div>';
                        }
                    } else {
                        echo '<p class="no-products">暂无产品数据</p>';
                    }
                    $conn->close();
                } catch(Exception $e) {
                    echo '<p class="error">加载产品数据时出错: ' . $e->getMessage() . '</p>';
                }
                ?>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?>