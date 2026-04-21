<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

$day  = $_POST['day'] ?? '';
$time = $_POST['time'] ?? '';
$type = $_POST['type'] ?? '';

// 直アクセスした人へ ↓
if (empty($day) || empty($time)) {
    header('Location: reserve.php');
    exit();
}

try {
    $sql = "INSERT INTO 'carcon_reservations`(carcon_reservation_detail_id, carcon_line_id, created_at, updated_at) VALUES (:carcon_reservation_detail_id,:carcon_line_id,:created_at,:updated_at)'";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":carcon_reservation_detail_id", $carcon_reservation_detail_id, PDO::PARAM_STR);
    $stmt->bindParam(":carcon_line_id", $created_at, PDO::PARAM_STR);
    $stmt->bindParam(":updated_at", $updated_at, PDO::PARAM_STR);

    $_SESSION["msg"] = "面談の予約が完了しました！";
    header('location:reserve_check.php');
    exit();

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION["err"] = "データベースへの接続・送信に失敗しました" .
        $e->getMessage();
    header('location:reserve_check.php');
    exit();
}
