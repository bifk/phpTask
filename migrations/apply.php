<?php


require_once __DIR__ . "/../internal/database/Database.php";


$database = new Database();
$db = $database->getConnection();



$migrationFiles = glob(__DIR__ . '/versions/*.php');
sort($migrationFiles);

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');
    require_once $file;

    $upFunc = "up_$migrationName";

    if (function_exists($upFunc)) {
        try {
            $sql = $upFunc();
            $db->exec($sql);

            echo "Применение миграции $migrationName\n";
        } catch (Exception $e) {
            error_log("Ошибка применения миграции: " . $e->getMessage() . "\n");
            return;
        }
    }

}
echo "Применение миграций выполнено";

?>