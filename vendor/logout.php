<?php
session_start();
$login = $_SESSION['user']['login'];
unset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <script>
        document.cookie = "user_login=<?php echo $login; ?>; max-age=20; path=/";
        window.location.href = '../index.php';
    </script>
</head>

<body>
</body>

</html>