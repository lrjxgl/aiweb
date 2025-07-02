<!DOCTYPE html>
<html>
<head>
    <title>安装程序</title>
</head>
<body>
    <h1>安装程序</h1>
    <form action="install.php" method="post">
        <label for="host">主机名:</label>
        <input type="text" id="host" name="host" required><br><br>
        <label for="username">用户名:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">密码:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="database">数据库名:</label>
        <input type="text" id="database" name="database" required><br><br>
        <input type="submit" value="安装">
    </form>
</body>
</html>