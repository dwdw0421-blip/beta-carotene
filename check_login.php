<?php
// セッションの開始
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/includes/functions.php';

// debug_check_array($_POST);
if (!empty($_POST)) {
    if (!empty($_POST['login_id']) && !empty($_POST['password'])) {
        // ユーザー認証処理
        $login_id = $_POST['login_id'];
        $password = $_POST['password'];

        try {
            $db = db_connect();
            $sql = 'SELECT * FROM m_students WHERE login_id = :login_id';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':login_id', $login_id, PDO::PARAM_STR);
            $stmt->execute();

            // 結果セットを連想配列の形で取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                // パスワードの検証
                if (password_verify($password, $result['password'])) {
                    $_SESSION['id'] = $result['id'];
                    $_SESSION['login_id'] = $result['login_id'];
                    $_SESSION['res_message'] = ['type' => 1, 'msg' => 'ログイン成功'];
                    header('location:index.php');
                    exit();
                }
                $_SESSION['res_message'] = ['type' => 0, 'msg' => 'ログインIDまたはパスワードが正しくありません。'];
                header('location:login.php');
                exit();
            }
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}
header('location: login.php');
exit();
