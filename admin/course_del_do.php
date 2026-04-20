<?php

require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    $courses_id = $_POST['courses_id'];
    try {
        $db = db_connect();

        // m_coursesテーブルから削除
        $sql = 'DELETE FROM m_courses WHERE m_courses.id = :courses_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':courses_id', $courses_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}


header('location:index.php');
