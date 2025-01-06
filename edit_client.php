<?php
session_start();
require_once "vendor/connect.php";

$clientId = $_GET['id'];

mysqli_begin_transaction($connect);

try {
    $stmt = $connect->prepare("SELECT * FROM `Change` WHERE `trip_id` = ? AND `done` = false FOR UPDATE");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        mysqli_rollback($connect);
        echo "Изменение уже было применено или поездки уже не существует";
        exit();
    }

    $change_row = $result->fetch_assoc();
    $change_id = $change_row["id"];

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
    exit();
}
// COMMIT and ROLLBACK automatically release locks acquired by FOR UPDATE.
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование клиента</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .client-info,
        .trip-info {
            margin-bottom: 20px;
        }

        .client-info h2,
        .trip-info h2 {
            margin-top: 0;
            color: #007BFF;
        }

        .client-info p,
        .trip-info p {
            margin: 10px 0;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        form input[type="date"],
        form input[type="number"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        form textarea {
            resize: vertical;
            height: 100px;
        }

        form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #218838;
        }

        .logout {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 20px;
        }

        .logout:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        let inactivityTime = function () {
            let time;

            // Функция для отправки запроса на освобождение изменения
            function releaseChange() {
                fetch('time_out_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ change_id: <?php echo $change_id; ?> })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Изменение освобождено');
                        } else {
                            console.error('Ошибка при освобождении изменения');
                        }
                    })
                    .catch(error => console.error('Ошибка:', error));
            }

            // Функция для выхода (переход на profile.php)
            function logout() {
                releaseChange(); // Освобождаем изменение перед выходом
                window.location.href = 'profile.php';
            }

            // Сброс таймера неактивности
            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(logout, 1 * 60 * 1000); // 1 минута
            }

            // Обработка событий активности пользователя
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;

            // Обработка события beforeunload (закрытие страницы или браузера)
            window.addEventListener('beforeunload', function (e) {
                // Отправляем запрос на освобождение изменения
                releaseChange();
            });
        };

        inactivityTime();
    </script>
</head>

<body>
    <header>
        <h1>Редактирование клиента</h1>
    </header>

    <div class="container">
        <div class="client-info">
            <h2>Данные клиента</h2>
            <p>Логин: <?php echo htmlspecialchars($row['login']); ?></p>
            <p>Имя: <?php echo htmlspecialchars($row['name']); ?></p>
            <p>Телефон: <?php echo htmlspecialchars($row['phone']); ?></p>
            <p>Описание желаемого изменения: <?php echo htmlspecialchars($change_row['description']); ?></p>
        </div>

        <div class="trip-info">
            <h2>Данные поездки для редактирования</h2>
            <form method="POST" action="process_form.php">
                <!-- Скрытые поля для передачи ID изменения и менеджера -->
                <input type="hidden" name="change_id" value="<?php echo htmlspecialchars($change_id); ?>">
                <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($row['id']); ?>">

                <label for="start_date">Дата начала:</label>
                <input type="date" id="start_date" name="start_date"
                    value="<?php echo htmlspecialchars($row['start_date']); ?>"><br>

                <label for="end_date">Дата окончания:</label>
                <input type="date" id="end_date" name="end_date"
                    value="<?php echo htmlspecialchars($row['end_date']); ?>"><br>

                <label for="description">Описание:</label>
                <textarea id="description"
                    name="description"><?php echo htmlspecialchars($row['description']); ?></textarea><br>

                <label for="need_visa">Нужна виза:</label>
                <select id="need_visa" name="need_visa">
                    <option value="1" <?php echo $row['need_visa'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_visa'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="need_transfer">Нужен трансфер:</label>
                <select id="need_transfer" name="need_transfer">
                    <option value="1" <?php echo $row['need_transfer'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_transfer'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="need_culture_program">Культурная программа:</label>
                <select id="need_culture_program" name="need_culture_program">
                    <option value="1" <?php echo $row['need_culture_program'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['need_culture_program'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="cancelled">Отменена:</label>
                <select id="cancelled" name="cancelled">
                    <option value="1" <?php echo $row['cancelled'] == 1 ? 'selected' : ''; ?>>Да</option>
                    <option value="0" <?php echo $row['cancelled'] == 0 ? 'selected' : ''; ?>>Нет</option>
                </select><br>

                <label for="cost">Стоимость:</label>
                <input type="number" id="cost" name="cost" step="0.01"
                    value="<?php echo htmlspecialchars($row['cost']); ?>"><br>

                <label for="hotel_id">ID отеля:</label>
                <input type="number" id="hotel_id" name="hotel_id"
                    value="<?php echo htmlspecialchars($row['hotel_id']); ?>"><br>

                <button type="submit">Сохранить изменения</button>
            </form>
        </div>
    </div>
</body>

</html>