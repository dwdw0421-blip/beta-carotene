<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

$carcon_reservation_detail_id = $_POST['type_id'] ?? '';
$carcon_line_id = $_POST['line_id'] ?? '';

// $stmt = $db->query($sql);
// $day  = $_POST['day'] ?? '';
// $time = $_POST['time'] ?? '';
// $type = $_POST['radioDefault'] ?? '';

// 直アクセスした人へ ↓
if (empty($carcon_reservation_detail_id) || empty($carcon_line_id)) {
    $_SESSION["err"] = "予約データが正しく送信されませんでした。";
    header('Location: reserve_check.php');
    exit();
}

try {
    $sql = "INSERT INTO carcon_reservations 
            (carcon_reservation_detail_id, carcon_line_id, created_at, updated_at) 
            VALUES 
            (:carcon_reservation_detail_id, :carcon_line_id, :created_at, :updated_at)";
    // $sql = "INSERT INTO carcon_reservations 
    //         (carcon_reservation_detail_id, carcon_line_id, created_at, updated_at) 
    //         VALUES 
    //         (:carcon_reservation_detail_id, :carcon_line_id, :created_at, :updated_at)";

    // $sql = 'SELECT 
    // carcon_reservation_details.student_id as student_id,
    // carcon_reservation_details.meeting_type as meeting_type_id,
    // m_meeting_types.name as meeting_type_name,
    // carcon_reservation_details.slot_index as slot_index,
    // carcon_reservation_details.is_plus_carcon as is_plus_carcon,
    // carcon_lines.date as date
    // FROM carcon_reservation_details 
    // INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
    // INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    // INNER JOIN m_meeting_types ON carcon_reservation_details.meeting_type = m_meeting_types.id
    // WHERE carcon_reservation_details.student_id = :student_id
    // ORDER BY carcon_lines.date';
    $stmt = $db->prepare($sql);
    $now = date('Y-m-d H:i:s');

    $stmt->bindValue(":carcon_reservation_detail_id", $carcon_reservation_detail_id, PDO::PARAM_INT);
    $stmt->bindValue(":carcon_line_id", $carcon_line_id, PDO::PARAM_INT);
    $stmt->bindValue(":created_at", $now, PDO::PARAM_STR);
    $stmt->bindValue(":updated_at", $now, PDO::PARAM_STR);

    $stmt->execute();

    $_SESSION["msg"] = "予約が完了しました";
    header('location:reserve_check.php');
    exit();
} catch (PDOException $e) {
    $_SESSION["err"] = "登録に失敗しました: " . $e->getMessage();
    header('location:reserve_check.php');
    exit();
}
