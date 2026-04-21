<?php
// session_start();
require_once __DIR__ . '/./includes/functions.php';

//ログインしていない場合はログイン画面へ
// if (!isset($_SESSION['id'])) {
//     header('location:index.php');
//     exit();
// }

$db = db_connect();
// $login_id = $_SESSION['id'];

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
    <title>申請・面談履歴</title>
</head>

<body>
    <?php
    include('header.php');
    ?>
    <main>
        <section class="wrapper">
            <h1>申請・申請履歴</h1>

            <h2>申請履歴</h2>
            <div class="reserve-card">
                <h3>変更申請</h3>
                <dl>
                    <dt>申請日時</dt>
                    <dd>TODO：日付を表示</dd>
                    <dt>事務局からの回答日時</dt>
                    <dd>TODO：日付を表示</dd>
                    <dt>ステータス</dt>
                    <dd>TODO：ステータスを表示</dd>
                    <dt>メッセージ</dt>
                    <dd>TODO：メッセージを表示</dd>
                </dl>
            </div>
            <div class="reserve-card">
                <h3>キャンセル申請</h3>
                <dl>
                    <dt>申請日時</dt>
                    <dd>TODO：日付を表示</dd>
                    <dt>事務局からの回答日時</dt>
                    <dd>TODO：日付を表示</dd>
                    <dt>ステータス</dt>
                    <dd>TODO：ステータスを表示</dd>
                    <!-- <dt>メッセージ</dt>
                    <dd>TODO：メッセージを表示</dd> -->
                </dl>
            </div>
        </section>
        <section class="wrapper">
            <h2>申請履歴</h2>
            <p>（今までに行った面談:TODO-DBから表示）</p>
            <p>（今までに行った必須キャリコン：:TODO-DBから表示）</p>
            <ul>
                <li class="reserve-card">
                    <p>必須/キャリコン＋</p>TODO-DBから表示
                </li>
            </ul>
        </section>
    </main>

    <!-- ボトムバー -->
    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>