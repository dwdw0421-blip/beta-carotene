<?php

safe_session_start();

//コース情報を取得
$sql = "SELECT m_courses.id as course_id,m_courses.name as courses_name, m_courses.is_deleted as m_courses_is_deleted,start_date,m_classrooms.name as classroom_name FROM m_courses INNER JOIN m_classrooms ON m_courses.classroom_id = m_classrooms.id  ORDER BY start_date ASC";


$stmt = $db->prepare($sql);
$stmt->execute();
$course_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<aside class="admin-sidebar-wrapper bg-light p-2">
    <div class="p-4 border border-4">
        <p class="fs-6">ログイン中のユーザー：<?php echo isset($_SESSION['login_admin_id']) ? $_SESSION['staff_name'] : "未ログイン"; ?></p>
        <a href="./logout.php" class="btn btn-secondary btn-sm fs-6 d-inline">ログアウトする</a>
    </div>
    <nav class="navbar navbar-light bg-light">
        <ul>
            <li class="navbar-brand"><a href="staff.php">管理者一覧</a></li>
            <li class="navbar-brand"><a href="carcon_staff.php">キャリコン担当者一覧</a></li>
            <li class="navbar-brand"><a href="index.php">申請内容一覧</a></li>
            <li class="navbar-brand"><a href="schedule.php">面談日程表一覧</a></li>
        </ul>
        <div id="course" class="card pb-2  m-auto">
            <p class="card-header">コース管理</p>
            <ul class="card-body list-group list-group-flush mb-2">
                <?php
                foreach ($course_list as $course):
                ?>
                    <?php if ($course['m_courses_is_deleted'] == 0): ?>
                        <li class="list-group-item"><a href="student.php?course_id=<?php echo h($course['course_id']) ?>"><?php echo h($course['classroom_name']) ?>(<?php echo h(format_date($course['start_date'], 2)) ?>開講)</a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <a href="course_add.php" class="btn btn-primary m-auto">コースを追加</a>
        </div>

    </nav>

</aside>