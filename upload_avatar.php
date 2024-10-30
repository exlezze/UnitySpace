<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Вы не авторизованы."]);
    exit;
}

$target_dir = "uploads/avatars/";
$target_file = $target_dir . basename($_FILES["avatar"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$check = getimagesize($_FILES["avatar"]["tmp_name"]);
if ($check !== false) {
    $uploadOk = 1;
} else {
    echo json_encode(["status" => "error", "message" => "Файл не является изображением."]);
    $uploadOk = 0;
}

if ($_FILES["avatar"]["size"] > 5000000) {
    echo json_encode(["status" => "error", "message" => "Файл слишком большой."]);
    $uploadOk = 0;
}

if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo json_encode(["status" => "error", "message" => "Разрешены только файлы JPG, JPEG, PNG и GIF."]);
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo json_encode(["status" => "error", "message" => "Ваш файл не был загружен."]);
} else {
    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        $servername = "";
        $username = "";
        $password = "";
        $dbname = "";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $user_id = $_SESSION['user_id'];
        $sql = "UPDATE users SET avatar='$target_file' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Аватарка успешно загружена."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Ошибка при сохранении аватарки в базу данных."]);
        }
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Произошла ошибка при загрузке файла."]);
    }
}
?>
