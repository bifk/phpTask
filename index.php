<?php
    session_start();
    require_once "Database.php";
    require_once "migrations/migrator.php";
    $db = new Database();
    $con = $db->getConnection();
    migrationsApply();
?>
