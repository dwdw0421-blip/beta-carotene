<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

// ステータス（3列）を取得---
$sql = 'SELECT * FROM carcon_lines';
// $stmt = $pdo->query($sql);
// $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $db->prepare($sql);
$stmt->execute();
$carcon_lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// var_dump($carcon_lines);

// タスクを全取得
// $sql_tasks = 'SELECT * FROM tasks';
// $stmt_tasks = $pdo->query($sql_tasks);
// $all_tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);


// $sql_student = 'SELECT
//     res.id AS reservation_id,
//     lin.id AS line_id,  -- carcon_linesのid
//     std.student_no,
//     std.last_name,
//     std.first_name,
//     lin.date AS line_date,
//     det.slot_index,
//     rm.name AS classroom_name
// FROM
//     carcon_reservations AS res
// JOIN
//     carcon_reservation_details AS det ON res.carcon_reservation_detail_id = det.id
// JOIN
//     carcon_lines AS lin ON res.carcon_line_id = lin.id
// JOIN
//     m_students AS std ON det.student_id = std.id
// JOIN
//     m_classrooms AS rm ON lin.classroom_id = rm.id
// ORDER BY
//     lin.date ASC,
//     lin.id ASC'; 
    // 日付順なおかつline_id順 これがないとupdateのとき毎回順番が変わる


$sql_student = 
   'SELECT
    lin.id AS line_id,
    lin.date AS line_date,
    -- ★ lin(枠)ではなく rm2(コース経由の教室名) を取得する
    rm2.name AS classroom_name, 
    res.id AS reservation_id,
    std.student_no,
    std.last_name,
    std.first_name,
    det.slot_index
FROM
    carcon_lines AS lin
JOIN
    m_classrooms AS rm ON lin.classroom_id = rm.id
LEFT JOIN
    carcon_reservations AS res ON lin.id = res.carcon_line_id
LEFT JOIN
    carcon_reservation_details AS det ON res.carcon_reservation_detail_id = det.id
LEFT JOIN
    m_students AS std ON det.student_id = std.id
-- ★ ここからが追加：生徒のコースを介して教室名を取得する
LEFT JOIN
    m_courses AS cou ON std.course_id = cou.id  -- ※std側のカラム名は適宜修正してください
LEFT JOIN
    m_classrooms AS rm2 ON cou.classroom_id = rm2.id 
ORDER BY
    lin.date ASC,
    lin.id ASC';
    // 日付順なおかつline_id順 これがないとupdateのとき毎回順番が変わる

$stmt_student = $db->prepare($sql_student);
$stmt_student->execute();
$students = $stmt_student->fetchAll(PDO::FETCH_ASSOC);


// $studentsの配列を日付順に並べ替える
// usort($students, function ($a, $b) {
//     return strtotime($a['line_date']) <=> strtotime($b['line_date']);
// });


// echo '<pre>';
// print_r($students);
// echo '</pre>';


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ドラッグ＆ドロップ 発展編 -DB連携-</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    :root {
      --drop-zone-bg: #f8f9fa;
      --drop-zone-border: #dee2e6;
      --drop-zone-hover: #e9ecef;
    }

    .drop-zone {
      width: 50%;
      height: 120px;
      vertical-align: middle;
      background-color: var(--drop-zone-bg);
      border: 2px dashed var(--drop-zone-border) !important;
      transition: all 0.2s ease;
      position: relative;
    }

    .drop-zone:hover {
      background-color: var(--drop-zone-hover);
    }

    /* ドラッグオーバー時の強調スタイル（JSで制御用） */
    .drag-over {
      background-color: #e3f2fd !important;
      border-color: #0d6efd !important;
      border-style: solid !important;
    }

    .item {
      cursor: grab;
      user-select: none;
      transition: transform 0.1s ease;
    }

    .item:active {
      cursor: grabbing;
    }

    #alert-wrapper {
      position: fixed;
      width: 100%;
      top: 10%;
      left: 50%;
      translate: -50% 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    [popover].msg-toast {
      position: fixed;
      top: auto;
      right: 20px;
      left: auto;
      bottom: 20px;
      margin: 0;
      border: none;
      min-width: 250px;
      transition: bottom 0.3s ease;

      &:popover-open {
        animation: slide-in 0.3s ease-out
      }
    }

    @keyframes slide-in {
      from {
        opacity: 0;
        translate: 0 20px;
      }

      to {
        opacity: 1;
        translate: 0 0;
      }
    }
  </style>
</head>

<body class="bg-light">
  <main class="container py-5">
    <header class="text-center mb-5">
      <h1 class="display-5 fw-bold">ドラッグ＆ドロップ 発展編<br> -DB連携-</h1>
      <p class="text-muted">ドラッグ&ドロップでタスクを入れ替える度にDBに保存する</p>
      <div id="alert-wrapper"></div>
    </header>


<!-- 俺追加↓ -->
     <div class="row mb-4 justify-content-center">
      <div class="col">
        <button type="button" class="btn btn-primary" id="open-line-btn">キャリコン予約枠追加</button>
      </div>
    </div>

    <dialog class="form p-5" id="modal-line">
      <div class="row mb-3">
        <h5 class="fw-bold">キャリコン予約枠追加</h5>
      </div>

    <div class="row mb-3">
    <div class="col-12 mb-3">
      <label for="line-date" class="form-label">追加する日付</label>
      <input type="date" id="line-date" class="form-control" value="<?= date('Y-m-d') ?>">
    </div>

      <div class="d-flex gap-3">
        <button class="btn btn-secondary flex-fill" type="button" id="cancel-line-btn">キャンセル</button>
        <button class="btn btn-primary flex-fill" type="button" id="add-line-btn">追加</button>
      </div>
    </dialog>
<!-- 俺追加↑ -->


    <div class="row mb-4 justify-content-center">
      <div class="col">
        <button type="button" class="btn btn-primary" id="open-btn">Add Task</button>
      </div>
    </div>
    <dialog class="form p-5" id="modal">
      <div class="row mb-3">
        <div class="col mb-3">
          <label for="task-title" class="form-label">タスク名</label>
          <input type="text" name="task-title" id="task-title" class="form-control">
        </div>
      </div>
      <div class="d-flex gap-3">
        <button class="btn btn-secondary flex-fill" type="button" id="cancel-btn">Cancel</button>
        <button class="btn btn-primary flex-fill" type="button" id="add-btn">Add</button>
      </div>
    </dialog>



<!-- Bootstrapのグリッドを使用 -->
<div class="row row-cols-1 row-cols-md-5 g-3 mt-4 w-100 px-3">


<!-- 日付の区切りにライン -->
<?php
$prev_date = null;      // 直前の日付を保存
?>


<?php
$grouped_students = [];
foreach ($students as $s) {
    // line_id をキーにして、その中にデータを詰め直す
    $grouped_students[$s['line_id']][] = $s;
    }

//     echo '<pre>';
// print_r($grouped_students);
// echo '</pre>';

?>
  
  
  <?php foreach ($grouped_students as $line_id => $tasks_in_line):?>

  <?php 
    $student = $tasks_in_line[0]; // 代表データ

    $current_date = $student['line_date'];

    //  日付の区切りライン
    if ($prev_date !== $current_date): ?>
        <!-- 強制的に100%幅を持たせて改行させる -->
        <div class="col-12" style="flex: 0 0 100%; max-width: 100%; width: 100%;">
            <div class="d-flex align-items-center mt-4 mb-2">
                <hr class="flex-grow-1 border-secondary border-2 opacity-50">
                <span class="mx-3 fw-bold text-secondary" style="white-space: nowrap; font-size: 0.8rem;">
                    <?= htmlspecialchars($current_date) ?>
                </span>
                <hr class="flex-grow-1 border-secondary border-2 opacity-50">
            </div>
        </div>
    <?php endif; ?>

  
  <?php
//       echo '<pre>';
// print_r($student);
// echo '</pre>';
  ?>

    <div class="col">
      <div class="status-column border border-2 border-secondary-subtle rounded-3 p-1 bg-light shadow-sm <?= $bg_color_class ?>">
        
      <div class="d-flex justify-content-between align-items-start mb-3">
        <h2 class="h6 fw-bold text-center border-bottom pb-2 mb-2">
           <?= 'ID : ' . htmlspecialchars($line_id . ' / ' . $current_date) ?>
          </h2>

          <!--  俺追加　削除用ボタン -->
            <button class="btn-close delete-status-btn"
                data-id="<?php echo $student['line_id']; ?>"
                style="font-size: 1.0rem;"></button>
      </div>

        <div class="task-slots">
          <?php 
          $hours = ['10:00～', '11:00～', '12:00～', '14:00～', '15:00～', '16:00～'];
          
           for ($i = 0; $i < 6; $i++):
            $task = null;

          foreach ($tasks_in_line as $t) { 
            if (isset($t['slot_index']) && (int)$t['slot_index'] === $i) { 
              $task = $t; 
              break;
              } 
              } 

// echo '<pre>';
// print_r($task);
// echo '</pre>';

          ?>
            <div class="d-flex align-items-center mb-1">
              <!-- 左側：時間表示 -->
              <small class="text-secondary fw-bold pe-2" style="width: 45px; text-align: right; font-size: 0.65rem;">
                <?= $hours[$i] ?>
              </small>

              <!-- 右側：スロット -->
               <!-- data-status-id=には空スロットの場合を考え"$line_id"を入れる -->
              <div class="drop-zone border border-dashed rounded flex-grow-1 d-flex align-items-center justify-content-center" 
                   style="height: 30px; background: #fff; border-color: #ddd; overflow: hidden;"
                   data-status-id="<?= htmlspecialchars($line_id) ?>" 
                   data-slot-index="<?= $i ?>"
                   ondragover="event.preventDefault()" 
                   ondrop="handleDrop(event)">
                
                   <!-- data-task-id=には 誰を動かしたかわかるため reservation_idをいれる-->
                <?php if ($task): ?>
                  <div class="card bg-warning w-100 h-100 task-item border-0 shadow-none d-flex align-items-center justify-content-center" 
                       id="task-<?= htmlspecialchars($task['reservation_id']) ?>" 
                       data-task-id="<?= htmlspecialchars($task['reservation_id']) ?>" 

                       
                       draggable="true" 
                       ondragstart="handleDragStart(event)"
                       style="cursor: move; font-size: 0.75rem; font-weight: bold;">
                    <?= htmlspecialchars($task['classroom_name'] . ' ' . $task['student_no'] .' '. $task['last_name'] . $task['first_name']); ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted small" style="font-size: 0.65rem;">【 空き 】</span>
                <?php endif; ?>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  <?php 
$prev_date = $current_date; // 今回の日付を保存
endforeach; ?>
</div>












<div class="delete-area mt-5 p-5 text-center border border-2 border-danger-subtle">
      Drop here to Delete
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="../js/script_schedule.js"></script>
</body>

</html>