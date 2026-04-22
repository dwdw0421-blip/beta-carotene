<?php
require_once __DIR__ . '/../includes/functions.php';
$line_data_json = file_get_contents('php://input');
$line_data = json_decode($line_data_json, true);

$id = (int)$line_data['id'];



try {
    $pdo = db_connect();

    // 消すlineにタスクがないかチェック
    $checkSql = 'SELECT COUNT(*) FROM `carcon_reservations` WHERE `carcon_line_id` = :id';
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $taskCount = $checkStmt->fetchColumn();

    // 2. タスクが1つ以上あれば、削除せずにメッセージを返す
    if ($taskCount > 0) {
        echo json_encode(['msg' => '削除する前に生徒を移動してください。', 'status' => 'error']);
        exit;
    }

    // $pdo->beginTransaction(); // トランザクション開始

    $sql = 'DELETE FROM carcon_lines WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // 「このラインIDに関連付いている予約詳細ID」に一致する行を消す、という命令
    // $sql_2 = 'DELETE FROM carcon_reservation_details 
    //         WHERE id IN (
    //               SELECT carcon_reservation_detail_id 
    //               FROM carcon_reservations 
    //               WHERE carcon_line_id = :id
    //           )';
    // $stmt_2 = $pdo->prepare($sql_2);
    // $stmt_2->bindValue(':id', $id, PDO::PARAM_INT);
    // $stmt_2->execute();

    //  $pdo->commit(); // すべて成功したら確定

    echo json_encode(['status' => 'success', 'msg' => '予約枠を削除しました。']);
} catch (PDOException $e) {

    // $pdo->rollBack(); // エラーがあれば元に戻す

    echo json_encode(['status' => 'error', 'msg' => 'データベースエラーが発生しました。']);
}

