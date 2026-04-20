<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

try {
    $db = db_connect();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION["err"] = "データベースへの接続・送信に失敗しました" .
        $e->getMessage();
    header('location:reserve_check.php');
    exit();
}
