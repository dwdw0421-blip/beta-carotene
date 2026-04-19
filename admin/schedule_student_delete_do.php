<?php
require_once __DIR__ . '/../includes/functions.php';
$line_data_json = file_get_contents('php://input');
$line_data = json_decode($line_data_json, true);

$id = (int)$line_data['id'];

var_dump($id);

try {
    $pdo = db_connect();

    // 消すlineにタスクがないかチェック
    $checkSql = 'SELECT COUNT(*) FROM tasks WHERE status = :id';
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $taskCount = $checkStmt->fetchColumn();

    // 2. タスクが1つ以上あれば、削除せずにメッセージを返す
    if ($taskCount > 0) {
        echo json_encode(['msg' => '削除する前にタスクを移動してください。', 'status' => 'error']);
        exit;
    }


    $sql = 'DELETE FROM statuses WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['msg' => 'lineを削除しました。']);
} catch (PDOException $e) {
    echo $e->getMessage();
}

