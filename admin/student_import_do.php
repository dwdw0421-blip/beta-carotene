<?php
require_once __DIR__ . '/../includes/functions.php';
$course_id = $_POST['course_id'];
$login_id = $_POST['start_year'] . $_POST['start_month'] . $_POST['room'];

// sample01.php でアップロードされたCSVファイルは$_FILES['csv_file']取得できる
$fileName = $_FILES['csv_file']['name'];
$fileTmpName = $_FILES['csv_file']['tmp_name'];

// ファイルパス
$filePath = './uploads/' . $fileName;

// CSVファイルをcsvディレクトリに保存する
move_uploaded_file($fileTmpName, $filePath);

// csvディレクトリに保存したCSVファイルを読み込み、配列に置き換る。
$data = array_map('str_getcsv', file($filePath));


// m_studentsテーブルにデータを挿入する
foreach ($data as $key => $row) {

    // 1行目はテーブルに入れたくないのでスキップする
    if ($key === 0) {
        continue;
    }

    $student_no = $row[0];
    $last_name  = $row[1];
    $first_name = $row[2];
    $password = $row[3];
    $enrollment_id = $row[4];
    $login_id = $login_id . $row[0];


    try {
        $db = db_connect();


        $sql = ("INSERT INTO m_students (student_no, first_name, last_name,login_id,password, course_id, enrollment_id) VALUES (:student_no, :first_name, :last_name,:login_id, :password, :course_id, :enrollment_id)");
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':student_no', $student_no, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':login_id', $login_id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->bindParam(':enrollment_id', $enrollment_id, PDO::PARAM_INT);
        $stmt->execute();
        header('location: student.php');
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}
header('location:student.php?courses_id=' . $course_id);
