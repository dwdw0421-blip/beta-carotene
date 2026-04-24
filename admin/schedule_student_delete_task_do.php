<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=UTF-8'); // JSONで返すことを明示

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// キー名が 'id' であることを確認
$id = isset($data['id']) ? (int)$data['id'] : 0;

if ($id > 0) {
    try {
        $pdo = db_connect();
        // 物理削除から論理削除へ変更
        // $sql = 'DELETE FROM carcon_reservations WHERE id = :id';
        $sql = 'UPDATE carcon_reservations 
                SET is_deleted = 1, carcon_line_id = 1
                WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['msg' => '削除しました。']);
        exit();
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['msg' => '削除エラー: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['msg' => '無効なIDです。']);
}
exit();
