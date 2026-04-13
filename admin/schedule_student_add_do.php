<?php
require_once 'functions_test.php';
$task_data_json = file_get_contents('php://input');
// echo $task_data_json;
$task_data = json_decode($task_data_json, true);

// var_dump($task_data);
$title = $task_data['title'];

try {
    $pdo = db_connect();
    $sql = 'INSERT INTO tasks (title) VALUES (:title)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['msg' => '新規タスクを登録しました。']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
