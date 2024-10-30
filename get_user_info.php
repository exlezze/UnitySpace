<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Вы не авторизованы."]);
    exit;
}

$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Ошибка подключения: " . $conn->connect_error]);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT avatar FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(["status" => "success", "avatar" => $user['avatar']]);
} else {
    echo json_encode(["status" => "error", "message" => "Пользователь не найден."]);
}

$conn->close();
?>
