<?php
session_start();
require_once 'vendor/connect.php';
if (!$_SESSION['user']['role_id'] == 2) {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_id = $_POST['trip_id'];
    $description = $_POST['description'];

    $stmt = $connect->prepare("SELECT * FROM `Change` WHERE trip_id=?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = "У вас уже есть изменение, не надо ломать наш сайт, пожалуйста)";
        $stmt->close();
        header("Location: profile.php");
        exit();
    }


    $stmt = $connect->prepare("INSERT INTO `Change` (trip_id, `description`) VALUES (?, ?)");
    $stmt->bind_param("is", $trip_id, $description);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Запись успешно добавлена!"; // Сохраняем сообщение в сессии
    } else {
        $_SESSION['message'] = "Ошибка при добавлении записи: " . $stmt->error; // Сохраняем ошибку в сессии
    }

    $stmt->close();

    header("Location: profile.php");
    exit();
} else {
    $_SESSION['message'] = "Некорректный запрос."; // Сохраняем сообщение в сессии
    header("Location: profile.php");
    exit();
}
?>