<?php

require_once __DIR__ . '/../includes/functions.php';


if (!empty($_POST)) {
    if (!empty($_POST['name']) && !empty($_POST['classroom_id']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['course_type'])) {
        $name = $_POST['name'];
        $classroom_id = $_POST['classroom_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $course_type = $_POST['course_type'];

        try {
            $db = db_connect();
            // 悩み：重複チェックって必要？？同じ名前のコースって多分普通にあるよね...でも全く同じもの作ろうとしてたら、それは止めてあげたいか...？

            // m_coursesテーブルに登録
            $sql = 'INSERT INTO m_courses (name,start_date,end_date,course_type,classroom_id) 
            VALUES (:name,:start_date,:end_date,:course_type,:classroom_id)';
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':classroom_id', $classroom_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt->bindParam(':course_type', $course_type, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}



header('location:index.php');
