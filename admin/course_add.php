<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

$sql = "SELECT * FROM m_classrooms ORDER BY id ASC";

$stmt = $db->prepare($sql);
$stmt->execute();

$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>コースを追加</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>コースを追加</h1>
        <form action="./course_add_confirm.php" method="post">
            <!-- コース名 -->
            <div class="mb-2">
                <label class="form-label" for="name">コース名</label>
                <input class="form-control" type="text" name="name" id="name">
            </div>

            <!-- 教室ID -->
            <div class="mb-2">
                <label class="form-label" for="classroom_id">教室</label>
                <select class="form-select" name="classroom_id">
                    <?php $roomlist = get_classrooms_list(); ?>
                    <?php
                    foreach ($classrooms as $room):
                    ?>
                        <option value="<?php echo h($room['id']) ?>"><?php echo h($room['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 入校日 -->
            <div class="mb-2">
                <label class="form-label" for="start_date">入校日</label>
                <input class="form-control" type="date" name="start_date" id="start_date">
            </div>

            <!-- 修了日 -->
            <div class="mb-2">
                <label class="form-label" for="end_date">修了日</label>
                <input class="form-control" type="date" name="end_date" id="end_date">
            </div>

            <!-- コース種別 -->
            <?php $types_list = get_course_types_list(); ?>
            <div class="mb-2">
                <label class="form-label" for="course_type">コース種別</label>
                <select class="form-select" name="course_type">

                    <?php
                    foreach ($types_list as $key => $type):
                    ?>
                        <option value="<?php echo h($key) ?>"><?php echo h($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 確認画面へ -->
            <input class="btn btn-primary" type="submit" value="入力内容を確認">

        </form>
    </section>
</body>

</html>