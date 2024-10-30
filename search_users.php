<?php
session_start();
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$query = $_GET['q'];

$sql = "SELECT id, nickname, avatar FROM users WHERE nickname LIKE '%$query%' OR id LIKE '%$query%'";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($users);
?>
