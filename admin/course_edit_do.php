<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
$course_id = $_POST['course_id'];

if (!empty($_POST)) {
    if (!empty($_POST['name']) && !empty($_POST['classroom_id']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['course_type'])) {
        $name = $_POST['name'];
        $classroom_id = $_POST['classroom_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $course_type = $_POST['course_type'];

        try {
            $db = db_connect();

            // m_coursesテーブルを更新
            $sql = 'UPDATE m_courses SET name=:name,start_date=:start_date,end_date=:end_date,course_type=:course_type,classroom_id=:classroom_id WHERE id = :course_id';
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt->bindParam(':course_type', $course_type, PDO::PARAM_INT);
            $stmt->bindParam(':classroom_id', $classroom_id, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            exit('エラー: ' . $e->getMessage());
        }
    }
}



header('location:student.php?course_id=' . $course_id);
