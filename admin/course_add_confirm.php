<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

//コース追加画面での入力内容を取得
$name = $_POST['name'];
$classroom_id = $_POST['classroom_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$course_type = $_POST['course_type'];


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
    <title>コースを追加｜確認画面</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>コースを追加｜確認画面</h1>
        <form action="./course_add_do.php" method="post">
            <!-- コース名 -->
            <div class="mb-2">
                <p class="form-label">コース名</p>
                <p><?php echo h($name); ?></p>
                <input type="hidden" name="name" id="name" value="<?php echo h($name) ?>">
            </div>

            <!-- 教室ID -->
            <div class=" mb-2">
                <p class="form-label" for="classroom_id">教室</p>
                <?php $roomlist = get_classrooms_list(); ?>
                <p><?php echo h($roomlist[$classroom_id]); ?></p>
                <input type="hidden" name="classroom_id" id="classroom_id" value="<?php echo h($classroom_id) ?>">

                </select>
            </div>

            <!-- 入校日 -->
            <div class="mb-2">
                <p class="form-label" for="start_date">入校日</p>
                <p><?php echo h($start_date); ?></p>
                <input type="hidden" name="start_date" id="start_date" value="<?php echo h($start_date) ?>">
            </div>

            <!-- 修了日 -->
            <div class="mb-2">
                <p class="form-label" for="end_date">修了日</p>
                <p><?php echo h($end_date); ?></p>
                <input type="hidden" name="end_date" id="end_date" value="<?php echo h($end_date) ?>">
            </div>

            <!-- コース種別 -->
            <?php $types_list = get_course_types_list(); ?>
            <div class="mb-2">
                <p class="form-label" for="course_type">コース種別</p>
                <p><?php echo h($types_list[$course_type]) ?></p>
                <input type="hidden" name="course_type" id="course_type" value="<?php echo h($course_type) ?>">

            </div>



            <button type="submit" class="btn btn-primary">コースを追加</button>
        </form>
    </section>
</body>

</html>