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

// Получение информации о пользователе из базы данных
$user_id = $_SESSION['user_id'];
$sql = "SELECT avatar FROM users WHERE id = $user_id";
$result = $conn->query($sql);

$avatar = "default_avatar.png"; // Значение по умолчанию, если у пользователя нет аватарки
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $avatar = $row["avatar"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnitySpace - Главная</title>
    <link rel="icon" href="unityspace.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="unityspace.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="unityspace.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="topbar">
        <div class="left-section">
            <img src="unityspace.png" alt="Avatar" class="avatar">
        </div>
        <div class="right-section">
            <input type="text" placeholder="Поиск" class="search-input">
        </div>
        <div class="right-section">
            <img src="<?php echo $avatar; ?>" alt="Avatar" width="30" height="30" class="user-avatar" onclick="openProfileModal()">
        </div>
    </div>

    <div class="content">
        <div class="button-container">
            <button class="menu-button">
                <img src="icons/homee.png" alt="Home">
                <span>Главная</span>
            </button>
            <button class="menu-button">
                <img src="icons/messager.png" alt="Messenger">
                <span>Мессенджер</span>
            </button>
            <button class="menu-button">
                <img src="icons/settings.png" alt="Settings">
                <span>Настройки</span>
            </button>
            <button class="menu-button">
                <img src="icons/vip.png" alt="Subscription">
                <span>Подписка</span>
            </button>
        </div>
    </div>

    <div class="content">
        <div class="info-box">
            <p>Добро пожаловать в UnitySpace!</p>
        </div>
    </div>

    <!-- Модальное окно профиля -->
    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProfileModal()">&times;</span>
            <div id="profile-container">
                <div class="profile-avatar-container">
                    <img id="profile-avatar" src="<?php echo $avatar; ?>" alt="Avatar" class="profile-avatar">
                </div>
                <h2 id="profile-name" class="profile-name">Undefined</h2>
                <h3 id="profile-nickname" class="profile-nickname">@undefined_nickname</h3>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <h4>Подписок</h4>
                        <p id="profile-following">0</p>
                    </div>
                    <div class="profile-stat">
                        <h4>Подписчиков</h4>
                        <p id="profile-followers">0</p>
                    </div>
                </div>
                <p id="profile-date" class="profile-date">Дата регистрации: undefined</p>
                <p id="profile-id" class="profile-id">ID: undefined</p>
            </div>
        </div>
    </div>

    <script>
        function openProfileModal() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_profile.php', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var profile = JSON.parse(this.responseText);
                    if (!profile.error) {
                        document.getElementById('profile-avatar').src = profile.avatar ? profile.avatar : 'default_avatar.png';
                        document.getElementById('profile-name').innerText = profile.name;
                        document.getElementById('profile-nickname').innerText = '@' + profile.nickname;
                        document.getElementById('profile-date').innerText = 'Дата регистрации: ' + profile.created_at;
                        document.getElementById('profile-id').innerText = 'ID: ' + profile.id;
                    } else {
                        console.error(profile.error);
                    }
                }
            };
            xhr.send();
        }

        function closeProfileModal() {
            document.getElementById('profile-modal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('profile-modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        function highlightButton(button) {
            button.classList.toggle("highlighted");
        }
    </script>
</body>
</html>