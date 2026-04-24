<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
$student_id = htmlspecialchars($_GET['student_id']);
try {
    //学生情報を取得
    $sql = 'SELECT * FROM m_students WHERE m_students.id  = :student_id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $student_result = $stmt->fetch(PDO::FETCH_ASSOC);

    //学生の予約情報を取得
    $sql = 'SELECT carcon_reservation_details.student_id as student_id,carcon_reservation_details.meeting_type as meeting_type,
    carcon_reservation_details.slot_index as slot_index,carcon_lines.date as date, 
    carcon_reservation_details.is_plus_carcon as is_plus_carcon
    FROM carcon_reservation_details 
    INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
    INNER JOIN carcon_lines ON carcon_lines.id  = carcon_reservations.carcon_line_id  
    INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id 
    WHERE carcon_reservation_details.student_id = :student_id AND carcon_reservations.is_deleted = 0 ORDER BY carcon_lines.date ASC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
$type = get_course_types_list();
$rooms = get_classrooms_list();

$course_id = $student_result['course_id'];

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="../css/style.css">
    <title>学生情報を編集</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>学生情報を編集</h1>
        <form action="./student_edit_confirm.php" method="post">

            <!-- 出席番号-->
            <div class="mb-2">
                <label class="form-label" for="student_no">出席番号</label>
                <input class="form-control" type="text" pattern="[0-9]*" name="student_no" id="student_no" value="<?php echo h($student_result['student_no']) ?>">
                <p class="fs-6 ">※半角数字のみ入力可能です</p>
            </div>

            <!-- 苗字-->
            <div class="mb-2">
                <label class="form-label" for="last_name">苗字</label>
                <input class="form-control" type="text" name="last_name" value="<?php echo h($student_result['last_name']) ?>">
            </div>


            <!-- 名前-->
            <div class="mb-2">
                <label class="form-label" for="first_name">名前</label>
                <input class="form-control" type="text" name="first_name" value="<?php echo h($student_result['first_name']) ?>">
            </div>

            <!-- パスワード-->
            <div class="mb-2">
                <label class="form-label" for="password">パスワード</label>
                <input class="form-control" type="password" name="password">
            </div>

            <!-- 在籍ステータス -->
            <?php $enrollments = get_enrollments_list() ?>
            <div class="mb-2">
                <label class="form-label" for="enrollment_id">在籍ステータス</label>
                <select class="form-select" name="enrollment_id">

                    <?php
                    foreach ($enrollments as $key => $enrollment):
                    ?>
                        <option value="<?php echo h($key) ?>" <?php echo  $key == $student_result['enrollment_id'] ? 'selected' : ""; ?>><?php echo h($enrollment) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <!-- 確認画面へ -->
            <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
            <input type="hidden" name="student_id" value="<?php echo h($student_id) ?>">
            <input class="btn btn-primary" type="submit" value="入力内容を確認">



        </form>

        <p class="mt-5">予約一覧</p>
        <ul class="list-group pb-5">
            <?php
            $slot = get_slot_list();
            $meeting = get_meeting_type_list();
            ?>
            <?php foreach ($reservation_result as $reserve): ?>
                <li class="list-group-item"><?php echo format_date($reserve['date'], 4) ?>&nbsp;<?php echo h($slot[$reserve['slot_index']]) ?>&nbsp;(<?php echo h($meeting[$reserve['meeting_type']]) ?>)｜<?php echo ($reserve['is_plus_carcon'] == 0) ? "キャリコン（必須）" : "キャリコン＋（任意）" ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>

</html>