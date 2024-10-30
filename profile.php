<?php
session_start();

// Подключение к базе данных
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

header('Content-Type: application/json');

// Получение информации о пользователе из базы данных
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, name, nickname, avatar, created_at FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Информация о пользователе не найдена."]);
}

$conn->close();
?>
