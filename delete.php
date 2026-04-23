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

$id = isset($_GET["id"]) ? (int)$_GET["id"] : "";
if (empty($id)) {
    header("location: edit.php");
    exit();
}

try {
    $sql = 'SELECT 
    carcon_reservation_details.id as reservation_id,
    m_meeting_types.name as meeting_type_name,
    carcon_reservation_details.slot_index as slot_index,
    carcon_reservation_details.is_plus_carcon as is_plus_carcon,
    carcon_lines.date as date
    FROM carcon_reservation_details 
    INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
    INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    INNER JOIN m_meeting_types ON carcon_reservation_details.meeting_type = m_meeting_types.id
    WHERE carcon_reservation_details.id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>取消内容確認</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5">
        <div class="user-wrapper mb-7">
            <h2 class="user-section_title mb-5 text-center text-danger">
                取消内容確認
            </h2>

            <div class="user-card px-4 py-4 shadow mb-3 rounded-4 m-auto d-flex flex-column align-items-center" style="max-width: 500px;">

                <h3 class="mb-4 fw-bold">キャリコン＋（任意面談）</h3>
                <p class="text-muted small mb-5 text-center">
                    内容を確認し、チェックを入れてください。
                </p>

                <form action="delete_do.php" method="POST" onsubmit="return confirm('本当に取消申請を送信してもよろしいですか?')">
                    <dl>
                        <div class="mb-4">
                            <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                <input class="form-check-input mt-0" type="checkbox" id="check_date" required>
                                <label class="form-check-label" for="check_date">予約日時</label>
                            </dt>

                            <dd class="fw-bold fs-5 ps-4">
                                <?php
                                echo h(format_date($reservation_result[0]['date'], 4));
                                ?>
                            </dd>
                        </div>

                        <div class="mb-4">
                            <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                <input class="form-check-input mt-0" type="checkbox" id="check_time" required>
                                <label class="form-check-label" for="check_time">予約時間</label>
                            </dt>
                            <dd class="fw-bold fs-5 ps-4">
                                <?php
                                $latest_reservation = $reservation_result[0];
                                echo h(get_slot_time_by_index($latest_reservation['slot_index']));
                                ?>
                            </dd>
                        </div>

                        <div class="mb-5">
                            <dt class="user-card_subtitle mb-2 fs-6 d-flex align-items-center gap-3">
                                <input class="form-check-input mt-0" type="checkbox" id="check_type" required>
                                <label class="form-check-label" for="check_type">面談形式</label>
                            </dt>

                            <dd class="fw-bold fs-5 ps-4">
                                <?php
                                $latest_reservation = $reservation_result[0];
                                echo h($latest_reservation['meeting_type_name']);
                                ?>
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

                        <input type="hidden" name="id" value="<?php echo isset($reservation_result[0]['reservation_id']) ? h($reservation_result[0]['reservation_id']) : ''; ?>">
                        <button type="submit" class="btn btn-danger py-2">
                            取消申請を送信
                        </button>
                    </div>
                </form>
            </div>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php');
    ?>
</body>

</html>