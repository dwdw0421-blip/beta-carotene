<?php
session_start();
// require_once 'functions_test.php';

require_once __DIR__ . '/../includes/functions.php';


// JSONを受け取る
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// var_dump($data);

// 必要なデータ（classroom_id と date）が揃っているか確認
//array_key_exists　値が何であれ（たとえ null でも）、「箱（キー）さえあれば」 true に。
if ($data && array_key_exists('classroom_id', $data) && isset($data['date'])) {
    try {
        $pdo = db_connect();
        
        $sql = 'INSERT INTO carcon_lines (date, classroom_id, created_at) VALUES (:date, :classroom_id, NOW())';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':date'         => $data['date'],
            ':classroom_id' => ($data['classroom_id'] === "" || $data['classroom_id'] === null) ? null : $data['classroom_id']
]);

        echo json_encode(['success' => true]);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'データが不足しています', 'received' => $data]);
}
exit();

