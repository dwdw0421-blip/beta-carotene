<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    $course_id = $_POST['course_id'];
    try {
        $db = db_connect();

        // m_coursesテーブルにて論理削除
        $sql = 'UPDATE m_courses SET is_deleted = 1 WHERE id = :course_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();

        //そのコースに所属する学生もm_studentsにて論理削除
        $sql = 'UPDATE m_students SET is_deleted = 1 WHERE course_id = :course_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}


header('location:index.php');
