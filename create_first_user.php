<?php
$mysqli = new mysqli("localhost", "root", "", "database");

$query = "SELECT * FROM `User` LIMIT 1";
$result = $mysqli->query($query);

if ($result->num_rows == 0) {
    $hash_password = md5("a");
    $query = "INSERT INTO `User` (id, `login`, `password`, `name`, phone, role_id) VALUES ('1', 'first_manager', '$hash_password', 'first_manager', '+7(888)-888-88-88', '2')";
    $result = $mysqli->query($query);
}

$mysqli->close();
?>