<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

// 予約画面から送信されたデータを変数に代入
// 三項演算子は、「このページを直接開いた場合」に起きるエラー対策

$day = $_POST["day"] ?? "";
$time = $_POST["time"] ?? "";
$type = $_POST["radioDefault"]  ?? "";
$login_id = $_SESSION['id'] ?? null;

$slot_time_list = get_slot_list();
$type_name = "未選択";
if ($type !== "") {
    $sql_type = "SELECT name FROM m_meeting_types WHERE id = :id";
    $stmt_type = $db->prepare($sql_type);
    $stmt_type->bindValue(':id', $type, PDO::PARAM_INT);
    $stmt_type->execute();
    $type_data = $stmt_type->fetch(PDO::FETCH_ASSOC);

    if ($type_data) {
        $type_name = $type_data['name'];
    }
}

$slot_time_list = get_slot_list();
$slot_time = $slot_time_list[$time] ?? "未選択";

// 項目が空だった時（$type で判定）
if ($day === "" || $time === "" || $type === "") {
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
    <title>予約内容確認</title>
</head>

<body>
    <?php
    include('header.php'); ?>
    <main>

        <?php if (isset($_SESSION['err'])): ?>
            <div class="alert alert-danger" style="color: red; font-weight: bold; text-align: center; background: #fff0f0; padding: 10px; border: 1px solid red; margin: 20px auto; width: 80%;">
                <?php
                echo h($_SESSION['err']);
                unset($_SESSION['err']); // 一度表示したら消す
                ?>
            </div>
        <?php endif; ?>

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
                        <span class="select-item"><?php echo h($slot_time); ?></span>
                    </div>

                    <div class="confirm-item">
                        <label class="con-item-name">面談形式</label>
                        <span class="select-item"><?php echo h($type_name); ?></span>
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