<?php
require_once 'functions_test.php';
$line_data_json = file_get_contents('php://input');
// echo $line_data_json;
// var_dump($line_data_json);
$line_data = json_decode($line_data_json, true);

// var_dump($line_data);
$status = $line_data['title'];

try {
    $pdo = db_connect();
    $sql = 'INSERT INTO statuses (status) VALUES (:status)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['msg' => '新規lineを登録しました。']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
