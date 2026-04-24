<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';

// ログインしていない場合はログイン画面へ
if (!isset($_SESSION['id'])) {
    header('location:login.php');
    exit();
}

$db = db_connect();

$student_id = $_SESSION['id'] ?? null;
$date       = $_POST['day'] ?? "";
$slot_index = isset($_POST['time']) ? (int)$_POST['time'] : "";
$meeting_type = $_POST['type'] ?? "";

// ログインチェック
if (!$student_id) {
    header("Location: login.php");
    exit();
}

// データ送信失敗
if ($date === "" || $slot_index === "" || $meeting_type === "") {
    $_SESSION["err"] = "予約データが正しく送信されませんでした。";
    header('Location: reserve.php');
    exit();
}

$meeting_url = "#";
$meeting_id = "000 0000 000";
$meeting_passcode = "000000";
$is_plus_carcon = 1; // ← キャリコンプラス

// ==============================
// 予約登録処理
// ==============================
try {
    $db->beginTransaction();

    //空いている枠を検索
    $search_empty_line_sql = "
        SELECT
            carcon_lines.id AS carcon_line_id
        FROM carcon_lines
        WHERE carcon_lines.date = :date
          AND NOT EXISTS (
              SELECT 1
              FROM carcon_reservations
              INNER JOIN carcon_reservation_details
                  ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id
              WHERE carcon_reservations.carcon_line_id = carcon_lines.id
                AND carcon_reservation_details.slot_index = :slot_index
                AND carcon_reservations.is_deleted = 0
          )
        ORDER BY carcon_lines.id ASC
        LIMIT 1
    ";

    $stmt = $db->prepare($search_empty_line_sql);
    $stmt->bindValue(":date", $date, PDO::PARAM_STR);
    $stmt->bindValue(":slot_index", $slot_index, PDO::PARAM_INT);
    $stmt->execute();

    $carcon_line_id = $stmt->fetchColumn();

    if ($carcon_line_id === false) {
        throw new Exception("指定された日時に空き枠がありません。");
    }
    $carcon_line_id = (int)$carcon_line_id;

    $insert_detail_sql = "
        INSERT INTO carcon_reservation_details (
            student_id,
            meeting_type,
            meeting_url,
            meeting_id,
            meeting_passcode,
            slot_index,
            is_plus_carcon,
            created_at,
            updated_at
        ) VALUES (
            :student_id,
            :meeting_type,
            :meeting_url,
            :meeting_id,
            :meeting_passcode,
            :slot_index,
            :is_plus_carcon,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP
        )
    ";

    $stmt = $db->prepare($insert_detail_sql);
    $stmt->bindValue(":student_id", $student_id, PDO::PARAM_INT);
    $stmt->bindValue(":meeting_type", $meeting_type, PDO::PARAM_INT);
    $stmt->bindValue(":meeting_url", $meeting_url, PDO::PARAM_STR);
    $stmt->bindValue(":meeting_id", $meeting_id, PDO::PARAM_STR);
    $stmt->bindValue(":meeting_passcode", $meeting_passcode, PDO::PARAM_STR);
    $stmt->bindValue(":slot_index", $slot_index, PDO::PARAM_INT);
    $stmt->bindValue(":is_plus_carcon", $is_plus_carcon, PDO::PARAM_INT);
    $stmt->execute();

    $carcon_reservation_detail_id = (int)$db->lastInsertId();

    // --- 3. carcon_reservations に紐づけ ---
    $insert_reservation_sql = "
        INSERT INTO carcon_reservations (
            carcon_reservation_detail_id, carcon_line_id, is_deleted, created_at, updated_at
        ) VALUES (
            :detail_id, :line_id, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
        )
    ";

    $stmt = $db->prepare($insert_reservation_sql);
    $stmt->bindValue(":detail_id", $carcon_reservation_detail_id, PDO::PARAM_INT);
    $stmt->bindValue(":line_id", $carcon_line_id, PDO::PARAM_INT);
    $stmt->execute();

    $db->commit();

    // 成功時、index.phpへリダイレクト
    $_SESSION["msg"] = "予約が完了しました！";
    header('location: index.php');
    exit();
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    // エラー時、reserve_check.phpに戻す
    $_SESSION["err"] = "予約に失敗しました: " . $e->getMessage();
    header('Location: reserve_check.php');
    exit();
}
