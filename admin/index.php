<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="../css/style.css">
    <title>申請内容一覧</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>申請内容一覧</h1>
        <div>
            <p>承認待ちリスト</p>
            <ul>
                <li>TODO:DBから申請レコード反映予定</li>
            </ul>
        </div>
        <div>
            <p>対応済みリスト</p>
            <ul>
                <li>TODO:DBから申請レコード反映予定</li>
            </ul>
        </div>
    </section>
</body>

</html>