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
$message = $_SESSION['res_message'] ?? '';
$type = ['danger', 'primary'];

try {
    //学生情報を取得
    $sql = 'SELECT 
    m_students.*,
    m_courses.course_type as course_type,
    CONCAT(m_students.last_name , " " , m_students.first_name) AS student_name
    FROM m_students 
    INNER JOIN m_courses ON m_students.course_id = m_courses.id 
    WHERE m_students.id  = :id';
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
    carcon_reservation_details.is_plus_carcon as is_plus_carcon,
    carcon_lines.date as date
    FROM carcon_reservation_details 
    INNER JOIN carcon_reservations ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id 
    INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    INNER JOIN m_meeting_types ON carcon_reservation_details.meeting_type = m_meeting_types.id
    WHERE carcon_reservation_details.student_id = :student_id
    AND carcon_lines.date >= CURDATE()
    ORDER BY carcon_lines.date ASC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //学生の変更・キャンセル申請情報を取得
    $sql = 'SELECT 
    carcon_request_reservations.request_carcon_reservation_detail_id as request_id,
    carcon_request_reservations.request_status_id as request_status_id,
    carcon_request_reservations.reject_message as reject_message,
    carcon_request_reservations.created_at as request_date,
    carcon_request_reservations.request_type as request_type,
    carcon_reservation_details.slot_index as slot_index,
    m_request_statuses.name as request_status,
    carcon_lines.date as date
    FROM carcon_request_reservations 
    INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id = carcon_reservation_details.id
    INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
    INNER JOIN carcon_reservations ON carcon_reservations.carcon_reservation_detail_id = carcon_request_reservations.request_carcon_reservation_detail_id
    INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    WHERE carcon_reservation_details.student_id = :student_id
    ORDER BY carcon_request_reservations.created_at DESC
    LIMIT 1';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $request_result = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>ホーム</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5">
        <!-- ログイン成功メッセージ表示 -->
        <div class="user-wrapper message-area">
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo $type[$message['type']]; ?> alert-dismissible" role="alert">
                    <div>
                        <?php echo $message['msg']; ?>
                    </div>
                </div>
                <?php unset($_SESSION['res_message']); ?>
            <?php endif; ?>
        </div>

        <!-- キャリコンプラス予約完了後のメッセージを表示 -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-success user-wrapper mb-4 text-center" style="max-width: 350px;">
                <p class="m-0">
                    キャリコン＋の予約が完了しました！
                </p>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php elseif (!empty($_SESSION['err'])): ?>
            <div class="alert alert-danger user-wrapper mb-4 text-center" style="max-width: 350px;">
                <p class="m-0">
                    キャリコン＋の予約に失敗しました...。<br>
                    (´・ω・`)(´・ω・`)(´・ω・`)
                </p>
            </div>
            <?php unset($_SESSION['err']); ?>
        <?php endif; ?>

        <div class="d-md-flex flex-row">
            <div class="col-md-6">
                <!-- 次回の予約日時sec -->
                <section class="user-wrapper mb-7">
                    <h2 class="user-section_title mb-5 text-center">
                        次回の予約日時
                    </h2>

                    <?php
                    // 予約がある場合はデータを表示し、配列から「必須」と「プラス」の最新を1つずつ抽出
                    $must_reserve = null;
                    $plus_reserve = null;

                    if (!empty($reservation_result)) {
                        foreach ($reservation_result as $reserve) {
                            if ($reserve['is_plus_carcon'] === 0 && $must_reserve === null) {
                                $must_reserve = $reserve;
                            }
                            if ($reserve['is_plus_carcon'] === 1 && $plus_reserve === null) {
                                $plus_reserve = $reserve;
                            }
                            // 両方見つかれば終了
                            if ($must_reserve && $plus_reserve) break;
                        }
                    }
                    ?>

                    <?php
                    //求職者支援訓練かつ必須キャリコンデータがあれば表示
                    if ($student_result['course_type'] === 2 && $must_reserve):
                    ?>
                        <!-- 必須キャリコン情報を表示 -->
                        <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                            <h3 class="titele-carcon mb-4 fw-bold">キャリコン（必須面談）</h3>
                            <dl>
                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                    <div class="fw-bold fs-5">
                                        <dd>
                                            <?php
                                            echo h(format_date($must_reserve['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($must_reserve['slot_index']));
                                            ?>
                                        </dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                    <dd class="fw-bold fs-5">
                                        <?php
                                        echo h($must_reserve['meeting_type_name']);
                                        ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    <?php endif; ?>

                    <!-- キャリコンプラス情報を表示 -->
                    <div class="user-card  px-4 py-4 shadow rounded-4">
                        <h3 class="mb-4 fw-bold">キャリコン＋（任意面談）</h3>
                        <dl>
                            <?php
                            //キャリコンプラスの予約があれば表示 
                            if ($plus_reserve):
                            ?>
                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                    <div class="fw-bold fs-5">
                                        <dd>
                                            <?php
                                            echo h(format_date($plus_reserve['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($plus_reserve['slot_index']));
                                            ?>
                                        </dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                    <dd class="fw-bold fs-5">
                                        <?php
                                        echo h($plus_reserve['meeting_type_name']);
                                        ?>
                                    </dd>
                                </div>

                            <?php
                            //キャリコンプラスの予約がなければ表示
                            else:
                            ?>
                                <div class="d-flex flex-column align-items-center">
                                    <p class="text-secondary fs-5 mb-5">
                                        キャリコン＋（任意面談）の予約はありません。
                                    </p>

                                    <a class="btn btn-primary px-4 py-2 m-0" href="./reserve.php">予約はこちら</a>
                                </div>
                            <?php endif; ?>

                            <!-- 確認事項は予約の有無にかかわらず表示 -->
                            <div class="p-2 border-start border-danger border-4 bg-light rounded-end shadow-sm mt-4" style="max-width: 600px; margin: 0 auto;">
                                <div class="d-flex align-items-center gap-2 text-danger mb-1">
                                    <span class="material-symbols-outlined fs-5">warning</span>
                                    <span class="fw-bold">ご確認ください</span>
                                </div>

                                <p class="mb-0 small text-muted px-4">
                                    キャリコン＋（任意面談）は仕様上、直接の日時変更ができません。<br />
                                    お手数ですが、現在の予約を一度取り消した上で、再度ご希望の日時でご予約をお願いいたします。
                                </p>
                            </div>
                        </dl>
                    </div>
                </section>

                <!-- 申請ステータスsec -->
                <section class="user-wrapper mb-7">
                    <h2 class="user-section_title text-center mb-5">
                        申請ステータス
                    </h2>

                    <!-- 変更申請 -->
                    <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                        <h3 class="mb-4 fw-bold">変更申請</h3>

                        <dl>
                            <?php
                            //変更申請があれば...
                            if ($request_result && $request_result['request_type'] === 0):;
                            ?>
                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">
                                        申請日時
                                    </dt>

                                    <div class="d-flex flex-row fw-bold fs-5">
                                        <dd>
                                            <?php
                                            echo h(format_date($request_result['request_date'], 4));
                                            ?>
                                        </dd>
                                    </div>
                                </div>


                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">
                                        申請内容
                                    </dt>

                                    <dd class="fw-bold fs-5">

                                        <p class="mb-0">
                                            <?php
                                            //交換相手がいれば...
                                            if (!empty($request_result['change_student_no'])):
                                            ?>
                                                日時の変更
                                            <?php else: ?>
                                                面談形式の変更
                                            <?php endif; ?>
                                        </p>

                                        <p class="fs-6 mb-0 text-secondary">
                                            予約日時:
                                            <?php echo h(format_date($request_result['date'], 4)) ?>
                                            &nbsp<?php echo h(get_slot_time_by_index($request_result['slot_index'])) ?>
                                        </p>
                                    </dd>
                                </div>

                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                                    <dd class="fw-bold fs-5">
                                        <?php
                                        echo h($request_result['request_status']);
                                        ?>
                                    </dd>
                                </div>

                                <?php
                                //棄却メッセージがあれば表示
                                if (!empty($request_result['reject_message'])):
                                ?>
                                    <div>
                                        <dt class="user-card_subtitle mb-2 fs-6">メッセージ</dt>
                                        <dd class="fw-bold fs-5 text-break">
                                            <?php
                                            echo h($request_result['reject_message']);
                                            ?>
                                        </dd>
                                    </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="text-secondary text-center fs-5">
                                    変更申請はありません。
                                </p>
                            <?php endif; ?>
                        </dl>
                    </div>

                    <!-- 取消申請 -->
                    <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                        <h3 class="mb-4 fw-bold">取消申請</h3>
                        <dl>
                            <?php
                            //取消申請があれば...
                            if ($request_result && $request_result['request_type'] === 1):
                            ?>
                                <div class="mb-2">
                                    <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                                    <div class="d-flex flex-row gap-4 fw-bold fs-5">
                                        <dd>
                                            <?php
                                            echo h(format_date($request_result['request_date'], 4));
                                            ?>
                                        </dd>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <dt class="user-card_subtitle mb-2 fs-6">
                                        申請内容
                                    </dt>

                                    <dd class="fw-bold fs-5">

                                        <p class="mb-0">
                                            キャリコン＋の取消申請
                                        </p>

                                        <p class="fs-6 mb-0 text-secondary">
                                            予約日時:
                                            <?php echo h(format_date($request_result['date'], 4)) ?>
                                            &nbsp<?php echo h(get_slot_time_by_index($request_result['slot_index'])) ?>
                                        </p>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                                    <dd class="fw-bold fs-5">
                                        <?php
                                        echo h($request_result['request_status']);
                                        ?>
                                    </dd>
                                </div>

                            <?php else: ?>
                                <p class="text-secondary text-center fs-5">
                                    取消申請はありません。
                                </p>
                            <?php endif; ?>
                        </dl>
                    </div>
                </section>
            </div>

            <!-- カレンダーsec -->
            <div class="col-md-6">
                <h2 class="user-section_title mb-5 text-center">
                    カレンダーから予約
                </h2>
                <?php
                include('calendar.php')
                ?>
            </div>
        </div>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>

    <script src="./js/logout.js"></script>
</body>

</html>