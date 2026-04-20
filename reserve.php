<?php
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();
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
    <?php
    include('header.php');

    $sql = "SELECT * FROM m_meeting_types";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 現在選択されている値（エラーで戻ってきた時用）
    $selected_type = $_POST['radioDefault'] ?? '';

    $error_msg = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $day = $_POST["day"] ?? "";
        $time = $_POST["time"] ?? "";
        $radio = $_POST["radioDefault"] ?? "";

        if ($day == "選択してください" || $time == "選択してください" || empty($radio)) {
            $error_msg = "未選択の項目があります。選択してください。";
        } else {
            header('location:reserve_check.php');
            exit();
        }
    }
    ?>

    <main>
        <section class="wrapper">
            <h1>面談予約</h1>

            <form action="reserve_check.php" method=" POST">
                <div class="reserve-card">

                    <?php if (!empty($error_msg)): ?>
                        <p style="color: red; font-weight: bold;"><?php echo $error_msg; ?></p>
                    <?php endif; ?>

                    <p class="category">任意</p>

                    <div class="item">
                        <div class="day-item">
                            <label class="item-name">面談希望日</label>
                            <select class="form-select" aria-label="Default select example" name="day">
                                <option selected class="defo">選択してください</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>

                        <div class="time-item">
                            <label class="item-name">面談希望時刻</label>
                            <select class="form-select" aria-label="Default select example" name="time">
                                <option selected class="defo">選択してください</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
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
                                            <?php
                                            if ($selected_type === $type['name']) {
                                                echo 'checked';
                                            }
                                            ?>>
                                        <label class="form-check-label" for="type_<?php echo h($type['id']); ?>">
                                            <?php echo h($type['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (!empty($errors['radioDefault'])): ?>
                                <p class="error" style="color: red; font-size: 0.8rem;"><?php echo $errors['radioDefault']; ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="btn">
                            <button type="button" id="return-btn" onclick="history.back()">戻る</button>
                            <button type="submit" id="next-btn">確認画面に進む</button>
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