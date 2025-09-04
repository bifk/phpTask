<?php
session_start();
require_once __DIR__ . "/../internal/database/user/update.php";
if (!isset($_SESSION['userId'])) {
    header('Location: /');
    exit();
}


$editError = false;
if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_POST["edit"]))) {

    // Проверка на неизмененные или пустые данные
    $userData = [
        'username' => $_POST["username"] === $_SESSION["username"] || $_POST["username"] === "" ? "" : $_POST["username"],
        'phone' =>$_POST["phone"] === $_SESSION["phone"] || $_POST["phone"] === "" ? "" : $_POST["phone"],
        'email' => $_POST["email"] === $_SESSION["email"] || $_POST["email"] === "" ? "" : $_POST["email"],
        'password' => $_POST['password'] === "" ? "" : password_hash($_POST["password"], PASSWORD_DEFAULT)
    ];
    try {
        $user = updateUser($_SESSION['userId'], $userData);
    } catch (Exception $e) {
        $editError = $e->getMessage();
    }
    if (isset($user) && $user) {
        $_SESSION["username"] = $user->username;
        $_SESSION["phone"] = $user->phone;
        $_SESSION["email"] = $user->email;

        // Срок действия сессии - 24 часа
        $ttl = 60 * 60 * 24;
        setcookie("SessionId", session_id(), time() + $ttl,  "/");
        header("Location: /");
        exit();
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
    <title>Редактирование профиля</title>
</head>
<body>
<h1>Редактирование профиля</h1>
<a href="/">Главная страница</a>
    <form action="/edit" method="post">
        <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['username']) ?>" placeholder="Имя пользователя">
        <input type="text" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone']) ?>" placeholder="Телефон">
        <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']) ?>" placeholder="Эл. почта">
        <input type="password" name="password" value="" placeholder="Пароль">
        <?php if ($editError): ?>
            <label style="color: #ff0000; display: block;"><?php echo htmlspecialchars($editError) ?></label>
        <?php endif; ?>

        <input type="submit" name="edit" value="Изменить профиль">
    </form>
</body>
</html>
