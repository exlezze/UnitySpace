<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Пользователь не авторизован']));
}

$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$chat_id = $_GET['chat_id'];

$sql = "SELECT id, message, created_at FROM messages WHERE chat_id = $chat_id ORDER BY created_at ASC";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode($messages);

$conn->close();
?>
