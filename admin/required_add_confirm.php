<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
$course_id = $_POST['course_id'];

try {
    // コース情報を取得
    $sql = 'SELECT * FROM m_courses WHERE m_courses.id  = :course_id AND m_courses.is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>必須キャリコンの一括予約</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>必須キャリコンの一括予約</h1>
        <p>選択中のコース：<?php echo h($result['name']) ?>
            (<?php echo h(format_date($result['start_date'], 2)) ?>開講｜<?php echo h($room[$result['classroom_id']]) ?>)
        </p>

        <form action="./required_add_do.php" method="post">
            <div>
                <!-- 1回目A -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">1回目A</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
                <!-- 1回目B -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">1回目B</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
            </div>
            <div>
                <!-- 2回目A -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">2回目A</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
                <!-- 2回目B -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">2回目B</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
            </div>

            <div>
                <!-- 3回目A -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">3回目A</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
                <!-- 3回目B -->
                <div class="mb-2">
                    <label class="form-label" for="start_date">3回目B</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
            </div>
            <!-- 確認画面へ -->
            <input class="btn btn-outline-secondary  d-inline-block" type="button" value="前の画面に戻る" onclick="history.back()">

            <input class="btn btn-primary" type="submit" value="この日程で予約する">

        </form>


    </section>
</body>

</html>