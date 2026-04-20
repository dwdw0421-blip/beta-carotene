<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

// 予約画面から送信されたデータを変数に代入
// 三項演算子は、「このページを直接開いた場合」に起きるエラー対策
$day = isset($_POST["day"]) ? $_POST["day"] : "";
$time = isset($_POST["time"]) ? $_POST["time"] : "";
$type = isset($_POST["radio"]) ? $_POST["radio"] : "";
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php
    include('head_link.php');
    ?>
    <title>ユーザー｜予約内容確認</title>
</head>

<body>
    <?php
    include('header.php'); ?>
    <main>
        <section class="wrapper">
            <h1>予約内容確認</h1>

            <div class="reserve-card">
                <p class="category">任意</p>
                <label class="item-name">面談希望日</label>
            </div>

        </section>
    </main>
</body>

<!-- ボトムバー -->
<?php
include('bottom_bar.php')
?>
</body>

</html>