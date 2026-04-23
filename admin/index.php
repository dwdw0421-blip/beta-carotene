<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();
// 申請中のデータを取得
// 並び替えは 更新情報 昇順（古い更新順）
$sql = "SELECT carcon_request_reservations.id AS request_id, 
    CONCAT(m_students.last_name , ' ' , m_students.first_name) AS student_name, 
    m_courses.name AS course_name, 
    m_courses.start_date, m_request_statuses.name AS request_status_name 
    FROM carcon_request_reservations 
    INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id = carcon_reservation_details.id 
    INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id 
    INNER JOIN m_courses ON m_students.course_id = m_courses.id 
    INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
    WHERE m_request_statuses.id = 1
    ORDER BY carcon_request_reservations.updated_at ASC";
$stmt = $db->prepare($sql);
$stmt->execute();

$pending_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 対応完了済みのデータ
// 並び替えは 更新情報 昇順（新しい更新順）
$sql = "SELECT carcon_request_reservations.id AS request_id, 
    CONCAT(m_students.last_name , ' ' , m_students.first_name) AS student_name, 
    m_courses.name AS course_name, 
    m_courses.start_date, m_request_statuses.name AS request_status_name, 
    carcon_request_reservations.updated_at
    FROM carcon_request_reservations 
    INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id = carcon_reservation_details.id 
    INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id 
    INNER JOIN m_courses ON m_students.course_id = m_courses.id 
    INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
    WHERE m_request_statuses.id != 1
    ORDER BY carcon_request_reservations.updated_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute();

$done_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="../css/style.css">
    <title>申請内容一覧</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1 class="display-5 fw-bold text-center">申請内容一覧</h1>
        <div class="border p-2">
            <h2>承認待ちリスト</h2>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">学生名</th>
                        <th scope="col">コース名</th>
                        <th scope="col">ステータス</th>
                        <th scope="col">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_data as $data): ?>
                        <tr>
                            <td><?php echo h($data["student_name"]); ?></td>
                            <td><?php echo h($data["course_name"]); ?></td>
                            <td><?php echo h($data["request_status_name"]); ?></td>
                            <td><a href="request_detail.php?id=<?php echo h($data["request_id"]); ?>">詳細</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="border p-2 mt-5 mb-5">
            <h2>対応済みリスト</h2>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">学生名</th>
                        <th scope="col">コース名</th>
                        <th scope="col">ステータス</th>
                        <th scope="col">更新日</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($done_data as $data): ?>
                        <tr>
                            <td><?php echo h($data["student_name"]); ?></td>
                            <td><?php echo h($data["course_name"]); ?></td>
                            <td><?php echo h($data["request_status_name"]); ?></td>
                            <td><?php echo h(format_date($data["updated_at"], 1)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>

</html>