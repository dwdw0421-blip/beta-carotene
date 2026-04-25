<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

$sql = "SELECT * FROM m_classrooms ORDER BY id ASC";

$stmt = $db->prepare($sql);
$stmt->execute();

$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
$course_id = htmlspecialchars($_GET['course_id']);
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
    <title>学生を追加</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>学生を追加</h1>
        <form action="./student_add_confirm.php" method="post">
            <!-- 出席番号-->
            <div class="mb-2">
                <label class="form-label" for="student_no">出席番号</label>
                <input class="form-control" type="text" pattern="[0-9]*" name="student_no" id="student_no">
                <p class="fs-6 ">※半角数字のみ入力可能です</p>
            </div>

            <!-- 苗字-->
            <div class="mb-2">
                <label class="form-label" for="last_name">苗字</label>
                <input class="form-control" type="text" name="last_name">
            </div>


            <!-- 名前-->
            <div class="mb-2">
                <label class="form-label" for="first_name">名前</label>
                <input class="form-control" type="text" name="first_name">
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
                        <option value="<?php echo h($key) ?>"><?php echo h($enrollment) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <!-- 確認画面へ -->
            <input type="hidden" name="course_id" value="<?php echo h($course_id) ?>">
            <input class="btn btn-primary" type="submit" value="入力内容を確認">

        </form>
    </section>
    <script src="../js/student_add.js"></script>
</body>

</html>