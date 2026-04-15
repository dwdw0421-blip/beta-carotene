<?php
//コース情報を取得
$sql = "SELECT start_date,m_classrooms.name as classroom_name FROM m_courses INNER JOIN m_classrooms ON m_courses.id = m_classrooms.course_id  ORDER BY start_date ASC";


$stmt = $db->prepare($sql);
$stmt->execute();
$course_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<aside class="admin-sidebar-wrapper">
    <p>ログイン中のユーザー：--TODO:ここにセッションに保存されている名前を表示--</p>
    <nav>
        <ul>
            <li><a href="staff.php">管理者一覧</a></li>
            <li><a href="index.php">申請内容一覧</a></li>
            <li><a href="schedule.php">面談日程表一覧</a></li>
        </ul>
        <div id="course">
            <p>コース管理</p>
            <ul>
                <!-- TODO:コースはDBからforeach表示する -->
                <?php
                foreach ($course_list as $course):
                ?>
                    <li><?php echo h($course['classroom_name']) ?>(<?php echo h(format_date($course['start_date'], 2)) ?>開講)</li>
                    <!-- <li>6A（2025年11月開講）</li> -->

                <?php endforeach; ?>
            </ul>
            <a href="course_add.php">コースを追加</a>
        </div>

    </nav>

</aside>