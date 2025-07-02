<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // Create connection
    $conn = new mysqli($host, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }

    // Select the database
    $conn->select_db($dbname);

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table users created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }

    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT(6) NOT NULL
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table products created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }

    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    total DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table orders created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }

    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED,
    product_id INT(6) UNSIGNED,
    quantity INT(6) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table order_items created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }

    // Create config file
    $config = "<?php
    define('DB_HOST', '$host');
    define('DB_USERNAME', '$username');
    define('DB_PASSWORD', '$password');
    define('DB_NAME', '$dbname');
    ?>";

    file_put_contents('../backend/config.php', $config);
    echo "Configuration file created successfully<br>";

    $conn->close();
    echo "Installation completed successfully!";
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B2C Shop Installation</title>
</head>
<body>
    <h1>B2C Shop Installation</h1>
    <form method="post">
        <label for="host">Database Host:</label>
        <input type="text" id="host" name="host" required><br><br>
        <label for="username">Database Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Database Password:</label>
        <input type="password" id="password" name="password"><br><br>
        <label for="dbname">Database Name:</label>
        <input type="text" id="dbname" name="dbname" required><br><br>
        <input type="submit" value="Install">
    </form>
</body>
</html>
<?php
}
?>