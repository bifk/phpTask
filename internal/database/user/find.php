<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";

$database = new Database();
$db = $database->getConnection();

// Поиск пользователя
function find($field, $value, $password) {
    global $db;

    $allow = ['phone', 'email'];

    if (!in_array($field, $allow)) {
        return false;
    }

    $sql = "SELECT id, username, phone, email, password FROM users WHERE $field = :value";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":value", $value);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt->closeCursor();

    // Проверка наличия пользователя и корректность пароля
    if (!$user || !password_verify($password, $user["password"])) {
        return false;
    }
    return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
}

?>