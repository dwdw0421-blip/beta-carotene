<?php
require_once __DIR__ . '/config.php';

// DB接続関数
function db_connect()
{
    try {
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=utf8mb4';
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
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


// 日付のフォーマット用関数
function format_date($datetime, $type)
{
    $format_types = [
        1 => 'Y年m月d日 H:i:s',
        2 => 'Y年m月',
    ];
    return date($format_types[$type], strtotime($datetime));
}
