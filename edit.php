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
    carcon_reservation_details.id as id,
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

    // 同じクラスの人が設定している必須キャリコンのデータを取得
    $sql = 'SELECT 
                carcon_reservation_details.id AS detail_id,
                CONCAT(m_students.last_name , " " , m_students.first_name) AS student_name,
                carcon_lines.date AS date,
                carcon_reservation_details.slot_index AS slot_index
            FROM carcon_reservation_details
            INNER JOIN carcon_reservations ON carcon_reservations.carcon_reservation_detail_id = carcon_reservation_details.id
            INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
            INNER JOIN m_students ON m_students.id = carcon_reservation_details.student_id
            WHERE m_students.course_id = :course_id AND m_students.id != :student_id
            ';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $student_result["course_id"], PDO::PARAM_INT);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $c_data_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <?php if (!empty($_SESSION['change'])): ?>
            <div class="alert alert-success user-wrapper mb-4 text-center" style="max-width: 250px;">
                変更申請を送信しました！
            </div>
            <?php unset($_SESSION['change']); ?>

        <?php elseif (!empty($_SESSION['delete'])): ?>
            <div class="alert alert-success user-wrapper mb-4 text-center" style="max-width: 250px;">
                取消申請を送信しました！
            </div>
            <?php unset($_SESSION['delete']); ?>
        <?php endif; ?>

        <div class="user-wrapper mb-7">
            <h2 class="user-section_title mb-5 text-center">
                変更・取消申請
            </h2>

            <div class="alert alert-danger bg-danger-subtle border-0 rounded-4 p-4 mb-6 shadow-sm m-auto" style="max-width: 400px;">
                <div class="d-flex flex-column align-items-center gap-3 ">
                    <div class="d-flex flex-row align-items-center gap-2 fs-5">
                        <span class="material-symbols-outlined text-danger ">
                            info
                        </span>
                        <p class="fw-bold mb-1 text-danger">
                            申請に関する注意事項
                        </p>
                    </div>

                    <ul class="mb-0 small text-secondary-emphasis">
                        <li>申請は事務局の承認をもって確定となります。</li>
                        <li>
                            ご希望に沿えない場合もございますので、<br>
                            あらかじめご了承ください。
                        </li>
                    </ul>
                </div>
            </div>

            <h3 class="text-center mb-4">予約一覧</h3>
            <?php
            //求職者支援訓練だったら...
            if ($student_result['course_type'] === 2):
                $latest_reservation = $reservation_result[0];
            ?>
                <!-- 必須キャリコン -->
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4 m-auto" style="max-width: 500px;">
                    <h3 class="titele-carcon mb-4 fw-bold">キャリコン（必須面談）</h3>
                    <dl>
                        <div class="mb-3">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="fw-bold fs-5">
                                <dd>
                                    <?php
                                    echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                    ?>
                                </dd>
                            </div>
                        </div>

                        <div class="mb-4">
                            <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                            <dd class="fw-bold fs-5">
                                <?php
                                echo h($latest_reservation['meeting_type_name']);
                                ?>
                            </dd>
                        </div>

                        <div class="d-flex flex-row gap-2">
                            <button type="button" class="btn btn-primary py-2 flex-fill w-100 d-block"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_change_type"
                                data-id="<?php echo h($latest_reservation['id']); ?>"
                                data-current-type="<?php echo h($latest_reservation['meeting_type_id']); ?>"
                                data-date="<?php echo h(format_date($latest_reservation['date'], 4)); ?>"
                                data-slot-text="<?php echo h(get_slot_time_by_index($latest_reservation['slot_index'])); ?>">
                                面談形式の変更
                            </button>

                            <button class="btn btn-success py-2 flex-fill w-100 d-block"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_change_detail"
                                data-id="<?php echo h($latest_reservation['id']); ?>"
                                data-date="<?php echo h(format_date($latest_reservation['date'], 4)); ?>"
                                data-slot-text="<?php echo h(get_slot_time_by_index($latest_reservation['slot_index']));  ?>"
                                data-class-data-list="<?php echo h(json_encode($c_data_list)); ?>">
                                日時変更
                            </button>
                        </div>
                    </dl>
                </div>

                <!-- キャリコンプラスの予約があれば表示 -->
                <?php
                if ($reservation_result[0]['is_plus_carcon'] === 1):
                    $latest_reservation = $reservation_result[0];
                ?>
                    <div class="user-card px-4 py-4 shadow rounded-4 m-auto" style="max-width: 500px;">
                        <h3 class="mb-4 fw-bold">
                            キャリコン＋（任意面談）
                        </h3>

                        <dl>
                            <div class="mb-3">
                                <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                <div class="fw-bold fs-5">
                                    <dd>
                                        <?php
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    </dd>
                                </div>
                            </div>

                            <div class="mb-4">
                                <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                <dd class="fw-bold fs-5">
                                    <?php
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                </dd>
                            </div>

                            <div class="d-flex flex-row gap-2">
                                <button type="button" class="btn btn-primary py-2 flex-fill w-100 d-block"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal_change_type"
                                    data-id="<?php echo h($latest_reservation['id']); ?>"
                                    data-current-type="<?php echo h($latest_reservation['meeting_type_id']); ?>"
                                    data-date="<?php echo h(format_date($latest_reservation['date'], 4)); ?>"
                                    data-slot-text="<?php echo h(get_slot_time_by_index($latest_reservation['slot_index'])); ?>">
                                    面談形式の変更
                                </button>

                                <form action="./delete.php" method="post" class="flex-fill w-100">
                                    <input type="hidden" name="id">
                                    <button type="submit" class="w-100 d-block btn btn-danger py-2">
                                        予約の取消
                                    </button>
                                </form>
                            </div>
                        </dl>
                    </div>
                <?php endif; ?>

            <?php
            //公共職業訓練だったら...    
            else:
            ?>
                <!-- キャリコンプラスの予約があれば... -->
                <?php
                if ($reservation_result[0]['is_plus_carcon'] === 1):
                    $latest_reservation = $reservation_result[0]; ?>

                    <div class="user-card  px-4 py-4 shadow rounded-4"
                        style="max-width: 500px;">
                        <h3 class="mb-4 fw-bold">キャリコン＋（任意面談）</h3>

                        <dl>
                            <div class="mb-3">
                                <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                                <div class="fw-bold fs-5">
                                    <dd>
                                        <?php
                                        echo h(format_date($latest_reservation['date'], 4))  . "&nbsp;" .  h(get_slot_time_by_index($latest_reservation['slot_index']));
                                        ?>
                                    </dd>
                                </div>
                            </div>

                            <div>
                                <dt class="user-card_subtitle mb-2 fs-6">面談形式</dt>
                                <dd class="fw-bold fs-5">
                                    <?php
                                    echo h($latest_reservation['meeting_type_name']);
                                    ?>
                                </dd>
                            </div>

                            <div class="d-flex flex-row gap-2">
                                <button type="button" class="btn btn-primary py-2 flex-fill w-100 d-block"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal_change_type"
                                    data-id="<?php echo h($latest_reservation['id']); ?>"
                                    data-current-type="<?php echo h($latest_reservation['meeting_type_id']); ?>"
                                    data-date="<?php echo h(format_date($latest_reservation['date'], 4)); ?>"
                                    data-slot-text="<?php echo h(get_slot_time_by_index($latest_reservation['slot_index'])); ?>">
                                    面談形式の変更
                                </button>

                                <form action="./delete.php" method="post">
                                    <input type="hidden" name="id" value="<?php echo h($latest_reservation['id']); ?>">
                                    <button type="submit" class="btn btn-danger py-2 flex-fill w-100 d-block">
                                        予約の取消
                                    </button>
                                </form>
                            </div>
                        </dl>
                    </div>

                    <!-- キャリコンプラスの予約がなければ... -->
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center">
                        <p class="text-secondary fs-5 mb-4">
                            キャリコンプラスの予約はありません。
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php include_once("./modal_change_type.php"); ?>
        <?php include_once("./modal_change_detail.php"); ?>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>