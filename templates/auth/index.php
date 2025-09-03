<?php

session_start();

if (isset($_SESSION['userId'])) {
    header('Location: /');
}

require_once __DIR__ . "/../../internal/database/user/find.php";
require_once __DIR__ . "/../../internal/EnvLoader.php";
require_once "captcha.php";

$passwordError = false;
$wrongCaptcha = false;

$envLoader = new EnvLoader();

$captcha_client_key = $envLoader->getEnv("CAPTCHA_CLIENT_KEY");

if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_POST["auth"]))) {
    $token = $_POST['smart-token'];
    if (!check_captcha($token)) {
        $wrongCaptcha = true;
    } else {
        $wrongCaptcha = false;
        $userData = [
            'login' => $_POST["login"],
            'password' => $_POST["password"]
        ];

        $user = false;
        $userPhone = findByPhone($userData['login'], $userData['password']);
        $userEmail = findByEmail($userData['login'], $userData['password']);

        if ($userPhone) {
            $user = $userPhone;
        } else if ($userEmail) {
            $user = $userEmail;
        }

        if (isset($user) && $user) {
            $passwordError = false;
            $_SESSION["userId"] = $user->id;
            $_SESSION["username"] = $user->username;
            $_SESSION["phone"] = $user->phone;
            $_SESSION["email"] = $user->email;

            $ttl = 60 * 60 * 24;
            setcookie("SessionId", session_id(), time() + $ttl,  "/");
            header("Location: /");
            exit();
        } else {
            $passwordError = true;
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
    <title>Авторизация</title>
</head>
<body>
<h1>Авторизация</h1>
<a href="/">Главная страница</a>
<form action="/auth" method="post">
    <input type="text" name="login" placeholder="Телефон / Почта" required/>
    <input type="password" name="password" placeholder="Пароль" required/>


    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>

    <div
        id="captcha-container"
        class="smart-captcha"
        data-sitekey=<?php echo htmlspecialchars($captcha_client_key)?>>
        <input type="hidden" name="smart-token" value="">
    </div>

    <?php if ($passwordError): ?>
        <label style="color: #ff0000; display: block;">Неверный логин и/или пароль</label>
    <?php endif; ?>
    <?php if ($wrongCaptcha): ?>
        <label style="color: #ff0000; display: block;">Некоректно пройдена каптча</label>
    <?php endif; ?>
    <input type="submit" name="auth" value="Войти">
</form>


</body>
</html>