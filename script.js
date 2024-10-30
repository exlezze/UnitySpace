// Функция для регистрации
function register(event) {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const nickname = document.getElementById('nickname').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name, nickname, email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('modal-text').innerText = data.message;
            document.getElementById('modal').style.display = 'block';
            setTimeout(() => {
                window.location.href = 'login.html'; // Перенаправление на страницу входа
            }, 2000);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при отправке запроса:', error);
        alert('Произошла ошибка. Попробуйте снова.');
    });
}

// Добавление слушателей событий на формы
document.getElementById('register-form').addEventListener('submit', register);

// Закрытие модального окна
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('modal').style.display = 'none';
});



function login(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('modal-text').innerText = data.message;
            document.getElementById('modal').style.display = 'block';
            setTimeout(() => {
                window.location.href = 'main.php'; // Перенаправление на панель пользователя
            }, 2000);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при отправке запроса:', error);
        alert('Произошла ошибка. Попробуйте снова.');
    });
}

// Добавление слушателей событий на формы
document.getElementById('login-form').addEventListener('submit', login);

// Закрытие модального окна
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('modal').style.display = 'none';
});

document.addEventListener('DOMContentLoaded', function() {
    fetch('get_user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('userAvatar').src = data.avatar;
            } else {
                alert(data.message);
            }
        });
});
