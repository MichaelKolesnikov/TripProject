<?php
session_start();
require_once "vendor/connect.php";

$clientId = $_GET['id'];

mysqli_begin_transaction($connect);

try {
    $stmt = $connect->prepare("SELECT * FROM `Change` WHERE `trip_id` = ? AND NOT `done` FOR UPDATE");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $change_row = $stmt->get_result()->fetch_assoc();

    $manager_id = $_SESSION['user']['id'];
    if ($change_row["manager_id"] != $manager_id && $change_row["manager_id"] != null) {
        echo "Кто-то уже работает над данным изменением";
        mysqli_rollback($connect);
        exit();
    }

    // set only one redactor for this Trip
    $stmt = $connect->prepare("UPDATE `Change` SET manager_id=? WHERE `trip_id`=?;");
    $stmt->bind_param("ii", $manager_id, $clientId);
    $stmt->execute();

    // get data about this trip for redacting
    $stmt = $connect->prepare("SELECT * FROM `User` JOIN `Trip` ON `User`.`id` = `Trip`.`id` WHERE `Trip`.`id` = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    mysqli_commit($connect);
} catch (Exception $e) {
    mysqli_rollback($connect);
    echo "Ошибка: " . $e->getMessage();
}
// COMMIT and ROLLBACK automatically release locks acquired by FOR UPDATE.
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование клиента</title>
</head>

<body>
    <h1>Данные клиента</h1>
    <div>
        <p>Логин: <?php echo $row['login']; ?></p>
        <p>Имя: <?php echo $row['name'] ?></p>
        <p>Ваш телефон: <?php echo $row['phone'] ?></p>
    </div>
    <h1>Данные поездки для редактирования</h1>
    <form method="POST">
        <label for="description">Описание:</label>
        <textarea id="description"
            name="description"><?php echo htmlspecialchars($row['description']); ?></textarea><br>

        <label for="start_date">Дата начала:</label>
        <input type="date" id="start_date" name="start_date"
            value="<?php echo htmlspecialchars($row['start_date']); ?>"><br>

        <label for="end_date">Дата окончания:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($row['end_date']); ?>"><br>

        <label for="need_culture_program">Культурная программа:</label>
        <select id="need_culture_program" name="need_culture_program">
            <option value="1" <?php echo $row['need_culture_program'] == 1 ? 'selected' : ''; ?>>Да</option>
            <option value="0" <?php echo $row['need_culture_program'] == 0 ? 'selected' : ''; ?>>Нет</option>
            <option value="" <?php echo is_null($row['need_culture_program']) ? 'selected' : ''; ?>>Не указано</option>
        </select><br>

        <button type="submit">Сохранить изменения</button>
    </form>
</body>

</html>