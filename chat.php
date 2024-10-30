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

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $avatar = $row["avatar"];
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение информации о пользователе из базы данных
$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM users WHERE id = $user_id";
$result = $conn->query($sql);


$name = "Чат не выбран";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnitySpace - Мессенджер</title>
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
            <input type="text" placeholder="Поиск" class="search-input" id="search-input">
        </div>
        <div class="right-section">
            <img src="<?php echo $avatar; ?>" alt="Avatar" width="30" height="30" class="user-avatar" onclick="openProfileModal()">
        </div>
    </div>

    <div id="search-results-container" class="search-results-container"></div>

    <div class="messenger-window">
        <div class="chats-section">
            <h2>Loading...</h2>
            <div class="chat-item" onclick="openChat(1)">
                <img src="icons/chat.png" alt="Favorite">
                <span>Loading...</span>
                <img src="icons/chat.png" alt="Favorite">
                <span>Loading...</span>
            </div>
        </div>
        <div class="messages-section">
            <div class="chat-header">
                <span>Чат не выбран.</span>
            </div>
            <div class="chat-messages">
                <!-- Сообщения будут динамически добавлены здесь -->
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Введите сообщение...">
                <button class="send-button" onclick="sendMessage()">&#9658;</button>
            </div>
        </div>
    </div>

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
                <div id="achievements-container" class="achievements-container"></div>
            </div>
        </div>
    </div>

    <script>
        let currentChatId = null;

        document.getElementById('search-input').addEventListener('input', function() {
            const query = this.value;
        
            const resultsContainer = document.getElementById('search-results-container');
            if (query.length < 1) {
                resultsContainer.innerHTML = '';
                resultsContainer.style.display = 'none';
                return;
            }
        
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_users.php?q=' + encodeURIComponent(query), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    resultsContainer.innerHTML = response.map(user => 
                        `<div class='user-result'>
                            <img src='${user.avatar}' alt='Avatar' class='user-avatar'>
                            <span class='user-nickname'>${user.nickname}</span>
                            <button class='user-action-button' onclick='startNewChat(${user.id})'>
                                <img src='/icons/newchat.png' alt='New Chat'>
                            </button>
                            <button class='user-action-button' onclick='addFriend(${user.id})'>
                                <img src='/icons/addfriend.png' alt='Add Friend'>
                            </button>
                        </div>`).join('');
                    resultsContainer.style.display = 'block';
                } else {
                    console.error('Error: ' + xhr.status);
                    resultsContainer.innerHTML = `<div class='error-message'>Error: ${xhr.status}</div>`;
                }
            };
            xhr.onerror = function() {
                console.error('Request failed');
                resultsContainer.innerHTML = `<div class='error-message'>Request failed</div>`;
            };
            xhr.send();
        });

        function startNewChat(userId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'create_chat.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        openChat(response.chat_id);
                    } else {
                        console.error('Error:', response.error);
                    }
                }
            };
            xhr.send(`user2_id=${userId}`);
        }

        function openChat(chatId) {
            currentChatId = chatId;
            const chatMessagesContainer = document.querySelector('.chat-messages');
            chatMessagesContainer.innerHTML = '';

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_messages.php?chat_id=' + chatId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const messages = JSON.parse(xhr.responseText);
                        if (Array.isArray(messages)) {
                            messages.forEach(message => {
                                const messageClass = message.user_id == <?php echo $_SESSION['user_id']; ?> ? 'my-message' : 'other-message';
                                chatMessagesContainer.innerHTML += `
                                    <div class='message ${messageClass}'>
                                        <span class='message-text'>${message.message}</span>
                                        <span class='message-time'>${message.created_at}</span>
                                    </div>
                                `;
                            });
                            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight; // Прокрутка к последнему сообщению
                        } else {
                            console.error('Received data is not an array:', messages);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                } else {
                    console.error('Error fetching messages:', xhr.status, xhr.statusText);
                }
            };
            xhr.send();
        }

        function sendMessage() {
            const messageInput = document.querySelector('.chat-input input');
            const message = messageInput.value;
            if (!message || !currentChatId) return;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        messageInput.value = '';
                        openChat(currentChatId);
                    } else {
                        console.error('Error:', response.error);
                    }
                }
            };
            xhr.send(`chat_id=${currentChatId}&message=${encodeURIComponent(message)}`);
        }

        function openProfileModal() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_profile.php', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    const profile = JSON.parse(this.responseText);
                    if (!profile.error) {
                        document.getElementById('profile-avatar').src = profile.avatar ? profile.avatar : 'default_avatar.png';
                        document.getElementById('profile-name').innerText = profile.name;
                        document.getElementById('profile-nickname').innerText = '@' + profile.nickname;
                        document.getElementById('profile-date').innerText = 'Дата регистрации: ' + profile.created_at;
                        document.getElementById('profile-id').innerText = 'ID: ' + <?php echo $_SESSION['user_id']; ?>;
                        document.getElementById('profile-followers').innerText = profile.followers;
                        document.getElementById('profile-following').innerText = profile.following;
        
                        // Отображение достижений
                        const achievementsContainer = document.getElementById('achievements-container');
                        achievementsContainer.innerHTML = '';
                        profile.achievements.forEach(achievement => {
                            const achievementElement = document.createElement('div');
                            achievementElement.className = 'achievement';
                            achievementElement.innerHTML = `<img src="${achievement.icon}" alt="${achievement.description}" class = "verified" title="${achievement.description}">`;
                            achievementsContainer.appendChild(achievementElement);
                        });
        
                        document.getElementById('profile-modal').style.display = 'block';
                    } else {
                        console.error(profile.error);
                    }
                } else {
                    console.error('Error fetching profile:', this.status);
                }
            };
            xhr.onerror = function() {
                console.error('Request error');
            };
            xhr.send();
        }

        function closeProfileModal() {
            document.getElementById('profile-modal').style.display = 'none';
        }

        function updateChatList() {
            const chatListContainer = document.querySelector('.chats-section');
            chatListContainer.innerHTML = '';

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_chats.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const chats = JSON.parse(xhr.responseText);
                        if (Array.isArray(chats)) {
                            chats.forEach(chat => {
                                chatListContainer.innerHTML += `
                                    <div class='chat-item' onclick='openChat(${chat.id})'>
                                        <img src='${chat.avatar}' alt='${chat.name}' class='avatar'>
                                        <span>${chat.name}</span>
                                    </div>
                                `;
                            });
                        } else {
                            console.error('Received data is not an array:', chats);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                } else {
                    console.error('Error fetching chats:', xhr.status, xhr.statusText);
                }
            };
            xhr.send();
        }

        window.onload = function() {
            updateChatList();
        };
    </script>
</body>
</html>