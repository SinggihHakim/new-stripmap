<?php
define('BASE_PATH', dirname(__DIR__));
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\"'");
    }
}
require_once BASE_PATH . '/app/helpers/Autoloader.php';
require_once BASE_PATH . '/app/helpers/Database.php';
require_once BASE_PATH . '/app/helpers/TahunHelper.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT tahun, COUNT(*) as cnt, COUNT(DISTINCT ruas_id) as ruas_cnt FROM penanganan GROUP BY tahun ORDER BY tahun");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
