<?php
require_once __DIR__ . '/config.php';

// DB接続関数
function db_connect()
{
    try {
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB接続エラー: ' . $e->getMessage());
    }
}

// XSS対策用のエスケープ関数
function h($string)
{
    if (is_null($string)) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
