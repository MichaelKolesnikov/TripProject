<?php
session_start();
if ($_SESSION['user']) {
    header('Location: profile.php');
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="/assets/css/register.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Туристическое агентство</h1>
    </header>

    <div>
        <form class="registration">
            <label>ФИО</label>
            <input type="text" name="name" placeholder="Введите свое имя">
            <label>Логин</label>
            <input type="text" name="login" placeholder="Введите свой логин">
            <label>Телефон</label>
            <input type="phone" name="phone" placeholder="Введите телефон">
            <label>Пароль</label>
            <input type="password" name="password" placeholder="Введите пароль">
            <label>Подтверждение пароля</label>
            <input type="password" name="password_confirm" placeholder="Подтвердите пароль">
            <button type="submit" class="register-btn">Зарегистрироваться</button>
            <p>
                У вас уже есть аккаунт? - <a href="/">авторизируйтесь</a>!
            </p>
            <p class="msg none">Lorem ipsum.</p>
        </form>
    </div>

    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>