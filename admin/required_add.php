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

        <form action="./required_add_confirm.php" method="post">
            <div class="border p-2">
                <!-- 1回目A -->
                <div class="mb-2">
                    <label class="form-label" for="first_a">1回目A</label>
                    <input class="form-control" type="date" name="first_a" id="start_date">
                </div>
                <!-- 1回目B -->
                <div class="mb-2">
                    <label class="form-label" for="first_b">1回目B</label>
                    <input class="form-control" type="date" name="first_b" id="start_date">
                </div>
            </div>
            <div class="border p-2">
                <!-- 2回目A -->
                <div class="mb-2">
                    <label class="form-label" for="second_a">2回目A</label>
                    <input class="form-control" type="date" name="second_a" id="start_date">
                </div>
                <!-- 2回目B -->
                <div class="mb-2">
                    <label class="form-label" for="second_b">2回目B</label>
                    <input class="form-control" type="date" name="second_b" id="start_date">
                </div>
            </div>

            <div class="border p-2">
                <!-- 3回目A -->
                <div class="mb-2">
                    <label class="form-label" for="third_a">3回目A</label>
                    <input class="form-control" type="date" name="third_a" id="start_date">
                </div>
                <!-- 3回目B -->
                <div class="mb-2">
                    <label class="form-label" for="third_b">3回目B</label>
                    <input class="form-control" type="date" name="third_b" id="start_date">
                </div>
            </div>
            <!-- 確認画面へ -->

            <input type="hidden" name="course_id" value="<?php echo h($courses_id) ?>">
            <input class="btn btn-primary d-inline-block mt-2" type="submit" value="入力内容を確認">

        </form>


    </section>
</body>

</html>