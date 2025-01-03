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
    </header>

    <main>
        <div>
            <?php
            if ($_SESSION['user']['role_id'] == 1) {
                $user_id = $_SESSION['user']['id'];
                $query = "SELECT * FROM Trip WHERE Trip.id='$user_id'";
                $result = mysqli_query($connect, $query);
                if (mysqli_num_rows($result) == 0) {
                    echo "Вы еще не зарегистрировали поездку. Позвоните по данному номеру телефона: +7(888)-888-88-88, чтобы обговорить желаемую поездку. После через некоторое время вы сможете ее увидеть в своем личном кабинете.";
                } else {
                    echo "Информация о Вашей поездке:";
                }
            } else {
                $query = "
    SELECT 
        User.id, 
        User.login, 
        User.name, 
        User.phone, 
        `Change`.done 
    FROM 
        User 
    INNER JOIN 
        Trip ON User.id = Trip.id 
    LEFT JOIN 
        `Change` ON `Change`.`trip_id` = `Trip`.`id` 
    WHERE 
        User.role_id = 1
";
                $result = mysqli_query($connect, $query);

                if (mysqli_num_rows($result) > 0) {
                    // Массивы для хранения клиентов
                    $clientsWithChanges = [];
                    $clientsWithoutChanges = [];

                    // Разделение клиентов на две группы
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['done'] == true) {
                            $clientsWithoutChanges[] = $row;
                        } else {
                            $clientsWithChanges[] = $row;
                        }
                    }

                    // Вывод клиентов, которым нужны изменения
                    echo "<h2>Список клиентов, у которых уже есть поездка:</h2>";
                    echo "Список клиентов, кому нужны изменения:";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>login</th><th>Имя</th><th>Телефон</th><th>Требуется ли изменение?</th></tr>";

                    foreach ($clientsWithChanges as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td><a href='edit_client.php?id=" . htmlspecialchars($row['id']) . "'><button>Редактировать</button></a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";

                    // Вывод клиентов, которым не нужны изменения
                    echo "Список клиентов, кому не нужны изменения:";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>login</th><th>Имя</th><th>Телефон</th></tr>";

                    foreach ($clientsWithoutChanges as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
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
                        echo "<td><a href='create_trip.php?id=" . $row['id'] . "'><button>Добавить поездку</button></a></td>";
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

    <footer>
        <a href="vendor/logout.php" class="logout">Выход</a>
    </footer>
</body>

</html>