<?php


require_once __DIR__ . "/../internal/database/Database.php";

$database = new Database();
$db = $database->getConnection();

$migrationFiles = glob(__DIR__ . '/versions/*.php');
rsort($migrationFiles);
$rollbackCount = isset($argv[1]) ? $argv[1] : 1;
$rolledBack = 0;

$i = 0;
while ($rolledBack < $rollbackCount && $i < count($migrationFiles)) {

    $migrationName = basename($migrationFiles[$i], '.php');
    require_once $migrationFiles[$i];
    $downFunc = "down_$migrationName";

    if (function_exists($downFunc)) {
        try {
            $sql = $downFunc();
            $db->exec($sql);

            echo "Откат миграции $migrationName\n";

        } catch(Exception $e) {
            error_log("Ошибка отката миграции: " . $e->getMessage() . "\n");
        }
    }
    $i++;
    $rolledBack++;
}

echo "Откат миграций выполнен";