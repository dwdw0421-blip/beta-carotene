<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();

$day = $_POST["day"] ?? "";
$time = $_POST["time"] ?? "";
$selected_type = $_POST['radioDefault'] ?? "";
$error_msg = "";

$sql = "SELECT * FROM m_meeting_types";
$stmt = $db->prepare($sql);
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $is_valid = true;

    if ($day === "" || $time === "") {
        $error_msg = "未選択の項目があります。";
        $is_valid = false;
    }

    if ($is_valid) {
        header('location: reserve_check.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php
    include('head_link.php');
    ?>
    <title>ユーザー｜面談予約</title>
</head>

<body>
    <?php include('header.php'); ?>
    <main>
        <section class="wrapper">
            <h1>面談予約</h1>

            <form action="reserve_check.php" method="POST">
                <div class="reserve-card">
                    <p class="category">キャリコン</p>

                    <?php if (!empty($error_msg)): ?>
                        <p style="color: red; font-weight: bold;text-align: center;"><?php echo h($error_msg); ?></p>
                    <?php endif; ?>

                    <div class="item">
                        <div class="day-item">
                            <label class="item-name">面談希望日</label>
                            <select class="form-select" name="day" required>
                                <option value="" <?php if ($day == "選択してください" || $day == "") echo 'selected'; ?> disabled>選択してください</option>
                                <option value="1月" <?php if ($day == "1") echo 'selected'; ?>>sain</option>
                                <option value="2月" <?php if ($day == "2") echo 'selected'; ?>>cosin</option>
                                <option value="3月" <?php if ($day == "3") echo 'selected'; ?>>tangent</option>
                            </select>
                        </div>

                        <div class="time-item">
                            <label class="item-name">面談希望時刻</label>
                            <select class="form-select" name="time" required>
                                <option value="" <?php if ($time == "選択してください" || $time == "") echo 'selected'; ?> disabled>選択してください</option>
                                <option value="1" <?php if ($time == "1") echo 'selected'; ?>>sain</option>
                                <option value="2" <?php if ($time == "2") echo 'selected'; ?>>cosin</option>
                                <option value="3" <?php if ($time == "3") echo 'selected'; ?>>tangent</option>
                            </select>
                        </div>

                        <div class="check-group">
                            <label class="item-name">面談形式</label>
                            <div class="d-flex gap-5">
                                <?php foreach ($types as $type): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="radioDefault"
                                            id="type_<?php echo h($type['id']); ?>"
                                            value="<?php echo h($type['name']); ?>"
                                            required
                                            <?php if ($selected_type === $type['name']) echo 'checked'; ?>>
                                        <label class="form-check-label" for="type_<?php echo h($type['id']); ?>">
                                            <?php echo h($type['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="btn">
                            <button type="button" id="return-btn" onclick="history.back()">戻る</button>
                            <button type="submit" id="next-btn">確認画面に進む
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>