<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'vendor/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_id = $_POST['trip_id'];
    $description = $_POST['description'];

    if ($_SESSION['user']['role_id'] == 1) {
        $stmt = $connect->prepare("INSERT INTO `Change` (trip_id, `description`) VALUES (?, ?)");
        $stmt->bind_param("is", $trip_id, $description);

        if ($stmt->execute()) {
            echo "Запись успешно добавлена!";
        } else {
            echo "Ошибка при добавлении записи: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "У вас нет прав для добавления записи.";
    }
} else {
    echo "Некорректный запрос.";
}
?>