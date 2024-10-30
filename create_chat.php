<?php
session_start();
header('Content-Type: application/json');

$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die(json_encode(array('error' => 'Ошибка подключения: ' . $conn->connect_error)));
}

// Получение идентификатора текущего пользователя
if (!isset($_SESSION['user_id'])) {
    die(json_encode(array('error' => 'Пользователь не авторизован')));
}

$user_id = $_SESSION['user_id'];
$user2_id = $_POST['user2_id'];

// Запрос на создание нового чата
$sql = "INSERT INTO chats (user1_id, user2_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(array('error' => 'Ошибка подготовки запроса: ' . $conn->error)));
}

$stmt->bind_param("ii", $user_id, $user2_id);

if ($stmt->execute()) {
    $chat_id = $stmt->insert_id;
    echo json_encode(array('success' => true, 'chat_id' => $chat_id));
} else {
    die(json_encode(array('error' => 'Ошибка выполнения запроса: ' . $stmt->error)));
}

$stmt->close();
$conn->close();
?>
