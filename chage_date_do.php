<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// ログインしていない場合はログイン画面へ
if (!isset($_SESSION['id'])) {
    header('location:login.php');
    exit();
}

// TODO: データ受け取り
if (!empty($_POST)) {
    // POST送信されたとき
    if (!empty($_POST['id'])) {
        // TODO: idのチェック（空の場合）
        $id = $_POST['id'];
        $change_detail = $_POST['change_detail_id'];
        // DBに接続
        try {
            $db = db_connect();
            // carcon_request_reservationsテーブルに1行挿入するSQL
            $sql = 'INSERT INTO `carcon_request_reservations` (
                `request_carcon_reservation_detail_id`, 
                `change_carcon_reservation_detail_id`,
                `request_status_id`, 
                `request_type`, 
                `created_at`, 
                `updated_at`
            ) VALUES (
                :request_carcon_reservation_detail_id, 
                :change_carcon_reservation_detail_id, 
                1, 
                0, 
                NOW(), 
                NOW()
            )';
            $stmt = $db->prepare($sql);
            // idをプレースホルダへバインド
            $stmt->bindParam(':request_carcon_reservation_detail_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':change_carcon_reservation_detail_id', $change_detail, PDO::PARAM_INT);
            $stmt->execute();

            //成功メッセージをセットして遷移
            $_SESSION['change_date'] = '変更申請を送信';
            header('location:edit.php');
            exit();
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    } else {
        // IDが届いていない場合のデバッグ表示
        echo "POSTデータが届いていません。現在の内容は以下です：<br>";
        var_dump($_POST);
        exit;
    }
}
