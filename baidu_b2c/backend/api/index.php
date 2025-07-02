<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config.php';
require_once '../includes/functions.php';

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$table = $request[0] ?? null;

switch ($method) {
    case 'GET':
        if ($table == 'products') {
            $id = $request[1] ?? null;
            if ($id) {
                $result = getProduct($conn, $id);
            } else {
                $result = getProducts($conn);
            }
        } elseif ($table == 'orders') {
            $userId = $request[1] ?? null;
            if ($userId) {
                $result = getOrders($conn, $userId);
            } else {
                $result = ["error" => "User ID is required"];
            }
        } else {
            $result = ["error" => "Invalid request"];
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($table == 'users') {
            $result = createUser($conn, $input);
        } elseif ($table == 'orders') {
            $result = createOrder($conn, $input);
        } else {
            $result = ["error" => "Invalid request"];
        }
        break;
    default:
        $result = ["error" => "Invalid method"];
        break;
}

echo json_encode($result);
$conn->close();
?>