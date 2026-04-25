<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';

// ログインしていない場合はログイン画面へ
if (!isset($_SESSION['id'])) {
    header('location:login.php');
    exit();
}

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
// 選択できるのは現在の日付以降
$sql_dates = "SELECT DISTINCT date FROM carcon_lines WHERE date >= CURDATE() ORDER BY date ASC";
$stmt_dates = $db->query($sql_dates);
$dates = $stmt_dates->fetchAll(PDO::FETCH_ASSOC);

/* 対面 or ZOOM */
$sql_types = "SELECT * FROM m_meeting_types";
$stmt_types = $db->query($sql_types);
$types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

/*
// NOTE: nagata-t
// ここの処理の意図が把握できず、不要と判断したのでコメントアウトしてます(´>ω<)人

// 以下の実装は「日付（$day）と時刻（$select_time）が渡されていたら、予約確認画面（reserve_check.php）へ直接遷移する」という処理になっています。
// 現時点で発生している処理の流れは以下のようになっていました。

// 1. カレンダーから「日付（$day）と時刻（$select_time）」を渡しつつ reserve.php に遷移する
// 2. reserve.php側で二つのデータが存在していたら、予約確認画面（reserve_check.php）に遷移する
// 3. 予約確認画面（reserve_check.php）に遷移するも、ただのlocationなのでデータが渡せていない遷移のため、予約確認画面（reserve_check.php）の39~42行目のチェック判定に引っかかり reserve.php にデータなしの状態で戻される
// 4. 結果、reserve.php にデータを渡しても、reserve_check.php を経由して、データなしの reserve.php が表示される

// おそらく「予約画面」に来た際に、すでにデータを持っていたら「予約確認画面」へ遷移させる意図のコードなのかなー？と思ったんですが、
// 現在の画面仕様だと「面談方式を選択させるのが reserve.php」なので、一旦この処理は無いほうが良いかな？と判断して削除（コメントアウト）しています！
// （上記に書いた通り、この処理が残っているとデータを渡してもグルグル回って無くなってしまう不具合が発生してしまう状態にもなるので）
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
*/

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