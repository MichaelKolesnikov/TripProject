<?php
require_once "vendor/connect.php";

$clientId = $_GET['id'];

$query = "INSERT INTO `Trip` (id) VALUES ('$clientId');";
mysqli_query($connect, $query);
$query = "INSERT INTO `Change` (trip_id) VALUES ('$clientId');";
mysqli_query($connect, $query);
header('Location: profile.php');
?>