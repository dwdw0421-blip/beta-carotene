<?php

// セッションの開始
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    if (!empty($_POST['staff_id']) && !empty($_POST['password'])) {
        // ユーザー認証処理
        $staff_id = $_POST['staff_id'];
        $password = $_POST['password'];

        try {
            $db = db_connect();
            $sql = 'SELECT * FROM m_admin_staffs WHERE staff_id = :staff_id';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_STR);
            $stmt->execute();

            // 結果セットを連想配列の形で取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($result);
            if ($result) {
                // パスワードの検証
                if (password_verify($password, $result['password'])) {
                    $_SESSION['login_admin_id'] = $result['id'];
                    $_SESSION['staff_id'] = $result['staff_id'];
                    $_SESSION['staff_name'] = $result['last_name'] . $result['first_name'];
                    $_SESSION['res_message'] = ['type' => 1, 'msg' => 'ログイン成功'];
                    header('location: index.php');
                    exit();
                }
                $_SESSION['res_message'] = ['type' => 0, 'msg' => 'ログインIDまたはパスワードが正しくありません。'];
                header('location: login.php');
                exit();
            }
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}
header('location: login.php');
exit();
