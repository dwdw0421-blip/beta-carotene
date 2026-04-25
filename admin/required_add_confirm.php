<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
$course_id = $_POST['course_id'];
//入力日程を取得
$first_a = $_POST['first_a'];
$first_b = $_POST['first_b'];
$second_a = $_POST['second_a'];
$second_b = $_POST['second_b'];
$third_a = $_POST['third_a'];
$third_b = $_POST['third_b'];
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

            <div class="border p-2">
                <!-- 1回目A -->
                <div class="mb-2">
                    <p class="form-label" for="first_a">1回目A</p>
                    <?php echo (!empty($_POST['first_a'])) ?  h(format_date($first_a, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="first_a" value="<?php echo h($first_a) ?>">
                </div>
                <!-- 1回目B -->
                <div class="mb-2">
                    <p class="form-label" for="first_b">1回目B</p>
                    <?php echo (!empty($_POST['first_b'])) ?  h(format_date($first_b, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="first_b" value="<?php echo h($first_b) ?>">
                </div>
            </div>
            <div class="border p-2">
                <!-- 2回目A -->
                <div class="mb-2">
                    <p class="form-label" for="second_a">2回目A</p>
                    <?php echo (!empty($_POST['second_a'])) ?  h(format_date($second_a, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="second_a" value="<?php echo h($second_a) ?>">
                </div>
                <!-- 2回目B -->
                <div class="mb-2">
                    <p class="form-label" for="second_b">2回目B</p>
                    <?php echo (!empty($_POST['second_b'])) ?  h(format_date($second_b, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="second_b" value="<?php echo h($second_b) ?>">
                </div>
            </div>

            <div class="border p-2">
                <!-- 3回目A -->
                <div class="mb-2">
                    <p class="form-label" for="third_a">3回目A</p>
                    <?php echo (!empty($_POST['third_a'])) ?  h(format_date($third_a, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="third_a" value="<?php echo h($third_a) ?>">
                </div>
                <!-- 3回目B -->
                <div class="mb-2">
                    <p class="form-label" for="third_b">3回目B</p>
                    <?php echo (!empty($_POST['third_b'])) ?  h(format_date($third_b, 4)) : "選択されていません"  ?>
                    <input class="form-control" type="hidden" name="third_b" value="<?php echo h($third_b) ?>">
                </div>
            </div>
            <!-- 確認画面へ -->
            <input class="btn btn-outline-secondary d-inline-block mt-2" type="button" value="前の画面に戻る" onclick="history.back()">
            <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
            <input class="btn btn-primary d-inline-block mt-2" type="submit" value="この日程で予約する">

        </form>


    </section>
</body>

</html>