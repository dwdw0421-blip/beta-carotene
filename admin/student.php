<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
$course_id = htmlspecialchars($_GET['course_id']);

try {
    // コース情報を取得
    $sql = 'SELECT * FROM m_courses WHERE m_courses.id  = :course_id AND m_courses.is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //学生情報を取得
    $sql = 'SELECT * FROM m_students WHERE m_students.course_id  = :course_id AND m_students.is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $student_result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    //学生の予約情報を取得
    $sql = 'SELECT carcon_reservation_details.student_id as student_id,carcon_reservation_details.meeting_type as meeting_type,carcon_reservation_details.slot_index as slot_index,carcon_lines.date as date FROM carcon_reservation_details INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id WHERE carcon_reservations.is_deleted = 0 ORDER BY carcon_lines.date DESC';
    $stmt = $db->prepare($sql);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // すでにこのクラスで一括予約が作成されていないか確認する
    $checkSql = "SELECT 1 FROM carcon_reservations r INNER JOIN carcon_reservation_details d ON r.carcon_reservation_detail_id = d.id INNER JOIN m_students s ON d.student_id = s.id WHERE s.course_id = :course_id LIMIT 1";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $alreadyExists = $checkStmt->fetch() ? true : false;
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
    <title><?php echo h($result['name']) ?>｜<?php echo h($room[$result['classroom_id']]) ?>(<?php echo h(format_date($result['start_date'], 2)) ?>開講)</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1><?php echo h($result['name']) ?>｜<?php echo h($room[$result['classroom_id']]) ?>(<?php echo h(format_date($result['start_date'], 2)) ?>開講)</h1>
        <form class="row card-body bg-light m-2" action="./course_del_do.php" method="post" onsubmit="return confirm('このコースを削除してよろしいですか？')">
            <dl>
                <dt class="col-sm-3">コース名</dt>
                <dd class="col-sm-9"><?php echo h($result['name']) ?></dd>


                <dt class="col-sm-3">教室</dt>
                <dd class="col-sm-9"><?php echo h($room[$result['classroom_id']]) ?></dd>

                <dt class="col-sm-3">期間</dt>
                <dd class="col-sm-9"><?php echo h(format_date($result['start_date'], 2)) ?>～<?php echo h(format_date($result['end_date'], 2)) ?></dd>


                <dt class="col-sm-3">区分</dt>
                <dd class="col-sm-9"><?php echo h($type[$result['course_type']]) ?></dd>
            </dl>
            <div>
                <a href="./course_edit.php?course_id=<?php echo h($result['id']) ?>" class="btn btn-outline-secondary  d-inline-block">情報を修正</a>
                <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
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
                        <th scope="col">ログインID</th>

                        <th scope="col">最新の予約日時</th>
                        <th scope="col">在籍状況</th>
                        <th scope="col" colspan="2">情報変更・予約一覧</th>
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
                            <td><?php echo h($student['login_id']) ?></td>

                            <td>
                                <?php if (!empty($reservation_result)): ?>
                                    <?php foreach ($reservation_result as $reserve): ?>
                                        <?php $text = "予約はありません" ?>
                                        <?php if ($student['id'] == $reserve['student_id']): ?>
                                            <?php $text = h(format_date($reserve['date'], 4)) . "&nbsp;" . h($slot[$reserve['slot_index']]) ?>
                                            <?php break;  ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php echo $text; ?>
                                <?php else: ?>
                                    予約はありません
                                <?php endif; ?>
                            </td>
                            <td><?php echo h($enrollments[$student['enrollment_id']]) ?></td>
                            <td colspan="2"><a href="./student_edit.php?student_id=<?php echo h($student['id']) ?>" class="btn btn-primary d-inline-block">変更・予約一覧</a></td>
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

            <a href="./student_add.php?course_id=<?php echo h($course_id) ?>" class="btn btn-outline-secondary  d-inline-block">学生を追加（手入力）</a>

            <form action="student_import_do.php" method="post" enctype="multipart/form-data" class="card-body bg-light p-2">
                <input type="file" name="csv_file" accept=".csv" required>
                <input type="hidden" name="course_id" value="<?php echo h($course_id); ?>">
                <input type="hidden" name="start_year" value="<?php echo h(format_date($result['start_date'], 5)); ?>">
                <input type="hidden" name="start_month" value="<?php echo h(format_date($result['start_date'], 6)); ?>">
                <input type="hidden" name="room" value="<?php echo h($room[$result['classroom_id']]); ?>">
                <button type="submit" class="btn btn-outline-secondary  d-inline-block">学生を追加（CSV読み込み）</button>
                <p>※予約が既に入っている場合はCSVでの読み込みを実行できません</p>
            </form>


        </div>

        <?php if ($result['course_type'] == 1): ?>
            <p class="opacity-50 p-2 m-1 bg-secondary text-light fw-bold rounded">対象のコースではないため、一括での予約は実行できません</p>
        <?php elseif (!$alreadyExists): ?>
            <a href="./required_add.php?course_id=<?php echo h($result['id']) ?>" class="btn btn-outline-secondary d-inline-block mt-2 mb-2">必須キャリコンの一括予約</a>
        <?php else: ?>
            <p class="opacity-50 p-2 m-1 bg-secondary text-light fw-bold rounded">既に予約が作成されているため、一括での予約は実行できません</p>
        <?php endif; ?>
    </section>
</body>

</html>