<?php
session_start();

if ($_SESSION['user']) {
    header('Location: profile.php');
}
$login_value = isset($_COOKIE['user_login']) ? $_COOKIE['user_login'] : '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="/assets/css/index.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Туристическое агентство</h1>
    </header>

    <div>
        <form class="login">
            <label>Логин</label>
            <input type="text" name="login" placeholder="Введите свой логин"
                value="<?php echo htmlspecialchars($login_value); ?>">
            <label>Пароль</label>
            <input type="password" name="password" placeholder="Введите пароль">
            <button type="submit" class="login-btn">Войти</button>
            <p>
                У вас нет аккаунта? - <a href="/register.php">зарегистрируйтесь</a>!
            </p>
            <p class="msg none">Lorem ipsum dolor sit amet.</p>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class=msg>" . $_SESSION['message'] . "</div>"; // Выводим сообщение
                unset($_SESSION['message']); // Удаляем сообщение из сессии, чтобы оно не отображалось повторно
            }
            ?>
        </form>
    </div>

    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>