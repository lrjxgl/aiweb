<?php include 'header.php'; ?>

    <main class="container">
        <section>
            <h2>联系我们</h2>
            
            <div class="contact-container">
                <div class="contact-info">
                    <h3>联系方式</h3>
                    <ul>
                        <li><strong>地址：</strong>北京市朝阳区某某路123号</li>
                        <li><strong>电话：</strong>010-12345678</li>
                        <li><strong>邮箱：</strong>info@company.com</li>
                        <li><strong>工作时间：</strong>周一至周五 9:00-18:00</li>
                    </ul>
                </div>
                
                <div class="contact-form">
                    <h3>发送消息</h3>
                    <form action="process_form.php" method="post">
                        <div class="form-group">
                            <label for="name">姓名：</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">邮箱：</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">主题：</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">消息内容：</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">发送消息</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?>