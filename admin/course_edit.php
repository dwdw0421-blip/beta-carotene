<?php
session_start();
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
    $sql = 'SELECT carcon_reservation_details.student_id as student_id,carcon_reservation_details.meeting_type as meeting_type,carcon_reservation_details.slot_index as slot_index,carcon_lines.date as date FROM carcon_reservation_details INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id ORDER BY carcon_lines.date DESC';
    $stmt = $db->prepare($sql);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //教室マスタ情報を取得
    $sql = "SELECT * FROM m_classrooms ORDER BY id ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
$type = get_course_types_list();
$rooms = get_classrooms_list();


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
    <title>コース情報を編集</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1><?php echo h($rooms[$result['classroom_id']]) ?>(<?php echo h(format_date($result['start_date'], 2)) ?>開講)</h1>

        <dl class="row card-body">
            <dt class="col-sm-3">コース名</dt>
            <dd class="col-sm-9"><?php echo h($result['name']) ?></dd>

            <dt class="col-sm-3">教室</dt>
            <dd class="col-sm-9"><?php echo h($rooms[$result['classroom_id']]) ?></dd>

            <dt class="col-sm-3">期間</dt>
            <dd class="col-sm-9"><?php echo h(format_date($result['start_date'], 2)) ?>～<?php echo h(format_date($result['end_date'], 2)) ?></dd>


            <dt class="col-sm-3">区分</dt>
            <dd class="col-sm-9"><?php echo h($type[$result['course_type']]) ?></dd>

        </dl>
        <form action="./course_edit_confirm.php" method="post">
            <!-- コース名 -->
            <div class="mb-2">
                <label class="form-label" for="name">コース名</label>
                <input class="form-control" type="text" name="name" id="name" value="<?php echo h($result['name']) ?>">
            </div>

            <!-- 教室ID -->
            <div class="mb-2">
                <label class="form-label">教室</label>
                <select class="form-select" name="classroom_id">
                    <?php $roomlist = get_classrooms_list(); ?>
                    <?php
                    foreach ($classrooms as $room):
                    ?>
                        <option value="<?php echo h($room['id']) ?>" <?php echo  $room['id'] == $result['classroom_id'] ? 'selected' : ""; ?>><?php echo h($room['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 入校日 -->
            <div class="mb-2">
                <label class="form-label" for="start_date">入校日</label>
                <input class="form-control" type="date" name="start_date" id="start_date" value="<?php echo h($result['start_date']) ?>">
            </div>

            <!-- 修了日 -->
            <div class=" mb-2">
                <label class="form-label" for="end_date">修了日</label>
                <input class="form-control" type="date" name="end_date" id="end_date" value="<?php echo h($result['end_date']) ?>">
            </div>

            <!-- コース種別 -->
            <?php $types_list = get_course_types_list(); ?>
            <div class="mb-2">
                <label class="form-label" for="course_type">コース種別</label>
                <select class="form-select" name="course_type">

                    <?php
                    foreach ($types_list as $key => $type):
                    ?>
                        <option value="<?php echo h($key) ?>" <?php echo  $key == $result['course_type'] ? 'selected' : ""; ?>><?php echo h($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 確認画面へ -->
            <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
            <input class="btn btn-primary" type="submit" value="入力内容を確認">



        </form>


    </section>
</body>

</html>