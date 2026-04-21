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
    $sql = 'SELECT 
    m_students.*,
    m_courses.course_type as course_type
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
    ORDER BY carcon_lines.date';
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
    m_request_statuses.name as request_status
    FROM carcon_request_reservations 
    INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id = carcon_reservation_details.id
    INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
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
    <title>変更・取消申請</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5">
        <section class="user-wrapper mb-7">
            <h1 class="user-section_title mb-5 text-center">
                変更・取消申請
            </h1>
            <div class="d-flex flex-column align-items-center gap-2 text-danger mb-5">
                <span class="material-symbols-outlined">
                    warning
                </span>
                <p class="mb-0">
                    変更・取消申請は、事務局の承認をもって確定となります。ご希望に沿えない場合もございますので、あらかじめご了承ください。
                </p>
            </div>

            <h2 class="text-center mb-4">予約一覧</h2>
            <?php
            //求職者支援訓練だったら...
            if ($student_result['course_type'] === 2):
            ?>
                <!-- 必須キャリコン -->
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコン(必須面談)</h3>
                    <dl>
                        <div class="mb-3">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="fw-bold fs-5">
                                <dd>
                                    <?php
                                    $latest_reservation = $reservation_result[0];
                                    echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                    ?>
                                </dd>
                            </div>
                        </div>

                        <div class="mb-4">
                            <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                            <dd class="fw-bold fs-5">
                                <?php
                                $latest_reservation = $reservation_result[0];
                                echo h($latest_reservation['meeting_type_name']);
                                ?>
                            </dd>
                        </div>

                        <div class="d-flex flex-row gap-2">
                            <button class="btn btn-primary py-2 flex-fill w-100 d-block">
                                面談形式の変更
                            </button>

                            <button class="btn btn-success py-2 flex-fill w-100 d-block">
                                日時変更
                            </button>
                        </div>
                    </dl>
                </div>

                <!-- キャリコンプラス -->
                <div class="user-card  px-4 py-4 shadow rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコンプラス(任意面談)</h3>
                    <dl>
                        <?php if ($reservation_result[0]['is_plus_carcon'] === 1): ?>
                            <div class="mb-3">
                                <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                <div class="fw-bold fs-5">
                                    <dd>
                                        <?php
                                        $latest_reservation = $reservation_result[0];
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    </dd>
                                </div>
                            </div>

                            <div>
                                <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                <dd class="fw-bold fs-5">
                                    <?php
                                    $latest_reservation = $reservation_result[0];
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                </dd>
                            </div>

                        <?php else: ?>
                            <div class="d-flex flex-column align-items-center">
                                <p class="text-secondary fs-5 mb-4">
                                    キャリコンプラスの予約はありません。
                                </p>
                                <a class="btn btn-primary px-4 py-2 m-0" href="./reserve.php">予約はこちら</a>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex flex-column align-items-center gap-2 mt-5 text-danger fw-bold">
                            <span class="material-symbols-outlined">
                                warning
                            </span>
                            <p class="mb-0">
                                キャリコンプラスは仕様上、直接の日時変更ができません。<br />
                                お手数ですが、現在の予約を一度取り消した上で、再度ご希望の日時でご予約をお願いいたします。
                            </p>
                        </div>
                    </dl>
                </div>

            <?php
            //公共職業訓練だったら...    
            else:
            ?>
                <!-- キャリコンプラス -->
                <div class="user-card  px-4 py-4 shadow rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコンプラス(任意面談)</h3>
                    <dl>
                        <?php if ($reservation_result[0]['is_plus_carcon'] === 1): ?>
                            <div class="mb-3">
                                <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                <div class="fw-bold fs-5">
                                    <dd>
                                        <?php
                                        $latest_reservation = $reservation_result[0];
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    </dd>
                                </div>
                            </div>

                            <div>
                                <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                <dd class="fw-bold fs-5">
                                    <?php
                                    $latest_reservation = $reservation_result[0];
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                </dd>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column align-items-center">
                                <p class="text-secondary fs-5 mb-4">
                                    キャリコンプラスの予約はありません。
                                </p>
                                <a class="btn btn-primary px-4 py-2 m-0" href="./reserve.php">予約はこちら</a>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex flex-column align-items-center gap-2 mt-5 text-danger fw-bold">
                            <span class="material-symbols-outlined">
                                warning
                            </span>
                            <p class="mb-0">
                                キャリコンプラスは仕様上、直接の日時変更ができません。<br />
                                お手数ですが、現在の予約を一度取り消した上で、再度ご希望の日時でご予約をお願いいたします。
                            </p>
                        </div>
                    </dl>
                </div>
            <?php endif; ?>
        </section>


    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>