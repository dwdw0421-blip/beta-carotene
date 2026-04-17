<?php

require_once __DIR__ . '/../includes/functions.php';

$id = (isset($_POST["id"]) ? (int)$_POST["id"] : "");

// 表示できるデータが無ければトップに戻す
if (empty($id)) {
    header("location: index.php");
    exit();
}

$reject_message = $_POST["reject_message"];
var_dump($reject_message);

if (empty($reject_message)) {
    header("location: request_detail.php?id=" . $id);
    exit("棄却理由は必ず必要なので戻す");
}

try {
    $db = db_connect();

    // 申請ステータスを「棄却」に更新＋「棄却理由」を更新
    $update_status_sql = "UPDATE carcon_request_reservations SET request_status_id=:next_status, reject_message=:reject_message WHERE id=:id";
    $stmt = $db->prepare($update_status_sql);
    $approve_id = RequestStatus::Reject->value;
    $stmt->bindParam(":next_status", $approve_id, PDO::PARAM_INT);
    $stmt->bindParam(":reject_message", $reject_message, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Throwable $e) {
    exit($e->getMessage());
}

// 処理が終わったらトップに戻す
header("location: index.php");
exit();
