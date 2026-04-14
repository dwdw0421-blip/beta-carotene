<?php
// セッションの開始
session_start();

// debug_check_array($_POST);
if (!empty($_POST)) {
    if (!empty($_POST['name']) && !empty($_POST['password'])) {
        // ユーザー認証処理
        $login_input = $_POST['name'];
        $password = $_POST['password'];

        try {
            $db = db_connect();
            $sql = 'SELECT *, 
            CONCAT(classroom_id, student_no) AS login_id
            FROM m_students 
            WHERE CONCAT(classroom_id, student_no) = :login_id';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':login_id', $login_input, PDO::PARAM_STR);
            $stmt->execute();

            // 結果セットを連想配列の形で取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                // パスワードの検証
                if (password_verify($password, $result['password'])) {
                    $_SESSION['id'] = session_id();
                    $_SESSION['name'] = $result['name'];
                    header('location:index.php');
                    exit();
                }
            }
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}
header('location:login.php');
exit();
