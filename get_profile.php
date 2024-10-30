<?php
session_start();

// Подключение к базе данных
$servername = "";
$username = "";
$password = "";
$dbname = "";

header('Content-Type: application/json');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]);
    exit();
}

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Пользователь не авторизован"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение информации о пользователе из базы данных
$sql = "SELECT name, nickname, avatar, created_at, followers, following FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Ошибка подготовки запроса: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Ошибка выполнения запроса: " . $stmt->error]);
    exit();
}

$stmt->bind_result($name, $nickname, $avatar, $created_at, $followers, $following);

$user_data = [];
if ($stmt->fetch()) {
    $user_data = [
        'name' => $name,
        'nickname' => $nickname,
        'avatar' => $avatar,
        'created_at' => $created_at,
        'followers' => $followers,
        'following' => $following
    ];
}

// Закрытие текущего запроса
$stmt->close();

// Получение достижений пользователя
$sql = "SELECT icon, description FROM achievements WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Ошибка подготовки запроса для достижений: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Ошибка выполнения запроса для достижений: " . $stmt->error]);
    exit();
}

$stmt->bind_result($icon, $description);

$achievements = [];
while ($stmt->fetch()) {
    $achievements[] = [
        'icon' => $icon,
        'description' => $description
    ];
}

// Добавление достижений в данные пользователя
$user_data['achievements'] = $achievements;

echo json_encode($user_data);

$stmt->close();
$conn->close();
?>
