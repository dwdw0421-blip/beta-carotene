<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
//学生追加画面での入力内容を取得
$student_no = $_POST['student_no'];
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$password = $_POST['password'];
$enrollment_id = $_POST['enrollment_id'];
$course_id = $_POST['course_id'];



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
    <title>学生を追加｜確認画面</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>学生を追加｜確認画面</h1>
        <form action="./student_add_do.php" method="post">
            <!-- 出席番号-->
            <div class="mb-2">
                <p class="form-label" for="student_no">出席番号</p>
                <p><?php echo h($student_no); ?></p>
                <input class="form-control" type="hidden" pattern="[0-9]*" name="student_no" id="student_no" value="<?php echo h($student_no); ?>">

            </div>

            <!-- 苗字-->
            <div class="mb-2">
                <p class="form-label" for="last_name">苗字</p>
                <p><?php echo h($last_name); ?></p>
                <input class="form-control" type="hidden" name="last_name" value="<?php echo h($last_name); ?>">
            </div>


            <!-- 名前-->
            <div class="mb-2">
                <p class="form-label" for="first_name">名前</p>
                <p><?php echo h($first_name); ?></p>
                <input class="form-control" type="hidden" name="first_name" value="<?php echo h($first_name); ?>">
            </div>

            <!-- パスワード-->
            <div class="mb-2">
                <p class="form-label" for="password">パスワード</p>
                <p><?php echo h($password); ?></p>
                <input class="form-control" type="hidden" name="password" value="<?php echo h($password); ?>">
            </div>

            <!-- 在籍ステータス -->
            <?php $enrollments = get_enrollments_list() ?>
            <div class="mb-2">
                <p class="form-label" for="enrollment_id">在籍ステータス</p>
                <p><?php echo h($enrollments[$enrollment_id]); ?></p>
                <input type="hidden" name="enrollment_id" id="enrollment_id" value="<?php echo h($enrollment_id); ?>">
            </div>


            <!-- 登録処理へ -->
            <input class="btn btn-outline-secondary  d-inline-block" type="button" value="前の画面に戻る" onclick="history.back()">
            <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
            <input class="btn btn-primary  d-inline-block" type="submit" value="学生を追加">

        </form>
    </section>
    <script src="../js/student_add.js"></script>
</body>

</html>