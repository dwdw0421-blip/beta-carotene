<?php

require_once __DIR__ . '/../includes/functions.php';

$id = (isset($_POST["id"]) ? (int)$_POST["id"] : "");

// 表示できるデータが無ければトップに戻す
if (empty($id)) {
    header("location: index.php");
    exit();
}

try {
    $db = db_connect();
    $db->beginTransaction();

    // キャリコン予約申請のデータを再度取得
    $sql = "SELECT request_carcon_reservation_detail_id,
                change_carcon_reservation_detail_id,
                request_meeting_type,
                change_meeting_type,
                request_status_id,
                request_type
            FROM carcon_request_reservations WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $request_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 存在しない変更申請の結果だったら戻す
    // 申請中以外のステータスなら不要なので戻す
    if (empty($request_data) || $request_data["request_status_id"] !== RequestStatus::Pending->value) {
        header("location: index.php");
        exit();
    }

    // 変更側の詳細データがない＝リクエスト側だけのデータ（自分のデータ変更は「形式」のみ）
    $is_exchange_data = !is_null($request_data["change_carcon_reservation_detail_id"]);
    // キャンセルの申請かどうか
    $is_cancel_data = $request_data["request_type"] === 1;

    if ($is_cancel_data) {
        // キャンセル申請なので予約詳細からデータを削除
        $delete_sql = "DELETE FROM carcon_reservation_details WHERE id=:id";
        $d_stmt = $db->prepare($delete_sql);
        $d_stmt->bindParam(":id", $request_data["request_carcon_reservation_detail_id"], PDO::PARAM_INT);
        $d_stmt->execute();
    } else if ($is_exchange_data) {
        // 二つデータがあるケース
        $detail_idA = $request_data["request_carcon_reservation_detail_id"];
        $detail_idB = $request_data["change_carcon_reservation_detail_id"];

        // 1. carcon_reservations から対象2件をロックして取得
        $reservation_select_sql = "
                SELECT carcon_reservation_detail_id, carcon_line_id
                FROM carcon_reservations
                WHERE carcon_reservation_detail_id IN (:id_a, :id_b)
                FOR UPDATE
            ";
        $r_stmt = $db->prepare($reservation_select_sql);
        $r_stmt->bindParam(":id_a", $detail_idA, PDO::PARAM_INT);
        $r_stmt->bindParam(":id_b", $detail_idB, PDO::PARAM_INT);
        $r_stmt->execute();

        $r_rows = $r_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($r_rows) !== 2) {
            throw new RuntimeException("carcon_reservations の対象レコードが2件取得できませんでした。");
        }

        $line_id_array = [];
        foreach ($r_rows as $row) {
            $line_id_array[(int)$row["carcon_reservation_detail_id"]] = (int)$row["carcon_line_id"];
        }

        if (!isset($line_id_array[$detail_idA], $line_id_array[$detail_idB])) {
            throw new RuntimeException("carcon_reservations の対象IDが不足しています。");
        }

        $line_idA = $line_id_array[$detail_idA];
        $line_idB = $line_id_array[$detail_idB];

        // 2. carcon_reservation_details から対象2件をロックして取得
        $detail_select_sql = "
                SELECT id, slot_index
                FROM carcon_reservation_details
                WHERE id IN (:id_a, :id_b)
                FOR UPDATE
            ";
        $d_stmt = $db->prepare($detail_select_sql);
        $d_stmt->bindParam(":id_a", $detail_idA, PDO::PARAM_INT);
        $d_stmt->bindParam(":id_b", $detail_idB, PDO::PARAM_INT);
        $d_stmt->execute();

        $d_rows = $d_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($d_rows) !== 2) {
            throw new RuntimeException("carcon_reservation_details の対象レコードが2件取得できませんでした。");
        }

        $slot_index_array = [];
        foreach ($d_rows as $row) {
            $slot_index_array[(int)$row["id"]] = (int)$row["slot_index"];
        }

        if (!isset($slot_index_array[$detail_idA], $slot_index_array[$detail_idB])) {
            throw new RuntimeException("carcon_reservation_details の対象IDが不足しています。");
        }

        $slot_indexA = $slot_index_array[$detail_idA];
        $slot_indexB = $slot_index_array[$detail_idB];

        // 3. carcon_reservations.carcon_line_id を入れ替え
        $r_update_sql = "
                UPDATE carcon_reservations
                SET carcon_line_id = CASE carcon_reservation_detail_id
                    WHEN :id_a_case THEN :line_id_b
                    WHEN :id_b_case THEN :line_id_a
                END
                WHERE carcon_reservation_detail_id IN (:id_a_where, :id_b_where)
            ";
        $r_update_stmt = $db->prepare($r_update_sql);
        $r_update_stmt->bindParam(":id_a_case", $detail_idA, PDO::PARAM_INT);
        $r_update_stmt->bindParam(":id_b_case", $detail_idB, PDO::PARAM_INT);
        $r_update_stmt->bindParam(":line_id_b", $line_idB, PDO::PARAM_INT);
        $r_update_stmt->bindParam(":line_id_a", $line_idA, PDO::PARAM_INT);
        $r_update_stmt->bindParam(":id_a_where", $detail_idA, PDO::PARAM_INT);
        $r_update_stmt->bindParam(":id_b_where", $detail_idB, PDO::PARAM_INT);
        $r_update_stmt->execute();

        // 4. carcon_reservation_details.slot_index を入れ替え
        $detail_update_sql = "
                UPDATE carcon_reservation_details
                SET slot_index = CASE id
                    WHEN :id_a_case THEN :slot_index_b
                    WHEN :id_b_case THEN :slot_index_a
                END
                WHERE id IN (:id_a_where, :id_b_where)
            ";
        $d_update_stmt = $db->prepare($detail_update_sql);
        $d_update_stmt->bindParam(":id_a_case", $detail_idA, PDO::PARAM_INT);
        $d_update_stmt->bindParam(":id_b_case", $detail_idB, PDO::PARAM_INT);
        $d_update_stmt->bindParam(":slot_index_b", $slot_indexB, PDO::PARAM_INT);
        $d_update_stmt->bindParam(":slot_index_a", $slot_indexA, PDO::PARAM_INT);
        $d_update_stmt->bindParam(":id_a_where", $detail_idA, PDO::PARAM_INT);
        $d_update_stmt->bindParam(":id_b_where", $detail_idB, PDO::PARAM_INT);
        $d_update_stmt->execute();
    } else {
        // リクエスト側しかデータがないケース
        // 形式の変更のみ
        $update_sql = "UPDATE carcon_reservation_details SET meeting_type=:next_type WHERE id=:id";
        $stmt = $db->prepare($update_sql);
        $stmt->bindParam(":next_type", $request_data["request_meeting_type"], PDO::PARAM_INT);
        $stmt->bindParam(":id", $request_data["request_carcon_reservation_detail_id"], PDO::PARAM_INT);
        $stmt->execute();
    }

    // 更新終わったら申請ステータスを「承認済み」に更新する
    $update_status_sql = "UPDATE carcon_request_reservations SET request_status_id=:next_status WHERE id=:id";
    $stmt = $db->prepare($update_status_sql);
    $approve_id = RequestStatus::Approve->value;
    $stmt->bindParam(":next_status", $approve_id, PDO::PARAM_INT);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $db->commit();
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    exit($e->getMessage());
}

// 処理が終わったらトップに戻す
header("location: index.php");
exit();
