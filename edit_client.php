<?php
require_once "vendor/connect.php";

// Получение ID клиента из URL
$clientId = $_GET['id'];

// Если форма отправлена, обновляем данные
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $tripDetails = $_POST['trip_details'];

    // SQL-запрос для обновления данных
    $query = "UPDATE User 
              INNER JOIN Trip ON User.id = Trip.id 
              SET User.name = '$name', User.phone = '$phone', Trip.trip_details = '$tripDetails' 
              WHERE User.id = $clientId";

    if (mysqli_query($connect, $query)) {
        echo "Данные клиента успешно обновлены.";
    } else {
        echo "Ошибка при обновлении данных: " . mysqli_error($connect);
    }
}

$query = "SELECT User.id, User.name, User.phone
          FROM User 
          INNER JOIN Trip ON User.id = Trip.id 
          WHERE User.id = $clientId";
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование клиента</title>
</head>

<body>
    <h1>Редактирование клиента</h1>
    <form method="POST">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required><br><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $row['phone']; ?>" required><br><br>

        <label for="trip_details">Детали поездки:</label>

        <button type="submit">Сохранить изменения</button>
    </form>
</body>

</html>