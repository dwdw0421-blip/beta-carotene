<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

// ==============================
// 仮の受け取りデータ
// 本来は $_POST などから受け取る想定
// ==============================

// 学生ID
$student_id = $_SESSION["id"];

// 指定する日付
$date = $_POST["day"];

// 時刻を管理するIndex
// 例: 0 = 10:00, 1 = 11:00, 2 = 12:00
$slot_index = $_POST["time"];

// 面談方式
// 例: 1 = オンライン, 2 = 対面 など
$meeting_type = $_POST["type"];

// Zoom情報（今回は仮で）
$meeting_url = "https://zoom.example.com/j/123456789";
$meeting_id = "123456789";
$meeting_passcode = "abc123";

// キャリコンプラスかどうか
// ここから予約できるのはプラスだけの想定
$is_plus_carcon = 1;


// ==============================
// 予約登録処理
// ==============================

try {
    $db->beginTransaction();

    // --------------------------------
    // 1. 指定日付・指定slot_indexで空いているcarcon_line_idを探す
    // --------------------------------
    //
    // 複数空きがある場合は、carcon_lines.id が小さいものから使用する
    //
    // 例:
    // line_id = 1 の10:00が空いていれば line_id = 1
    // line_id = 1 が埋まっていれば line_id = 2
    //
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
    $stmt->bindParam(":date", $date, PDO::PARAM_STR);
    $stmt->bindParam(":slot_index", $slot_index, PDO::PARAM_INT);
    $stmt->execute();

    $carcon_line_id = $stmt->fetchColumn();

    if ($carcon_line_id === false) {
        throw new Exception("指定された日時に空き枠がありません。");
    }

    $carcon_line_id = (int)$carcon_line_id;


    // --------------------------------
    // 2. carcon_reservation_details に予約詳細を作成
    // --------------------------------

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
    $stmt->bindParam(":student_id", $student_id, PDO::PARAM_INT);
    $stmt->bindParam(":meeting_type", $meeting_type, PDO::PARAM_INT);
    $stmt->bindParam(":meeting_url", $meeting_url, PDO::PARAM_STR);
    $stmt->bindParam(":meeting_id", $meeting_id, PDO::PARAM_STR);
    $stmt->bindParam(":meeting_passcode", $meeting_passcode, PDO::PARAM_STR);
    $stmt->bindParam(":slot_index", $slot_index, PDO::PARAM_INT);
    $stmt->bindParam(":is_plus_carcon", $is_plus_carcon, PDO::PARAM_INT);
    $stmt->execute();

    $carcon_reservation_detail_id = (int)$db->lastInsertId();


    // --------------------------------
    // 3. carcon_reservations に紐づけデータを作成
    // --------------------------------

    $insert_reservation_sql = "
        INSERT INTO carcon_reservations (
            carcon_reservation_detail_id,
            carcon_line_id,
            is_deleted,
            created_at,
            updated_at
        ) VALUES (
            :carcon_reservation_detail_id,
            :carcon_line_id,
            0,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP
        )
    ";

    $stmt = $db->prepare($insert_reservation_sql);
    $stmt->bindParam(":carcon_reservation_detail_id", $carcon_reservation_detail_id, PDO::PARAM_INT);
    $stmt->bindParam(":carcon_line_id", $carcon_line_id, PDO::PARAM_INT);
    $stmt->execute();


    // --------------------------------
    // 4. 確定
    // --------------------------------

    $db->commit();

    echo "予約登録が完了しました。<br>";
    echo "使用した carcon_line_id: " . htmlspecialchars((string)$carcon_line_id, ENT_QUOTES, "UTF-8") . "<br>";
    echo "作成した carcon_reservation_detail_id: " . htmlspecialchars((string)$carcon_reservation_detail_id, ENT_QUOTES, "UTF-8") . "<br>";
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo "予約登録に失敗しました。<br>";
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8");
}
