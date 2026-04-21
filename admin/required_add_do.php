<?php


require_once __DIR__ . '/../includes/functions.php';
if (!empty($_POST)) {
    if (!empty($_POST['first_a']) && !empty($_POST['first_b']) && !empty($_POST['second_a']) && !empty($_POST['second_b']) && !empty($_POST['third_a']) && !empty($_POST['third_b']) && !empty($_POST['third_a'])) {


        //入力日程を取得
        $first_a = $_POST['first_a'];
        $first_b = $_POST['first_b'];
        $second_a = $_POST['second_a'];
        $second_b = $_POST['second_b'];
        $third_a = $_POST['third_a'];
        $third_b = $_POST['third_b'];
        try {

            var_dump($_POST);
            exit();
            $db = db_connect();


            $sql = '';
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':first_a', $first_a, PDO::PARAM_STR);
            $stmt->bindParam(':first_b', $first_b, PDO::PARAM_STR);
            $stmt->bindParam(':second_a', $second_a, PDO::PARAM_STR);
            $stmt->bindParam(':second_b', $second_b, PDO::PARAM_STR);
            $stmt->bindParam(':third_a', $third_a, PDO::PARAM_STR);
            $stmt->bindParam(':third_b', $third_b, PDO::PARAM_STR);


            $stmt->execute();

            //carcon_linesに日程情報（date）追加
            //carcon_reservation_detailsにスロット情報（slot_index）追加（こっちで勝手に決める）
            //carcon_reservationsにcarcon_reservation_detail_idとcarcon_line_idを追加
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}
// header('location:student.php?courses_id=' . $course_id);
