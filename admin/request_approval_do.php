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

    // キャリコン予約申請のデータを再度取得
    $sql = "SELECT request_carcon_reservation_detail_id,
                change_carcon_reservation_detail_id,
                request_meeting_type,
                change_meeting_type,
                request_status_id 
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
    $is_change_data = !is_null($request_data["change_carcon_reservation_detail_id"]);

    $request_result = array();
    $change_result = array();
    $students_data = array();

    if ($is_change_data) {
        // リクエスト側しかデータがないケース
        // 形式の変更のみ
        $update_sql = "UPDATE carcon_reservation_details SET meeting_type=:next_type WHERE id=:id";
        $stmt = $db->prepare($update_sql);
        $stmt->bindParam(":next_type", $request_data["request_meeting_type"], PDO::PARAM_INT);
        $stmt->bindParam(":id", $request_data["request_carcon_reservation_detail_id"], PDO::PARAM_INT);
        $stmt->execute();
    } else {
    }

    // 更新終わったら申請ステータスを「承認済み」に更新する
    $update_status_sql = "UPDATE carcon_request_reservations SET request_status_id=:next_status WHERE id=:id";
    $stmt = $db->prepare($update_status_sql);
    $approve_id = RequestStatus::Approve->value;
    $stmt->bindParam(":next_status", $approve_id, PDO::PARAM_INT);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    exit($e->getMessage());
}

// 二つデータがあるケース

// 1. キャリコン予約 に紐づいているそれぞれの詳細データ を入れ替える（ラインの入れ替え）
// 2. それぞれの詳細に紐づいているslot_indexを入れ替える（時間の入れ替え）
// 3. 問題なければ通してOK

// リクエスト側しかデータがないケース

// 1. 形式の変更のみなので、形式変更を更新する