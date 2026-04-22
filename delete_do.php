<?php
exit('プログラムは開始されました');
session_start();
require_once __DIR__ . '/includes/functions.php';

// TODO: データ受け取り
if (!empty($_POST)) {
    // POST送信されたとき
    var_dump($_POST);
    exit;
    if (!empty($_POST['id'])) {
        // TODO: idのチェック（空の場合）
        $id = $_POST['id'];
        // DBに接続
        try {
            $db = db_connect();
            // carcon_request_reservationsテーブルに1行挿入するSQL
            $sql = 'INSERT INTO `carcon_request_reservations` (
                `request_carcon_reservation_detail_id`, 
                `change_carcon_reservation_detail_id`, 
                `request_meeting_type`, 
                `change_meeting_type`, 
                `request_status_id`, 
                `request_type`, 
                `reject_message`, 
                `created_at`, 
                `updated_at`
            ) VALUES (
                :reservation_id, 
                NULL, 
                NULL, 
                NULL, 
                1, 
                1, 
                NULL, 
                NOW(), 
                NOW()
            )';
            $stmt = $db->prepare($sql);
            // idをプレースホルダへバインド
            $stmt->bindParam(':reservation_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            //edit.phpでメッセージを表示するためのセッション
            $_SESSION['success'] = '取消申請を送信';

            // 変更・取消画面へ遷移
            header('location:edit.php');
            exit();
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}
