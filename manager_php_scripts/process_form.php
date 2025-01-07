<?php
require_once "../vendor/connect.php";
session_start();
if (!$_SESSION['user']) {
    header('Location: /');
    exit();
}

function updateTrip($conn, $change_id, $start_date, $end_date, $description, $need_visa, $need_transfer, $need_culture_program, $cancelled, $cost, $hotel_id, $trip_id)
{
    $stmt = $conn->prepare("
        UPDATE `Trip` 
        SET 
            start_date = ?, 
            end_date = ?, 
            description = ?, 
            need_visa = ?, 
            need_transfer = ?, 
            need_culture_program = ?, 
            cancelled = ?, 
            cost = ?, 
            hotel_id = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("sssiiiidii", $start_date, $end_date, $description, $need_visa, $need_transfer, $need_culture_program, $cancelled, $cost, $hotel_id, $trip_id);

    $stmt->execute();
    $stmt1 = $conn->prepare("UPDATE `Change` SET done=true WHERE id=?");
    $stmt1->bind_param("i", $change_id);
    $stmt1->execute();

    $stmt->close();
    $stmt1->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $change_id = $_POST['change_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];
    $need_visa = $_POST['need_visa'];
    $need_transfer = $_POST['need_transfer'];
    $need_culture_program = $_POST['need_culture_program'];
    $cancelled = $_POST['cancelled'];
    $cost = $_POST['cost'];
    $hotel_id = $_POST['hotel_id'];
    $trip_id = $_POST['trip_id'];


    updateTrip(
        $connect,
        $change_id,
        $start_date,
        $end_date,
        $description,
        $need_visa,
        $need_transfer,
        $need_culture_program,
        $cancelled,
        $cost,
        $hotel_id,
        $trip_id
    );
    header('Location: /');
}
?>