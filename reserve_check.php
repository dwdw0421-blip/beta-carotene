<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

// 予約画面から送信されたデータを変数に代入
// 三項演算子は、「このページを直接開いた場合」に起きるエラー対策
$day = $_POST["day"] ?? "";
$time = $_POST["time"] ?? "";
$type = $_POST["radioDefault"]  ?? "";

// 項目が空だった時
if ($day === "" && $time === "" && $type === "") {
    header('location:reserve.php');
    exit();
}
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
                <div class="confirm-item-list">
                    <div class="confirm-item">
                        <label class="con-item-name">面談希望日</label>
                        <span class="select-item"><?php echo h(format_date($day, 4)); ?></span>
                    </div>

                    <div class="confirm-item">
                        <label class="con-item-name">面談希望時刻</label>
                        <span class="select-item"><?php echo h($time); ?></span>
                    </div>

                    <div class="confirm-item">
                        <label class="con-item-name">面談形式</label>
                        <span class="select-item"><?php echo h($type); ?></span>
                    </div>
                </div>

                <form action="reserve_do.php" method="POST">
                    <input type="hidden" name="day" value="<?php echo h($day); ?>">
                    <input type="hidden" name="time" value="<?php echo h($time); ?>">
                    <input type="hidden" name="type" value="<?php echo h($type); ?>">
                    <div class="confirm-check" style="text-align: center; margin-bottom: 20px;">
                        <label>
                            <input type="checkbox" name="check" style="accent-color: orange;" required> 全ての内容を確認しました。
                        </label>
                    </div>
                    <div class="btn">
                        <button type="button" id="return-btn" onclick="history.back()">戻る</button>
                        <button type=" submit" id="reserve">予約する</button>

                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>

</body>

</html>