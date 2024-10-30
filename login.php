<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "";
    $username = "";
    $password = "";
    $dbname = "";

    // Создание соединения
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Проверка соединения
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Получение данных из формы
    $email = $_POST['email'];
    $password = $_POST['password']; // Хешируем введенный пароль для сравнения с хешем в БД

    // Подготовка и выполнение запроса для выбора пользователя с заданным email
    $sql = "SELECT id, name, nickname, email, password FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Пользователь найден
        $row = $result->fetch_assoc();
        // Проверка введенного пароля с хешем в БД
        if ($password === $row['password']) {
            // Вход успешен
            $_SESSION['user_id'] = $row['id'];
            // Перенаправление на главную страницу
            header('Location: main.php');
            exit();
        } else {
            // Неправильный пароль
            echo json_encode(array("status" => "error", "message" => "Неправильный пароль."));
        }
    } else {
        // Пользователь не найден
        echo json_encode(array("status" => "error", "message" => "Пользователь с таким адресом электронной почты не найден."));
    }

    $conn->close();
}
?>
