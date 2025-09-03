<?php
session_start();
require_once __DIR__ . "/../internal/database/user/find.php";
require_once __DIR__ . "/../internal/EnvLoader.php";

$passwordError = false;
$wrongCaptcha = false;

$envLoader = new EnvLoader();

$captcha_client_key = $envLoader->getEnv("CAPTCHA_CLIENT_KEY");

if (($_SERVER["REQUEST_METHOD"] == "POST") and (isset($_POST["auth"]))) {
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

        if (!$user) {
            $passwordError = true;
        } else {
            $passwordError = false;
            $_SESSION["userId"] = $user->id;
            $_SESSION["username"] = $user->username;
            $_SESSION["phone"] = $user->phone;
            $_SESSION["email"] = $user->email;

            $ttl = 60 * 60 * 24;
            setcookie("SessionId", session_id(), time() + $ttl,  "/");
            header("Location: /");
            exit();
        }
    }
}


function check_captcha($token) {
    $ch = curl_init("https://smartcaptcha.yandexcloud.net/validate");

    $ip = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    global $envLoader;
    define("SMARTCAPTCHA_SERVER_KEY", $envLoader->getEnv("CAPTCHA_SERVER_KEY"));
    $args = [
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $ip,
    ];

    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }

    $resp = json_decode($server_output);
    return $resp->status === "ok";
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