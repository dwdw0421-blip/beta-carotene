<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';
$db = db_connect();
$login_id = $_SESSION['id'];

$day = $_POST["day"] ?? "";
$select_time = $_POST["time"] ?? "";
$selected_type = $_POST['radioDefault'] ?? "";
$slot_time = get_slot_list();
$error_msg = "";

$sql_student = "SELECT * FROM m_students WHERE id = :login_id";
$stmt_student = $db->prepare($sql_student);
$stmt_student->bindParam(':login_id', $login_id, PDO::PARAM_INT);
$stmt_student->execute();
$student = $stmt_student->fetch(PDO::FETCH_ASSOC);

/* 日付 */
$sql_dates = "SELECT DISTINCT date FROM carcon_lines ORDER BY date ASC";
$stmt_dates = $db->query($sql_dates);
$dates = $stmt_dates->fetchAll(PDO::FETCH_ASSOC);

/* 対面 or ZOOM */
$sql_types = "SELECT * FROM m_meeting_types";
$stmt_types = $db->query($sql_types);
$types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $is_valid = true;

    if ($day === "" || $select_time === "") {
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
    <title>面談予約</title>
</head>

<body>
    <?php include('header.php'); ?>
    <main>
        <section class="wrapper">
            <h1>面談予約</h1>

            <form action="reserve_check.php" method="POST">
                <div class="reserve-card">

                    <?php if (!empty($error_msg)): ?>
                        <p style="color: red; font-weight: bold;text-align: center;"><?php echo h($error_msg); ?></p>
                    <?php endif; ?>

                    <div class="item">
                        <div class="day-item">
                            <label class="item-name">面談希望日</label>
                            <select class="form-select" name="day" required>

                                <?php if (empty($dates)): ?>
                                    <option value="" selected disabled>現在予約可能な日はありません</option>
                                <?php else: ?>
                                    <option value="" <?php if ($day == "") echo 'selected'; ?> disabled>選択してください</option>

                                    <?php foreach ($dates as $row): ?>
                                        <option value="<?php echo h($row['date']); ?>" <?php if ($day == $row['date']) echo 'selected'; ?>>
                                            <?php echo h($row['date']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="time-item">
                            <label class="item-name">面談希望時刻</label>
                            <select class="form-select" name="time" required>
                                <option value="" <?php if ($select_time == "") echo 'selected'; ?> disabled>選択してください</option>

                                <?php foreach ($slot_time as $key => $value): ?>
                                    <option value="<?php echo h($key); ?>" <?php if ($select_time == $key) echo 'selected'; ?>>
                                        <?php echo h($value); ?>
                                    </option>
                                <?php endforeach; ?>
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
                                            value="<?php echo h($type['id']); ?>"
                                            required
                                            <?php if ($selected_type === $type['id']) echo 'checked'; ?>>
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
    include('bottom_bar.php');
    ?>
</body>

</html>