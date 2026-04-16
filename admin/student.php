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
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
$type = get_course_types_list();
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
        <h1><?php echo h($course['classroom_name']) ?>(<?php echo h(format_date($course['start_date'], 2)) ?>開講)</h1>
        <form class="row card-body bg-light" action="./course_del_do.php" method="post" onsubmit="return confirm('このコースを削除してよろしいですか？')">

            <dt class="col-sm-3">コース名</dt>
            <dd class="col-sm-9"><?php echo h($result['name']) ?></dd>


            <dt class="col-sm-3">教室</dt>
            <dd class="col-sm-9"><?php echo h($course['classroom_name']) ?></dd>

            <dt class="col-sm-3">期間</dt>
            <dd class="col-sm-9"><?php echo h(format_date($course['start_date'], 2)) ?>～<?php echo h(format_date($result['end_date'], 2)) ?></dd>


            <dt class="col-sm-3">区分</dt>
            <dd class="col-sm-9"><?php echo h($type[$result['course_type']]) ?></dd>

            <div>
                <a href="./course_edit.php?courses_id=<?php echo h($result['id']) ?>" class="btn btn-outline-secondary  d-inline-block">情報を修正</a>
                <input type="submit" class="btn btn-outline-danger d-inline-block" value="コースを削除">
            </div>
        </form>
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
                $enrollments = get_course_enrollments_list();
                ?>
                <?php
                foreach ($student_result as $student):
                ?>
                    <tr>
                        <th scope="row"><?php echo h($student['student_no']) ?></th>
                        <td><?php echo h($student['last_name']) ?>&ensp;<?php echo h($student['first_name']) ?></td>
                        <td><?php echo h($course['classroom_name']) ?><?php echo h(sprintf('%02d', $student['student_no'])) ?></td>
                        <td><?php echo h($student['password']) ?></td>
                        <td></td>
                        <td><?php echo h($enrollments[$student['enrollment_id']]) ?></td>
                        <td><a href="./student_edit.php?student_no=<?php echo h($student['student_no']) ?>" class="btn">変更</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </section>
</body>

</html>