<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

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
    <title>管理者を追加</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>管理者を追加</h1>
        <form class="row card-body bg-light" action="./staff_add_do.php" method="post" onsubmit="return confirm('管理者データを追加しますか？')">

            <div class="mb-2">
                <label class="form-label" for="staff_id">スタッフID</label>
                <input class="form-control" type="text" name="staff_id" id="staff_id">
            </div>

            <div class="mb-2">
                <label class="form-label" for="last_name">苗字</label>
                <input class="form-control" type="text" name="last_name" id="last_name">
            </div>

            <div class="mb-2">
                <label class="form-label" for="last_name">名前</label>
                <input class="form-control" type="text" name="first_name" id="first_name">
            </div>

            <div class="mb-2">
                <label class="form-label" for="end_date">パスワード</label>
                <input class="form-control" type="password" name="password" id="password">
            </div>

            <input type="submit" class="btn btn-outline-danger d-inline-block" value="変更する">
        </form>
    </section>
</body>

</html>