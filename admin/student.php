<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
$courses_id = htmlspecialchars($_GET['courses_id']);

try {
    // コース情報を取得
    $sql = 'SELECT * FROM m_courses WHERE m_courses.id  = :courses_id AND m_courses.is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':courses_id', $courses_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //学生情報を取得
    $sql = 'SELECT * FROM m_students WHERE m_students.course_id  = :courses_id AND m_students.is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':courses_id', $courses_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $student_result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    //学生の予約情報を取得
    $sql = 'SELECT carcon_reservation_details.student_id as student_id,carcon_reservation_details.meeting_type as meeting_type,carcon_reservation_details.slot_index as slot_index,carcon_lines.date as date FROM carcon_reservation_details INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id ORDER BY carcon_lines.date DESC';
    $stmt = $db->prepare($sql);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
$type = get_course_types_list();
$room = get_classrooms_list();

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
    <title><?php echo h($result['name']) ?></title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1><?php echo h($room[$result['classroom_id']]) ?>(<?php echo h(format_date($result['start_date'], 2)) ?>開講)</h1>
        <form class="row card-body bg-light" action="./course_del_do.php" method="post" onsubmit="return confirm('このコースを削除してよろしいですか？')">

            <dt class="col-sm-3">コース名</dt>
            <dd class="col-sm-9"><?php echo h($result['name']) ?></dd>


            <dt class="col-sm-3">教室</dt>
            <dd class="col-sm-9"><?php echo h($room[$result['classroom_id']]) ?></dd>

            <dt class="col-sm-3">期間</dt>
            <dd class="col-sm-9"><?php echo h(format_date($result['start_date'], 2)) ?>～<?php echo h(format_date($result['end_date'], 2)) ?></dd>


            <dt class="col-sm-3">区分</dt>
            <dd class="col-sm-9"><?php echo h($type[$result['course_type']]) ?></dd>

            <div>
                <a href="./course_edit.php?courses_id=<?php echo h($result['id']) ?>" class="btn btn-outline-secondary  d-inline-block">情報を修正</a>
                <input type="submit" class="btn btn-outline-danger d-inline-block" value="コースを削除">
            </div>
        </form>
        <div class="border p-3 mt-2 mb-2">
            <h2>学生一覧</h2>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">出席番号</th>
                        <th scope="col">名前</th>
                        <th scope="col">ユーザーID</th>
                        <th scope="col">パスワード</th>
                        <th scope="col">最新の予約日時</th>
                        <th scope="col">在籍状況</th>
                        <th scope="col">変更</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $enrollments = get_enrollments_list();
                    ?>
                    <?php
                    $slot = get_slot_list();
                    foreach ($student_result as $student):
                    ?>
                        <tr>
                            <th scope="row"><?php echo h($student['student_no']) ?></th>
                            <td><?php echo h($student['last_name']) ?>&ensp;<?php echo h($student['first_name']) ?></td>
                            <td><?php echo h($course['classroom_name']) ?><?php echo h(sprintf('%02d', $student['student_no'])) ?></td>
                            <td><?php echo h($student['password']) ?></td>
                            <td>
                                <?php if (!empty($reservation_result)): ?>
                                    <?php foreach ($reservation_result as $reserve): ?>
                                        <?php if ($student['id'] == $reserve['student_id']): ?>
                                            <?php echo h(format_date($reserve['date'], 4)) ?>&ensp;<?php echo h($slot[$reserve['slot_index']]) ?>
                                            <?php break;  ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    予約はありません
                                <?php endif; ?>
                            </td>
                            <td><?php echo h($enrollments[$student['enrollment_id']]) ?></td>
                            <td><a href="./student_edit.php?student_no=<?php echo h($student['student_no']) ?>" class="btn btn-primary d-inline-block">変更</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            <p>【在籍状況について】</p>
            <ul>
                <li>在校中：在校中でCC可能</li>
                <li>中退：途中退校でCCは不要（開講前に辞退した方も含む）</li>
                <li>支援中OB：修了済でCC可能</li>
                <li>支援不要OB：修了済でCC不要（就職退校含む）</li>
            </ul>

            <a href="./student_add.php" class="btn btn-outline-secondary  d-inline-block">学生を追加（手入力）</a>
            <button id="student_add" class="btn btn-outline-secondary  d-inline-block">学生を追加（CSV読み込み）</button>
        </div>
        <a href="./required_add.php" class="btn btn-outline-secondary d-inline-block mt-2 mb-2">必須キャリコンの一括予約</a>
    </section>
</body>

</html>