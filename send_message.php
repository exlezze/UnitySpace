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

$chat_id = $_POST['chat_id'];
$sender_id = $_SESSION['user_id'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (chat_id, sender_id, message) VALUES ($chat_id, $sender_id, '$message')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Ошибка отправки сообщения: ' . $conn->error]);
}

$conn->close();
?>

