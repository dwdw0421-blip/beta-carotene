<?php

require_once __DIR__ . '/../includes/functions.php';

$id = (isset($_GET["id"]) ? (int)$_GET["id"] : "");

if (empty($id)) {
    header("location: index.php");
    exit();
}

try {
    // TODO: nagata-t リクエスト側のデータと変更側のデータを別々で取っている
    // 実装優先で簡単な方に倒したが効率よいやり方ありそうなのであとで探す

    $db = db_connect();

    $sql = "SELECT change_carcon_reservation_detail_id,request_status_id FROM carcon_request_reservations WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $request_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 存在しない変更申請の結果だったら戻す
    if (empty($request_data)) {
        header("location: index.php");
        exit();
    }

    // 変更側の詳細データがない＝リクエスト側だけのデータ（自分のデータ変更は「形式」のみ）
    $is_change_data = !is_null($request_data["change_carcon_reservation_detail_id"]);

    $request_result = array();
    $change_result = array();
    $students_data = array();

    // リクエスト側のデータ取得
    $get_request_data_sql = "SELECT 
        carcon_reservation_details.id AS detail_id,
        carcon_reservation_details.slot_index AS detail_slot_index,
        carcon_lines.id AS line_id,
        carcon_lines.date AS line_date,
        current_meeting_type.name AS current_meeting_type,
        next_meeting_type.name AS next_meeting_type,
        m_classrooms.name AS classroom_name,
        m_students.id AS student_id,
        carcon_reservation_details.created_at AS detail_send_date
            FROM carcon_request_reservations
            INNER JOIN carcon_reservation_details ON carcon_request_reservations.request_carcon_reservation_detail_id =     carcon_reservation_details.id
            LEFT JOIN m_meeting_types AS next_meeting_type ON carcon_request_reservations.request_meeting_type =    next_meeting_type.id
	    	INNER JOIN m_meeting_types AS current_meeting_type ON carcon_reservation_details.meeting_type =     current_meeting_type.id
            INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
            INNER JOIN carcon_reservations ON carcon_reservations.carcon_reservation_detail_id = carcon_reservation_details.id
            INNER JOIN carcon_lines ON carcon_reservations.carcon_line_id = carcon_lines.id
            INNER JOIN m_classrooms ON carcon_lines.classroom_id = m_classrooms.id
            INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id
            WHERE carcon_request_reservations.id=:id;
        ";
    $stmt = $db->prepare($get_request_data_sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $request_result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($is_change_data) {
        // 変更側のデータ取得
        $get_change_data_sql = "SELECT 
            carcon_reservation_details.id AS detail_id,
            carcon_reservation_details.slot_index AS detail_slot_index,
            carcon_lines.id AS line_id,
            carcon_lines.date AS line_date,
            current_meeting_type.name AS current_meeting_type,
            next_meeting_type.name AS next_meeting_type,
            m_classrooms.name AS classroom_name,
            m_students.id AS student_id,
            carcon_reservation_details.created_at AS detail_send_date
                FROM carcon_request_reservations
                LEFT JOIN carcon_reservation_details ON carcon_request_reservations.change_carcon_reservation_detail_id =       carcon_reservation_details.id
                LEFT JOIN m_meeting_types AS next_meeting_type ON carcon_request_reservations.change_meeting_type =         next_meeting_type.id
	        	INNER JOIN m_meeting_types AS current_meeting_type ON carcon_reservation_details.meeting_type =         current_meeting_type.id
                INNER JOIN m_request_statuses ON carcon_request_reservations.request_status_id = m_request_statuses.id
                INNER JOIN carcon_reservations ON carcon_reservations.carcon_reservation_detail_id = carcon_reservation_details.    id
                INNER JOIN carcon_lines ON carcon_reservations.carcon_line_id = carcon_lines.id
                INNER JOIN m_classrooms ON carcon_lines.classroom_id = m_classrooms.id
                INNER JOIN m_students ON carcon_reservation_details.student_id = m_students.id
                WHERE carcon_request_reservations.id=:id;
            ";

        $stmt = $db->prepare($get_change_data_sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $change_result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 申請側の学生データ取得
    $requestStudentId = $request_result["student_id"] ?? null;
    $changeStudentId = $change_result["student_id"] ?? null;

    $get_student_sql = "SELECT 
                            CONCAT(last_name, first_name) AS student_name,
                            m_classrooms.name AS classroom_name,
                            m_courses.start_date AS course_start_date
                        FROM m_students 
                        INNER JOIN m_courses ON m_courses.id = m_students.course_id
                        INNER JOIN m_classrooms ON m_classrooms.id = m_courses.classroom_id
                        WHERE m_students.id = :r_student_id
                           OR m_students.id = :c_student_id";

    $stmt = $db->prepare($get_student_sql);
    $stmt->bindParam(":r_student_id", $requestStudentId, $requestStudentId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(":c_student_id", $changeStudentId, $changeStudentId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->execute();

    $students_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $r_student = $students_data[0];
    if ($is_change_data) {
        $c_student = $students_data[1];
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}
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
    <title>申請内容詳細</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>申請内容詳細</h1>
        <p>申請者: <?php echo $r_student["student_name"]; ?>（<?php echo $r_student["classroom_name"]; ?>｜<?php echo h(format_date($r_student['course_start_date'], 2)) ?>開講）</p>
        <p>申請日時: <?php echo format_date($request_result["detail_send_date"], 1); ?></p>

        <div class="card">
            <p><?php echo $r_student["student_name"]; ?>（<?php echo $r_student["classroom_name"]; ?>｜<?php echo h(format_date($r_student['course_start_date'], 2)) ?>開講）さんの変更内容</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">変更前の予約内容</p>
                            <dl>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>日程</dt>
                                    <dd><?php echo format_date($request_result["line_date"], 3); ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>ラインID</dt>
                                    <dd><?php echo $request_result["line_id"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>時間</dt>
                                    <dd><?php echo $request_result["detail_slot_index"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>形式</dt>
                                    <dd><?php echo $request_result["current_meeting_type"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>教室</dt>
                                    <dd><?php echo $request_result["classroom_name"]; ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">変更後の予約内容</p>
                            <dl>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>日程</dt>
                                    <dd><?php echo !$is_change_data ?
                                            format_date($request_result["line_date"], 3) :
                                            format_date($change_result["line_date"], 3); ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>ラインID</dt>
                                    <dd><?php echo !$is_change_data ?
                                            $request_result["line_id"] :
                                            $change_result["line_id"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>時間</dt>
                                    <dd><?php echo !$is_change_data ?
                                            $request_result["detail_slot_index"] :
                                            $change_result["detail_slot_index"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>形式</dt>
                                    <dd><?php echo $request_result["next_meeting_type"] ?? $request_result["current_meeting_type"];  ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>教室</dt>
                                    <dd><?php echo !$is_change_data ?
                                            $request_result["classroom_name"] :
                                            $change_result["classroom_name"]; ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($is_change_data): ?>
            <div class="card">
                <p><?php echo $c_student["student_name"]; ?>（<?php echo $c_student["classroom_name"]; ?>｜<?php echo h(format_date($c_student['course_start_date'], 2)) ?>開講）さんの変更内容</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <p>変更前の予約内容</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>日程</dt>
                                    <dd><?php echo format_date($change_result["line_date"], 3); ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>ラインID</dt>
                                    <dd><?php echo $change_result["line_id"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>時間</dt>
                                    <dd><?php echo $change_result["detail_slot_index"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>形式</dt>
                                    <dd><?php echo $change_result["current_meeting_type"]; ?></dd>
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <dt>教室</dt>
                                    <dd><?php echo $change_result["classroom_name"]; ?></dd>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <p>変更後の予約内容</p>
                                <dl>
                                    <div class="d-flex justify-content-center gap-3">
                                        <dt>日程</dt>
                                        <dd><?php echo format_date($request_result["line_date"], 3); ?></dd>
                                    </div>
                                    <div class="d-flex justify-content-center gap-3">
                                        <dt>ラインID</dt>
                                        <dd><?php echo $request_result["line_id"]; ?></dd>
                                    </div>
                                    <div class="d-flex justify-content-center gap-3">
                                        <dt>時間</dt>
                                        <dd><?php echo $request_result["detail_slot_index"]; ?></dd>
                                    </div>
                                    <div class="d-flex justify-content-center gap-3">
                                        <dt>形式</dt>
                                        <dd><?php echo $change_result["next_meeting_type"] ?? $change_result["current_meeting_type"]; ?></dd>
                                    </div>
                                    <div class="d-flex justify-content-center gap-3">
                                        <dt>教室</dt>
                                        <dd><?php echo $request_result["classroom_name"]; ?></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($request_data["request_status_id"] === RequestStatus::Pending->value): ?>
            <form action="request_approval_do.php" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" name="action" value="approval">承認</button>
            </form>
            <form action="request_reject_do.php" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" name="action" value="reject">棄却</button>
                <label for="reject_message">
                    <p>棄却する場合は下記に理由を入力してください。<span>※学生への通知メッセージに表示されます。</span></p>
                </label>
                <textarea name="reject_message" id="reject_message" placeholder="4月11日15:00の枠はZOOMのみの対応となりますので、佐藤さんにその旨お伝えして再度ご相談ください。また、その上で日時交換希望される際は改めて申請をお願いします。"></textarea>
            </form>
        <?php else: ?>
            <p><?php echo $request_data["request_status_id"] === RequestStatus::Approve->value ?
                    "承認済み" :
                    "棄却済み"; ?></p>
        <?php endif; ?>
    </section>
</body>

</html>