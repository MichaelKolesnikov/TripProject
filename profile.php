<?php
require_once "vendor/connect.php";
session_start();
if (!$_SESSION['user']) {
    header('Location: /');
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>

<body>
    <header>
        <h1>Туристическое агентство</h1>
        <div>
            <p>Ваш логин: <?php echo $_SESSION['user']['login']; ?></p>
            <p>Ваше имя: <?php echo $_SESSION['user']['name']; ?></p>
            <p>Ваш телефон: <?php echo $_SESSION['user']['phone']; ?></p>
            <p>Ваша роль: <?php echo ($_SESSION['user']['role_id'] == 1 ? "Клиент" : "Менеджер") ?> </p>
        </div>
        <a href="vendor/logout.php" class="logout">Выход</a>
    </header>

    <main>
        <div class="form-container">
            <?php
            if ($_SESSION['user']['role_id'] == 1) {
                $user_id = $_SESSION['user']['id'];
                $query = "SELECT Trip.*, Hotel.name, Hotel.country, Hotel.city, Hotel.street FROM Trip JOIN Hotel ON hotel_id=Hotel.id WHERE Trip.id='$user_id'";
                $result = mysqli_query($connect, $query);
                if (mysqli_num_rows($result) == 0) {
                    echo "Вы еще не зарегистрировали поездку. Позвоните по данному номеру телефона: +7(888)-888-88-88, чтобы обговорить желаемую поездку. После через некоторое время вы сможете ее увидеть в своем личном кабинете.";
                } else {
                    echo "Информация о Вашей поездке:";
                    $row = mysqli_fetch_assoc($result);
                    echo "<p>ID поездки: " . $row['id'] . "</p>";
                    echo "<p>Дата начала: " . $row['start_date'] . "</p>";
                    echo "<p>Дата окончания: " . $row['end_date'] . "</p>";
                    echo "<p>Описание: " . $row['description'] . "</p>";
                    echo "<p>Необходимость визы: " . ($row['need_visa'] ? 'Да' : 'Нет') . "</p>";
                    echo "<p>Необходимость трансфера: " . ($row['need_transfer'] ? 'Да' : 'Нет') . "</p>";
                    echo "<p>Необходимость культурной программы: " . ($row['need_culture_program'] ? 'Да' : 'Нет') . "</p>";
                    echo "<p>Отменена: " . ($row['cancelled'] ? 'Да' : 'Нет') . "</p>";
                    echo "<p>Стоимость: " . $row['cost'] . "</p>";
                    echo "<p>Отель: " . $row['name'] . " (Страна: " . $row['country'] . ", Город: " . $row['city'] . ", Улица: " . $row['street'] . ")</p>";

                    echo "Поездка может иметь только одно несделанное изменение";
                    $query = "SELECT * FROM `Change` WHERE trip_id='$user_id' AND done=false;";
                    if (mysqli_num_rows(mysqli_query($connect, $query)) == 0) {
                        echo "<h3>Добавить запись о изменении поездки:</h3>";
                        echo "<form method='POST' action='add_change.php'>";
                        echo "<input type='hidden' name='trip_id' value='" . $row['id'] . "'>";
                        echo "<textarea name='description' placeholder='Введите описание изменения' required></textarea><br>";
                        echo "<button type='submit'>Добавить запись</button>";
                        echo "</form>";
                    }
                }
            } else {
                $query = "
SELECT 
    User.id, 
    User.login, 
    User.name, 
    User.phone, 
    MAX(`Change`.done = false) AS has_not_done_change
FROM 
    User 
INNER JOIN 
    Trip ON User.id = Trip.id 
LEFT JOIN 
    `Change` ON `Change`.`trip_id` = `Trip`.`id` 
WHERE 
    User.role_id = 1
GROUP BY 
    User.id, User.login, User.name, User.phone;
";
                $result = mysqli_query($connect, $query);

                if (mysqli_num_rows($result) > 0) {
                    // Массивы для хранения клиентов
                    $clientsWithChanges = [];
                    $clientsWithoutChanges = [];

                    // Разделение клиентов на две группы
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['has_not_done_change'] == true) {
                            $clientsWithChanges[] = $row;
                        } else {
                            $clientsWithoutChanges[] = $row;
                        }
                    }

                    // Вывод клиентов, которым нужны изменения
                    echo "<h2>Список клиентов, у которых уже есть поездка:</h2>";
                    echo "Список клиентов, кому нужны изменения:";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>login</th><th>Имя</th><th>Телефон</th><th>Требуется ли изменение?</th><th>Информация о поездке</th></tr>";

                    foreach ($clientsWithChanges as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td><a href='manager_php_scripts/edit_client.php?id=" . htmlspecialchars($row['id']) . "'><button>Редактировать</button></a></td>";
                        echo "<td><a href='manager_php_scripts/watch_trip_info.php?id=" . htmlspecialchars($row['id']) . "'><button>Посмотреть</button></a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";

                    // Вывод клиентов, которым не нужны изменения
                    echo "Список клиентов, кому не нужны изменения:";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>login</th><th>Имя</th><th>Телефон</th><th>Информация о поездке</th></tr>";

                    foreach ($clientsWithoutChanges as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td><a href='manager_php_scripts/watch_trip_info.php?id=" . htmlspecialchars($row['id']) . "'><button>Посмотреть</button></a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Нет клиентов с поездками";
                }

                $query = "SELECT User.id, User.login, User.name, User.phone FROM User LEFT JOIN Trip ON User.id = Trip.id WHERE User.role_id = 1 AND Trip.id IS NULL";
                $result = mysqli_query($connect, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<h2>Список клиентов, у которых нет поездки:</h2>";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>login</th><th>Имя</th><th>Телефон</th><th>Действие</th></tr>";

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['login'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['phone'] . "</td>";
                        echo "<td><a href='/manager_php_scripts/create_trip.php?id=" . $row['id'] . "'><button>Добавить поездку</button></a></td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "Нет клиентов без поездок";
                }
            }
            ?>
        </div>
    </main>
</body>

</html>