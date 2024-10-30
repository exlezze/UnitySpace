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

// Запрос на получение чатов текущего пользователя
$sql = "
    SELECT c.id, u.name, u.avatar 
    FROM chats c 
    JOIN users u ON (u.id = c.user1_id OR u.id = c.user2_id) 
    WHERE (c.user1_id = ? OR c.user2_id = ?) AND u.id != ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(array('error' => 'Ошибка подготовки запроса: ' . $conn->error)));
}

$stmt->bind_param("iii", $user_id, $user_id, $user_id);

if ($stmt->execute()) {
    $stmt->store_result();
    $stmt->bind_result($chat_id, $name, $avatar);
    
    $chats = array();
    while ($stmt->fetch()) {
        $chats[] = array(
            'id' => $chat_id,
            'name' => $name,
            'avatar' => $avatar
        );
    }
    echo json_encode($chats);
} else {
    die(json_encode(array('error' => 'Ошибка выполнения запроса: ' . $stmt->error)));
}

$stmt->close();
$conn->close();
?>
