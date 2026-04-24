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
$message = $_SESSION['res_message'] ?? '';
unset($_SESSION['res_message']);
$type = ['danger', 'primary'];


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
    ORDER BY carcon_lines.date ASC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reservation_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //学生の予約件数を取得(必須キャリコンのみ)
    $sql = 'SELECT COUNT(carcon_reservation_details.id) FROM carcon_reservation_details INNER JOIN carcon_reservations ON  carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id WHERE student_id = :student_id AND carcon_reservation_details.is_plus_carcon = 0 AND carcon_lines.date < NOW();';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reserve_required_count = $stmt->fetch(PDO::FETCH_COLUMN);



    //学生の予約件数を取得(キャリコン＋のみ)
    $sql = 'SELECT COUNT(carcon_reservation_details.id) FROM carcon_reservation_details INNER JOIN carcon_reservations ON  carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id WHERE student_id = :student_id AND carcon_reservation_details.is_plus_carcon = 1 AND carcon_lines.date < NOW();';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $reserve_count = $stmt->fetch(PDO::FETCH_COLUMN);


    //学生の変更・キャンセル申請情報を取得
    $sql = 'SELECT 
    carcon_request_reservations.request_carcon_reservation_detail_id as request_id,
    carcon_request_reservations.request_status_id as request_status_id,
    carcon_request_reservations.reject_message as reject_message,
    carcon_request_reservations.created_at as request_date,
    carcon_request_reservations.request_type as request_type,
    carcon_request_reservations.request_meeting_type as meeting_type,
    m_request_statuses.name as request_status,
    m_request_statuses.name AS request_status_name, 
    carcon_request_reservations.updated_at as updated_at,
    carcon_reservation_details.slot_index as slot_index,
    carcon_lines.date as date
    FROM carcon_request_reservations 
    INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id = carcon_reservation_details.id
    INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
    INNER JOIN carcon_reservations ON carcon_reservations.carcon_reservation_detail_id = carcon_request_reservations.request_carcon_reservation_detail_id
    INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
    WHERE carcon_reservation_details.student_id = :student_id
    ORDER BY carcon_request_reservations.created_at DESC
   ';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $request_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>申請・面談履歴</title>
</head>

<body>
    <?php
    include('header.php');
    ?>
    <main>
        <section class="wrapper">
            <h2 class="user-section_title mb-5 text-center">申請・面談履歴</h2>
            <ul class="nav  d-flex justify-content-center mb-5">
                <li class="nav-item btn btn-outline-dark me-2"><a href="#request" class="nav-link">申請履歴</a></li>
                <li class="nav-item btn btn-outline-dark"><a href="#reserve_log" class="nav-link">面談履歴</a></li>
            </ul>
            <h3 class="user-card_subtitle mb-2 fs-6 text-center" id="request">申請履歴</h3>
            <?php if (empty($request_result)): ?>
                <p>申請履歴はありません</p>
            <?php else: ?>
                <?php foreach ($request_result as $request): ?>
                    <div class="reserve-card user-card px-4 py-4 shadow mb-4 rounded-4">
                        <h4 class="mb-4 fw-bold">
                            <?php echo ($request['request_type'] == 0 ? "変更申請" : "キャンセル申請") ?>
                        </h4>
                        <dl>
                            <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                            <dd><?php echo h(format_date($request['request_date'], 1)) ?></dd>
                            <?php $type = get_meeting_type_list(); ?>
                            <?php if ($request['request_status'] == "申請中"): ?>
                                <dt class="user-card_subtitle mb-2 fs-6">申請中の予約内容（変更前の日時｜形式）</dt>
                                <dd><?php echo h(format_date($request['date'], 4)) ?>&nbsp<?php echo h(get_slot_time_by_index($request['slot_index'])) ?>｜<?php echo h($type[$request['meeting_type']]) ?></dd>
                                <dd><?php ?></dd>
                            <?php endif; ?>
                            <dt class="user-card_subtitle mb-2 fs-6">事務局からの回答日時</dt>
                            <dd>
                                <?php if ($request['updated_at'] !== $request['request_date']): ?>
                                    <?php echo h(format_date($request['updated_at'], 1)) ?>
                                <?php else: ?>
                                    順次対応中です。しばらくお待ちください。
                                <?php endif; ?>
                            </dd>
                            <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                            <dd><?php echo h($request['request_status_name']) ?></dd>
                            <?php if (!empty($request['reject_message'])): ?>
                                <dt class="user-card_subtitle mb-2 fs-6">メッセージ</dt>
                                <dd><?php echo h($request['reject_message']) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>
        <section class="wrapper mt-5">
            <h3 class="user-card_subtitle mb-2 fs-6 text-center" id="reserve_log">面談履歴</h3>
            <div class="text-secondary fs-6 text-center">


                <?php if ($student_result['course_type'] = 2): ?>
                    <p> 今までに実施したキャリコン（必須）:
                        <?php echo h($reserve_required_count) ?></p>
                    <p>今までに実施したキャリコン＋（任意）：
                        <?php echo h($reserve_count) ?> </p>
                <?php elseif ($student_result['course_type'] = 1): ?>
                    <p> 今までに実施したキャリコン＋（任意）：
                        <?php echo h($reserve_count) ?>
                    </p>
                <?php endif; ?>

            </div>
            <ul class="history-ul">
                <?php foreach ($reservation_result as $reserve): ?>
                    <li class="reserve-card reserve-card--history d-flex flex-row gap-1">
                        <!-- is_plus_carconが0なら必須、1なら＋（任意） -->
                        <?php if ($reserve['is_plus_carcon'] == 0): ?>
                            <p class="category category--required">キャリコン(必須)</p>
                        <?php elseif ($reserve['is_plus_carcon'] == 1): ?>
                            <p class="category">キャリコン＋(任意)</p>
                        <?php endif; ?>
                        <p class="history-item"><?php echo h(format_date($reserve['date'], 4)) ?>&nbsp<?php echo h(get_slot_time_by_index($reserve['slot_index'])) ?></p>

                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>