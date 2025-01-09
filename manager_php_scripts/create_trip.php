<?php
require_once "../vendor/connect.php";
session_start();

$clientId = $_GET['id'];

try {
    $query = "INSERT INTO `Trip` (id) VALUES ('$clientId');";
    mysqli_query($connect, $query);
} catch (Exception $e) {
    $_SESSION['message'] = "Уже кто-то добавил поездку этому клиенту";
}

header('Location: ../profile.php');
?>