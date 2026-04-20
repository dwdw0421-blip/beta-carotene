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

try {
    //学生情報を取得
    $sql = 'SELECT * FROM m_students WHERE id  = :id AND is_deleted = 0';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $student_result = $stmt->fetch(PDO::FETCH_ASSOC);


    //学生の予約情報を取得
    $sql = 'SELECT 
    carcon_reservation_details.student_id as student_id,
    carcon_reservation_details.meeting_type as meeting_type_id,
    m_meeting_types.name as meeting_type_name,
    carcon_reservation_details.slot_index as slot_index,
    carcon_lines.date as date
    FROM carcon_reservation_details 
    INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
    INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    INNER JOIN m_meeting_types ON carcon_reservation_details.meeting_type = m_meeting_types.id
    WHERE carcon_reservation_details.student_id = :student_id
    ORDER BY carcon_lines.date';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //学生の変更・キャンセル申請情報を取得
    $sql = 'SELECT ';
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
    <title>ユーザー｜TOP</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5 d-md-flex flex-row">
        <div class="col-md-6">
            <!-- 次回の予約日時sec -->
            <section class="user-wrapper mb-5">
                <h2 class="user-section_title mb-5 text-center">
                    次回の予約日時
                </h2>

                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコン（必須面談）</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="fw-bold fs-5">
                                <dd>
                                    <?php if (!empty($reservation_result)): ?>
                                        <?php
                                        $latest_reservation = $reservation_result[0];
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    <?php else: ?>
                                        予約はありません
                                    <?php endif; ?>
                                </dd>
                            </div>
                        </div>

                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">形式</dt>
                            <dd class="fw-bold fs-5">
                                <?php if (!empty($reservation_result)): ?>
                                    <?php
                                    $latest_reservation = $reservation_result[0];
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                <?php else: ?>
                                    予約はありません
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="user-card  px-4 py-4 shadow rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコン（任意面談）</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="fw-bold fs-5">
                                <dd>
                                    <?php if (!empty($reservation_result)): ?>
                                        <?php
                                        $latest_reservation = $reservation_result[0];
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    <?php else: ?>
                                        予約はありません
                                    <?php endif; ?>
                                </dd>
                            </div>
                        </div>

                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">形式</dt>
                            <dd class="fw-bold fs-5">
                                <?php if (!empty($reservation_result)): ?>
                                    <?php
                                    $latest_reservation = $reservation_result[0];
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                <?php else: ?>
                                    予約はありません
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- 申請ステータスsec -->
            <section class="user-wrapper mb-7">
                <h2 class="user-section_title text-center mb-5">申請ステータス</h2>
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold fs-5">変更申請</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                            <div class="d-flex flex-row fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                            </div>
                        </div>

                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                            <dd class="fw-bold fs-5">承認待ち or 承認済み or 棄却</dd>
                        </div>

                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">メッセージ</dt>
                            <dd class="fw-bold fs-5">棄却のため、再申請をお願いします。</dd>
                        </div>
                    </dl>
                </div>

                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold">キャンセル申請</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                            <div class="d-flex flex-row gap-4 fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                            </div>
                        </div>

                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                            <dd class="fw-bold fs-5">承認待ち or 承認済み</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </div>

        <!-- カレンダーsec -->
        <div class="col-md-6">
            <?php
            include('calendar.php')
            ?>
        </div>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>