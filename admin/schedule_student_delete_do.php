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


    $sql = 'DELETE FROM carcon_lines WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'msg' => '予約枠を削除しました。']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'msg' => 'データベースエラーが発生しました。']);
}

