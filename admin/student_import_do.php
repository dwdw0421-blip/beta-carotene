<?php
require_once __DIR__ . '/../includes/functions.php';
$course_id = $_POST['course_id'];
$login_id = $_POST['start_year'] . $_POST['start_month'] . $_POST['room'];

// アップロードされたCSVファイルは$_FILES['csv_file']取得できる
$fileName = $_FILES['csv_file']['name'];
$fileTmpName = $_FILES['csv_file']['tmp_name'];

// ファイルパス
$filePath = './uploads/' . $fileName;

// CSVファイルをcsvディレクトリに保存する
move_uploaded_file($fileTmpName, $filePath);

// csvディレクトリに保存したCSVファイルを読み込み、配列に置き換える。
$data = array_map('str_getcsv', file($filePath));




// m_studentsテーブルにデータを挿入する
foreach ($data as $key => $row) {

    // 1行目はテーブルに入れたくないのでスキップする
    if ($key === 0) {
        continue;
    }

    $student_no = sprintf('%02d', $row[0]);
    $last_name  = $row[1];
    $first_name = $row[2];
    $password = $row[3];
    $enrollment_id = $row[4];
    $pw_hash = "";
    if (check_preg_password($password)) {
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        header('location:student.php?course_id=' . $course_id);
        exit();
    }

    try {
        $db = db_connect();
        //ログインIDの準備（主キーの取得）
        $sql = 'SELECT id FROM m_students ORDER BY id DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $lastId_result =  $stmt->fetch(PDO::FETCH_ASSOC);
        $lastId = (int)$lastId_result['id'] + 1;
        $login_id .= $lastId;

        //ユーザーID被りがいないか確認
        $sql = 'SELECT COUNT(login_id) FROM m_students WHERE login_id=:login_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':login_id', $login_id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_NUM);

        //このコースの学生が既に予約をとっていないか確認
        $sql = 'SELECT COUNT(m_students.id) FROM carcon_reservation_details INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id WHERE m_students.course_id = :course_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $reserve_result = $stmt->fetch(PDO::FETCH_NUM);

        //予約が0なら、2回目以降の一括読み込みを受け入れる
        if ($reserve_result[0] == 0) {
            //2回目以降の一括読み込みでは、既存学生のレコードを削除してから読み込み処理する
            if ($result[0] !== 0) {
                $sql = "DELETE FROM m_students WHERE course_id = $course_id";
                $stmt = $db->prepare($sql);
                $stmt->execute();
            }


            $sql = ("INSERT INTO m_students (student_no, first_name, last_name,login_id,password, course_id, enrollment_id) VALUES (:student_no, :first_name, :last_name,:login_id, :password, :course_id, :enrollment_id)");
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':student_no', $student_no, PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':login_id', $login_id, PDO::PARAM_INT);
            $stmt->bindParam(':password', $pw_hash, PDO::PARAM_STR);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->bindParam(':enrollment_id', $enrollment_id, PDO::PARAM_INT);
            $stmt->execute();
            $login_id = $_POST['start_year'] . $_POST['start_month'] . $_POST['room'];
            header('location:student.php?course_id=' . $course_id);
        }
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}
header('location:student.php?course_id=' . $course_id);
