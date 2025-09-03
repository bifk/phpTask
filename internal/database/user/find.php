<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";

$database = new Database();
$db = $database->getConnection();

function findByPhone($phone, $password) {
    global $db;

    $sql = "SELECT id, username, phone, email, password FROM users WHERE phone = :phone";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":phone", $phone);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt->closeCursor();

    if (!$user || !password_verify($password, $user["password"])) {
        return false;
    }
    return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
}

function findByEmail($email, $password) {
    global $db;

    $sql = "SELECT id, username, phone, email, password FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt->closeCursor();

    if (!$user || !password_verify($password, $user["password"])) {
        return false;
    }
    return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
}

?>