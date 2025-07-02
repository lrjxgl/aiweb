# 企业网站系统

这是一个基于PHP和MySQL的企业网站系统，包含产品展示和联系表单功能。

## 系统要求
- PHP 7.4+
- MySQL 5.7+
- Web服务器(Apache/Nginx)

## 安装步骤

1. 克隆仓库到您的web目录
```bash
git clone https://example.com/company-website.git
cd company-website
```

2. 创建MySQL数据库并导入初始数据
```bash
mysql -u root -p
> CREATE DATABASE company_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> USE company_website;
> source init_db.sql;
> exit
```

3. 修改配置文件
编辑`config.php`文件，更新数据库连接信息：
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'company_website');
```

4. 设置Web服务器
- 确保网站根目录指向项目目录
- 确保URL重写已启用(用于美观URL)

5. 访问网站
在浏览器中访问您的网站地址

## 功能特点
- 响应式设计，适配各种设备
- 产品展示系统
- 联系表单及管理
- 安全的数据验证和过滤

## 后续开发计划
- 后台管理系统
- 产品管理功能
- 多语言支持
- 用户认证系统