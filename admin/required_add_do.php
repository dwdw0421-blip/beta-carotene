<?php
require_once __DIR__ . '/../includes/functions.php';


if (!empty($_POST)) {

    if (
        !empty($_POST['first_a']) && !empty($_POST['first_b']) && !empty($_POST['second_a']) && !empty($_POST['second_b']) && !empty($_POST['third_a']) && !empty($_POST['third_b']) &&
        !empty($_POST['course_id'])
    ) {

        //入力日程を取得
        $first_a = $_POST['first_a'];
        $first_b = $_POST['first_b'];
        $second_a = $_POST['second_a'];
        $second_b = $_POST['second_b'];
        $third_a = $_POST['third_a'];
        $third_b = $_POST['third_b'];
        $course_id = $_POST['course_id'];

        $dates = [
            $first_a,
            $first_b,
            $second_a,
            $second_b,
            $third_a,
            $third_b,
        ];

        //6日程がすべて別日なら処理続行する
        if (count($dates) == count(array_unique($dates))) {

            try {
                $db = db_connect();

                // すでにこのクラスで一括予約が作成されていないか確認する
                $checkSql = "SELECT 1 FROM carcon_reservations r INNER JOIN carcon_reservation_details d ON r.carcon_reservation_detail_id = d.id INNER JOIN m_students s ON d.student_id = s.id WHERE s.course_id = :course_id LIMIT 1";
                $checkStmt = $db->prepare($checkSql);
                $checkStmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
                $checkStmt->execute();

                if ($checkStmt->fetch()) {
                    throw new Exception('このクラスは既に一括予約が作成されています。再実行できません。');
                }

                $db->beginTransaction();
                //入れ込みたい学生の配列づくり
                $course_id = $_POST['course_id'];
                //学生情報を取得
                $sql = 'SELECT * FROM m_students WHERE m_students.course_id  = :course_id AND m_students.is_deleted = 0 ORDER BY m_students.id ASC';
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->execute();
                $student_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

                //2分割する
                $total = count($student_list);
                if ($total === 0) {
                    throw new Exception('対象の学生がいません');
                }
                // 分割位置（前半の人数）
                $splitIndex = (int)ceil($total / 2);

                // 前半グループ（A）
                $groupA = array_slice($student_list, 0, $splitIndex);

                // 後半グループ（B）
                $groupB = array_slice($student_list, $splitIndex);

                //グループごとに日程を対応付け
                $scheduleUsers = [
                    'first_a'  => $groupA,
                    'first_b'  => $groupB,
                    'second_a' => $groupA,
                    'second_b' => $groupB,
                    'third_a'  => $groupA,
                    'third_b'  => $groupB,
                ];

                $scheduleDates = [
                    'first_a'  => $first_a,
                    'first_b'  => $first_b,
                    'second_a' => $second_a,
                    'second_b' => $second_b,
                    'third_a'  => $third_a,
                    'third_b'  => $third_b,
                ];

                //キャリコン枠（ライン）
                $line_sql = 'INSERT INTO carcon_lines (date) VALUES (:date)';
                $line_stmt = $db->prepare($line_sql);

                //キャリコン予約詳細
                $detail_sql = 'INSERT INTO carcon_reservation_details (student_id,meeting_type,meeting_url,meeting_id,meeting_passcode,slot_index,is_plus_carcon) 
                        VALUES (:student_id,1,"https://www.google.com","000 0000 000","000000",:slot_index,0)  ';
                $detail_stmt = $db->prepare($detail_sql);

                //キャリコン予約
                $reservations_sql = 'INSERT INTO carcon_reservations (carcon_reservation_detail_id, carcon_line_id) VALUES (:carcon_reservation_detail_id, :carcon_line_id)';
                $reservations_stmt = $db->prepare($reservations_sql);


                //各日程ごとに必要ライン数を計算
                foreach ($scheduleUsers as $scheduleKey => $users) {
                    $date = $scheduleDates[$scheduleKey];
                    $userCount = count($users);
                    $requiredLines = (int)ceil($userCount / 6);

                    //毎回初期化しないと、前の日程のラインIDが残ってしまう
                    $lineIds = [];


                    for ($i = 0; $i < $requiredLines; $i++) {
                        $line_stmt->bindValue(':date', $date, PDO::PARAM_STR);
                        $line_stmt->execute();

                        $lineIds[] = $db->lastInsertId();
                    }

                    // 学生を流し込む
                    foreach ($users as $index => $student) {
                        $lineIndex = intdiv($index, 6);
                        $slotIndex = $index % 6;
                        $lineId = $lineIds[$lineIndex];
                        $studentId = $student['id'];

                        // 予約詳細
                        $detail_stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
                        $detail_stmt->bindValue(':slot_index', $slotIndex, PDO::PARAM_INT);
                        $detail_stmt->execute();

                        $detailId = $db->lastInsertId();

                        // 予約
                        $reservations_stmt->bindValue(':carcon_reservation_detail_id', $detailId, PDO::PARAM_INT);
                        $reservations_stmt->bindValue(':carcon_line_id', $lineId, PDO::PARAM_INT);
                        $reservations_stmt->execute();
                    }
                }

                $db->commit();
                header('location:student.php?course_id=' . $course_id);
            } catch (Exception $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                echo $e->getMessage();
                exit;
            }
        }
    }
}
