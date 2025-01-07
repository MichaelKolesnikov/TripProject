<?php
session_start();
require_once "../vendor/connect.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $change_id = $data['change_id'];

    mysqli_begin_transaction($connect);

    try {
        $stmt = $connect->prepare("UPDATE `Change` SET manager_id = NULL WHERE id = ?");
        $stmt->bind_param("i", $change_id);
        $stmt->execute();

        mysqli_commit($connect);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($connect);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>