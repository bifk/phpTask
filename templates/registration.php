<?php
session_start();
require_once __DIR__ . "/../internal/database/user/create.php";

$passwordError = false;

if (($_SERVER["REQUEST_METHOD"] == "POST") and (isset($_POST["register"]))) {
    if ($_POST["password"] != $_POST["password_confirm"]) {
        $passwordError = true;
    } else {
        $userData = [
            'username' => $_POST["username"],
            'phone' => $_POST["phone"],
            'email' => $_POST["email"],
            'password' => password_hash($_POST["password"], PASSWORD_DEFAULT)
        ];
        $userId = createUser($userData)->id;
        if ($userId) {
            $_SESSION["userId"] = $userId;
            $_SESSION["username"] = $userData["username"];
            $_SESSION["phone"] = $userData["phone"];
            $_SESSION["email"] = $userData["email"];

            $ttl = 60 * 60 * 24;
            setcookie("SessionId", session_id(), time() + $ttl,  "/");
            header("Location: /");
            exit();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Регистрация</title>
</head>
<body>
<form action="/registration" method="post">
    <input type="text" name="username" placeholder="Имя пользователя" required/>
    <input type="text" name="phone" placeholder="Телефон" required/>
    <input type="email" name="email" placeholder="Эл. почта" required/>
    <input type="password" name="password" placeholder="Пароль" required/>
    <input type="password" name="password_confirm" placeholder="Повтор пароля" required/>

    <?php if ($passwordError): ?>
        <label style="color: #ff0000; display: block;">Пароли не совпадают</label>
    <?php endif; ?>

    <input type="submit" name="register" value="Зарегистрироваться">
</form>
</body>
</html>