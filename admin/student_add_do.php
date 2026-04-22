<?php

require_once __DIR__ . '/../includes/functions.php';


if (!empty($_POST)) {

    if (!empty($_POST['course_id'])) {
        $db = db_connect();

        //コースIDから開始年と開始月、開催教室を取得
        $course_id = $_POST['course_id'];

        $sql = 'SELECT start_date,classroom_id FROM m_courses  WHERE m_courses.id = :course_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $course_result = $stmt->fetch(PDO::FETCH_ASSOC);

        $rooms = get_classrooms_list();


        $login_id  = format_date($course_result['start_date'], 5) . format_date($course_result['start_date'], 6) . $rooms[$course_result['classroom_id']];

        if (!empty($_POST['student_no']) && !empty($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['enrollment_id'])) {

            $student_no = sprintf('%02d', $_POST['student_no']);
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $password = $_POST['password'];
            $enrollment_id = $_POST['enrollment_id'];
            $login_id .= $student_no;
            $pw_hash = "";

            if (check_preg_password($password)) {
                $pw_hash = password_hash($password, PASSWORD_DEFAULT);
            } else {
                header('location:student.php?courses_id=' . $course_id);
                exit();
            }

            try {
                $db = db_connect();


                //m_studentsテーブルに登録
                $sql = 'INSERT INTO m_students(student_no, last_name, first_name, login_id, password, course_id, enrollment_id) 
            VALUES (:student_no,:last_name,:first_name,:login_id,:password,:course_id,:enrollment_id)';
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':login_id', $login_id, PDO::PARAM_STR);
                $stmt->bindParam(':password', $pw_hash, PDO::PARAM_STR);
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_STR);
                $stmt->bindParam(':enrollment_id', $enrollment_id, PDO::PARAM_STR);


                $stmt->execute();
            } catch (PDOException $e) {
                $msg = ($e->getCode() == '23000')
                    ? "学生番号が重複しているため、変更できませんでした。"
                    : "エラーが発生したため、変更できませんでした。";

                $redirectUrl = 'student.php?courses_id=' . $course_id;

                echo "<script src='../js/message.js'></script>";
                echo "<script>
        window.onload = function() {
            showErrorAlert('" . addslashes($msg) . "', '" . $redirectUrl . "');
        };
    </script>";
                exit;
            }
        }
    }
}
header('location:student.php?courses_id=' . $course_id);
