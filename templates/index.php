<?php


require_once __DIR__ . "/../internal/database/user/create.php";
require_once __DIR__ . "/../internal/database/Database.php";

session_start();
$database = new Database();
$db = $database->getConnection();
$username = "";

if (isset($_SESSION["username"])) {
    $username = $_SESSION['username'];
}

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная страница</title>
</head>
<body>
    <nav>
        <?php if (!isset($_SESSION["userId"])): ?>
            <a href="/registration">Регистрация</a>
            <a href="/auth">Авторизация</a>
        <?php else: ?>
            <a href="/edit"><?php echo htmlspecialchars($username) ?></a>
            <a href="/logout">Выйти</a>
        <?php endif; ?>
    </nav>
</body>
</html>