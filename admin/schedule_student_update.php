<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
// require_once 'functions_test.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=UTF-8');

// JSONデータの受け取り
$json = file_get_contents('php://input');
$data = json_decode($json, true);





$id = isset($data['id']) ? (int)$data['id'] : 0;
$status = isset($data['status']) ? (int)$data['status'] : 0;
$slot_index = isset($data['slot_index']) ? (int)$data['slot_index'] : 0;



// var_dump($id);





if ($id > 0) {
    try {
        $pdo = db_connect();

        // --- トランザクション開始 ---
        $pdo->beginTransaction();

        // 1. carcon_reservations を更新 (line_id を書き換え)
        $sql1 = 'UPDATE carcon_reservations SET carcon_line_id = :line_id, updated_at = now() WHERE id = :id';
        $pdo->prepare($sql1)->execute([':line_id' => $status, ':id' => $id]);

        // 2. carcon_reservation_details を更新 (slot_index を書き換え)
        // 予約IDに紐づく詳細IDをサブクエリで指定
        $sql2 = 'UPDATE carcon_reservation_details SET slot_index = :slot, updated_at = now() 
                 WHERE id = (SELECT carcon_reservation_detail_id FROM carcon_reservations WHERE id = :id)';
        $pdo->prepare($sql2)->execute([':slot' => $slot_index, ':id' => $id]);

        // 3. メッセージ用のデータ取得（tasksとstatusesを結合）
        // $sql_msg = 'SELECT t.title, s.status FROM tasks t JOIN statuses s ON t.status = s.id WHERE t.id = :id';
        // $stmt = $pdo->prepare($sql_msg);
        // $stmt->execute([':id' => $id]);
        // $task = $stmt->fetch();

        // echo json_encode([
        //     'msg' => "「{$task['title']}」を「{$task['status']}」のスロット{$slot_index}へ移動しました。"
        // ], JSON_UNESCAPED_UNICODE);

        // --- トランザクション（保存） ---
        $pdo->commit();

        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    } else {
http_response_code(400);
    echo json_encode(['success' => false]);
    }
exit();






// if ($id > 0) {
//     try {
//         $pdo = db_connect();
        
//         // status と slot_index の両方を更新する
//         $sql = 'UPDATE tasks SET status = :status, slot_index = :slot_index, updated_at = now() WHERE id = :id';
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':status', $status, PDO::PARAM_INT);
//         $stmt->bindValue(':slot_index', $slot_index, PDO::PARAM_INT);
//         $stmt->bindValue(':id', $id, PDO::PARAM_INT);
//         $stmt->execute();

// // carcon_reservationsのcarcon_line_id と carcon_reservation_detailsのslot_indexを更新

// // メッセージ用：タスク名とステータス名を取得
//         $sql_task = 'SELECT tasks.title, statuses.status FROM tasks 
//                      INNER JOIN statuses ON tasks.status = statuses.id 
//                      WHERE tasks.id = :id';
//         $stmt_task = $pdo->prepare($sql_task);
//         $stmt_task->bindValue(':id', $id, PDO::PARAM_INT);
//         $stmt_task->execute();
//         $task = $stmt_task->fetch();

//         echo json_encode([
//             'msg' => "「{$task['title']}」を「{$task['status']}」のスロット{$slot_index}へ移動しました。"
//         ], JSON_UNESCAPED_UNICODE);

//     } catch (PDOException $e) {
//         http_response_code(500);
//         echo json_encode(['msg' => 'DBエラー: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
//     }
// } else {
//     http_response_code(400);
//     echo json_encode(['msg' => '不正なデータです'], JSON_UNESCAPED_UNICODE);
// }
// exit();

