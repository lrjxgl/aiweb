<?php include 'header.php'; ?>

    <main class="container">
        <!-- 企业简介 -->
        <section class="intro-section">
            <h2>欢迎来到我们的企业</h2>
            <div class="intro-content">
                <div class="intro-text">
                    <p>我们是一家专注于创新科技解决方案的企业，成立于2010年，致力于为客户提供最优质的产品和服务。</p>
                    <p>我们的核心价值是诚信、创新和卓越，这些理念贯穿于我们工作的每一个环节。</p>
                    <a href="about.php" class="learn-more">了解更多</a>
                </div>
                <div class="intro-image">
                    <img src="https://picsum.photos/500/300?random=101" alt="企业办公环境">
                </div>
            </div>
        </section>

        <!-- 特色服务 -->
        <section class="featured-services">
            <h2>我们的特色服务</h2>
            <div class="services-grid">
                <?php
                require_once 'config.php';
                try {
                    $conn = getDBConnection();
                    $sql = "SELECT * FROM products LIMIT 3";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($service = $result->fetch_assoc()) {
                            echo '
                            <div class="service-card">
                                <img src="' . $service['image_url'] . '" alt="' . $service['name'] . '">
                                <h3>' . $service['name'] . '</h3>
                                <p>' . substr($service['description'], 0, 100) . '...</p>
                                <a href="products.php" class="service-link">查看详情</a>
                            </div>';
                        }
                    }
                    $conn->close();
                } catch(Exception $e) {
                    echo '<p class="error">加载服务数据时出错</p>';
                }
                ?>
            </div>
        </section>

        <!-- 日期显示 -->
        <section class="date-section">
            <?php
                date_default_timezone_set('Asia/Shanghai');
                echo '<p>今天是 ' . date('Y年m月d日') . '，星期' . ['日','一','二','三','四','五','六'][date('w')] . '</p>';
            ?>
        </section>

        <!-- 客户评价 -->
        <section class="testimonials">
            <h2>客户评价</h2>
            <div class="testimonial-slider">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"这家公司的服务非常专业，解决了我们长期以来的技术难题，强烈推荐！"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/80/80?random=201" alt="客户头像">
                        <div>
                            <h4>张先生</h4>
                            <p>ABC公司 CEO</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"产品质量超出预期，售后服务也很到位，是我们长期合作的可靠伙伴。"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/80/80?random=202" alt="客户头像">
                        <div>
                            <h4>李女士</h4>
                            <p>XYZ集团 采购总监</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 最新动态 -->
        <section class="news-section">
            <h2>最新动态</h2>
            <div class="news-grid">
                <div class="news-card">
                    <div class="news-date">2023-11-15</div>
                    <h3>新产品发布会圆满成功</h3>
                    <p>我们于11月15日成功举办了年度新产品发布会，展示了多项创新技术和解决方案...</p>
                    <a href="#" class="read-more">阅读更多</a>
                </div>
                <div class="news-card">
                    <div class="news-date">2023-10-28</div>
                    <h3>荣获行业创新大奖</h3>
                    <p>我们很荣幸地宣布，公司荣获2023年度行业创新大奖，这是对我们技术实力的认可...</p>
                    <a href="#" class="read-more">阅读更多</a>
                </div>
                <div class="news-card">
                    <div class="news-date">2023-10-10</div>
                    <h3>与ABC公司达成战略合作</h3>
                    <p>我们与ABC公司正式签署战略合作协议，双方将在人工智能领域展开深度合作...</p>
                    <a href="#" class="read-more">阅读更多</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>