<?php
session_start();
if (!$_SESSION['user']) {
    header('Location: /');
}
if ($_SESSION['user']['role_id'] == 1) {
    header('Location: /');
}
require_once "../vendor/connect.php";

$clientId = $_GET['id'];

try {
    // Получаем данные о поездке
    $stmt = $connect->prepare("SELECT * FROM `User` JOIN `Trip` ON `User`.`id` = `Trip`.`id` WHERE `Trip`.`id` = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    // Получаем описание изменения, если оно есть
    $stmt = $connect->prepare("SELECT * FROM `Change` WHERE `trip_id` = ? AND `done` = false");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $change_row = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Просмотр поездки</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="/assets/css/edit_client.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Просмотр поездки</h1>
    </header>

    <div class="container">
        <div class="client-info">
            <h2>Данные клиента</h2>
            <p>Логин: <?php echo htmlspecialchars($row['login']); ?></p>
            <p>Имя: <?php echo htmlspecialchars($row['name']); ?></p>
            <p>Телефон: <?php echo htmlspecialchars($row['phone']); ?></p>
            <?php if ($change_row): ?>
                <p>Описание желаемого изменения: <?php echo htmlspecialchars($change_row['description']); ?></p>
            <?php endif; ?>
        </div>

        <div class="trip-info">
            <h2>Данные поездки</h2>
            <form>
                <label for="start_date">Дата начала:</label>
                <input type="date" id="start_date" name="start_date"
                    value="<?php echo htmlspecialchars($row['start_date']); ?>" readonly><br>

                <label for="end_date">Дата окончания:</label>
                <input type="date" id="end_date" name="end_date"
                    value="<?php echo htmlspecialchars($row['end_date']); ?>" readonly><br>

                <label for="description">Описание:</label>
                <textarea id="description" name="description"
                    readonly><?php echo htmlspecialchars($row['description']); ?></textarea><br>

                <label for="need_visa">Нужна виза:</label>
                <select id="need_visa" name="need_visa" disabled>
                    <option value="1" <?php echo $row['need_visa'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_visa'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="need_transfer">Нужен трансфер:</label>
                <select id="need_transfer" name="need_transfer" disabled>
                    <option value="1" <?php echo $row['need_transfer'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_transfer'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="need_culture_program">Культурная программа:</label>
                <select id="need_culture_program" name="need_culture_program" disabled>
                    <option value="1" <?php echo $row['need_culture_program'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_culture_program'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="cancelled">Отменена:</label>
                <select id="cancelled" name="cancelled" disabled>
                    <option value="1" <?php echo $row['cancelled'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['cancelled'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="cost">Стоимость:</label>
                <input type="number" id="cost" name="cost" step="0.01"
                    value="<?php echo htmlspecialchars($row['cost']); ?>" readonly><br>

                <label for="hotel_id">ID отеля:</label>
                <input type="number" id="hotel_id" name="hotel_id"
                    value="<?php echo htmlspecialchars($row['hotel_id']); ?>" readonly><br>
            </form>
        </div>
    </div>
</body>

</html>