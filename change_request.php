<?php
session_start();
require_once __DIR__ . '/./includes/functions.php';

//ログインしていない場合はログイン画面へ
// if (!isset($_SESSION['id'])) {
//     header('location:index.php');
//     exit();
// }

$db = db_connect();
$login_id = $_SESSION['id'];

$is_request_detail = isset($_POST['request_detail']) ? (int)$_POST['request_detail'] : "";
$is_request_meeting_type = isset($_POST['request_type']) ? (int)$_POST['request_type'] : "";
if ($is_request_detail === 1) {
    $id = isset($_POST['currentDetailId_detail']) ? $_POST['currentDetailId_detail'] : "";
    $change_detail_id = isset($_POST['changeDetailData']) ? (int)$_POST['changeDetailData'] : "";
} else if ($is_request_meeting_type === 1) {
    $id = isset($_POST['currentDetailId_type']) ? $_POST['currentDetailId_type'] : "";
    $change_meeting_type = isset($_POST['radioType']) ? (int)$_POST['radioType'] : "";
} else {
    exit('エラー');
}

try {
    $sql = 'SELECT 
                carcon_lines.date,
                carcon_reservation_details.slot_index,
                carcon_reservation_details.is_plus_carcon,
                m_meeting_types.name AS meeting_type,
                CONCAT(m_students.last_name , " " , m_students.first_name) AS student_name
            FROM carcon_reservation_details 
            INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
            INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
            INNER JOIN m_meeting_types ON carcon_reservation_details.meeting_type = m_meeting_types.id
            INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id
            WHERE carcon_reservation_details.id = :id
            ';
    $stmt = $db->prepare($sql);
    if ($is_request_meeting_type === 1) {
        // 形式変更の時は自分の情報を表示
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } elseif ($is_request_detail === 1) {
        // 日時変更の時は相手の情報を表示
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php
    include('head_link.php');
    ?>
    <title>変更内容確認</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5">
        <div class="user-wrapper mb-7">
            <h2 class="user-section_title mb-5 text-center">
                変更内容確認
            </h2>

            <?php if ($is_request_meeting_type === 1): ?>
                <!-- 面談形式の変更 -->
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4 m-auto d-flex flex-column align-items-center" style="max-width: 500px;">
                    <?php
                    //必須キャリコンがtrue
                    echo $reservation_result['is_plus_carcon'] === 0
                        ? '<h3 class="titele-carcon mb-4 fw-bold">キャリコン（必須面談）</h3>'
                        : ' <h3 class="mb-4 fw-bold">キャリコン＋（任意面談）</h3>';
                    ?>
                    <p class="text-muted small mb-5 text-center">
                        内容を確認し、チェックを入れてください。
                    </p>

                    <form action="./chage_meetingtype_do.php" method="POST">
                        <dl>
                            <div class="mb-4">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_date" required>
                                    <label class="form-check-label" for="check_date">予約日時</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h(format_date($reservation_result["date"], 4)); ?>
                                </dd>
                            </div>

                            <div class="mb-4">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_time" required>
                                    <label class="form-check-label" for="check_time">予約時間</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h(get_slot_time_by_index($reservation_result["slot_index"])); ?>
                                </dd>
                            </div>

                            <div class="mb-5">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_type" required>
                                    <label class="form-check-label" for="check_type">変更後の面談形式</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h($reservation_result["meeting_type"]); ?>
                                </dd>
                            </div>
                        </dl>

                        <div class="mb-4 d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0" type="checkbox" id="check_confirm_all" required>

                            <label class="form-check-label" for="check_confirm_all">
                                全ての内容を確認しました。
                            </label>
                        </div>

                        <div class="d-flex flex-row gap-3">
                            <a class="btn btn-secondary py-2 " href="./edit.php">
                                戻る
                            </a>

                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="change_meeting_type" value="<?php echo $change_meeting_type; ?>">
                            <button type="submit" class="btn btn-primary py-2 m-0">
                                変更申請を送信
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif ($is_request_detail === 1): ?>
                <!-- 日時変更 -->
                <div
                    class="user-card px-4 py-4 shadow mb-4 rounded-4 m-auto d-flex flex-column align-items-center"
                    style="max-width: 500px;">
                    <?php
                    //必須キャリコンがtrue
                    echo $reservation_result['is_plus_carcon'] === 0
                        ? '<h3 class="mb-4 fw-bold">キャリコン（必須面談）</h3>'
                        : ' <h3 class="titele-carconplus mb-4 fw-bold">キャリコン＋（任意面談）</h3>';
                    ?>
                    <p class="text-muted small mb-5 text-center">
                        内容を確認し、チェックを入れてください。
                    </p>

                    <form action="./chage_date_do.php" method="POST">
                        <dl>
                            <div class="mb-4">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_date" required>
                                    <label class="form-check-label" for="check_date">変更後の希望日</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h(format_date($reservation_result["date"], 4)); ?>
                                </dd>
                            </div>

                            <div class="mb-4">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_time" required>
                                    <label class="form-check-label" for="check_time">変更後の希望時間</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h(get_slot_time_by_index($reservation_result["slot_index"])); ?>
                                </dd>
                            </div>

                            <div class="mb-5">
                                <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="check_target" required>
                                    <label class="form-check-label" for="check_target">交渉相手</label>
                                </dt>
                                <dd class="fw-bold fs-5 ps-4">
                                    <?php echo h($reservation_result["student_name"]); ?>
                                </dd>
                            </div>
                        </dl>

                        <div class="mb-4 d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0" type="checkbox" id="check_type" required>

                            <label class="form-check-label" for="check_type">
                                全ての内容を確認しました。
                            </label>
                        </div>


                        <div class="d-flex flex-row gap-3">
                            <a class="btn btn-secondary py-2 " href="./edit.php">
                                戻る
                            </a>

                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="change_detail_id" value="<?php echo $change_detail_id; ?>">
                            <button type="submit" class="btn btn-primary py-2 m-0">
                                変更申請を送信
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>