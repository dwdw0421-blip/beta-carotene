<?php
require_once 'functions_test.php';
header('Content-Type: application/json; charset=UTF-8');

// JSONデータの受け取り
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id = isset($data['id']) ? (int)$data['id'] : 0;
$status = isset($data['status']) ? (int)$data['status'] : 0;
$slot_index = isset($data['slot_index']) ? (int)$data['slot_index'] : 0;

if ($id > 0) {
    try {
        $pdo = db_connect();
        
        // status と slot_index の両方を更新する
        $sql = 'UPDATE tasks SET status = :status, slot_index = :slot_index, updated_at = now() WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':slot_index', $slot_index, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // メッセージ用：タスク名とステータス名を取得
        $sql_task = 'SELECT tasks.title, statuses.status FROM tasks 
                     INNER JOIN statuses ON tasks.status = statuses.id 
                     WHERE tasks.id = :id';
        $stmt_task = $pdo->prepare($sql_task);
        $stmt_task->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt_task->execute();
        $task = $stmt_task->fetch();

        echo json_encode([
            'msg' => "「{$task['title']}」を「{$task['status']}」のスロット{$slot_index}へ移動しました。"
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['msg' => 'DBエラー: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode(['msg' => '不正なデータです'], JSON_UNESCAPED_UNICODE);
}
exit();

