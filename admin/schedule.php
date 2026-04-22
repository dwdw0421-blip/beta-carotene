<?php

require_once __DIR__ . '/../includes/functions.php';

$db = db_connect();

$sql_student =
  'SELECT
    lin.id AS line_id,
    lin.date AS line_date,

    lin.classroom_id,      -- 教室プルダウン用
    lin.carcon_staff_id,   -- 講師プルダウン用

    -- rm(枠の教室)がNULLでも、rm2(コース経由)があれば表示
    rm2.name AS classroom_name, 
    res.id AS reservation_id,
    std.student_no,
    std.last_name,
    std.first_name,
    det.slot_index,
    is_plus_carcon
FROM
    carcon_lines AS lin
LEFT JOIN
    m_classrooms AS rm ON lin.classroom_id = rm.id
LEFT JOIN
    carcon_reservations AS res ON lin.id = res.carcon_line_id
LEFT JOIN
    carcon_reservation_details AS det ON res.carcon_reservation_detail_id = det.id
LEFT JOIN
    m_students AS std ON det.student_id = std.id
LEFT JOIN
    m_courses AS cou ON std.course_id = cou.id
LEFT JOIN
    m_classrooms AS rm2 ON cou.classroom_id = rm2.id 
ORDER BY
    lin.date ASC,
    lin.id ASC';
// 日付順なおかつline_id順 これがないとupdateのとき毎回順番が変わる

$stmt_student = $db->prepare($sql_student);
$stmt_student->execute();
$students = $stmt_student->fetchAll(PDO::FETCH_ASSOC);


// echo '<pre>';
// print_r($students);
// echo '</pre>';



// セレクトボックス用データ
$sql_classrooms = 'SELECT * FROM m_classrooms';
$stmt_classrooms = $db->prepare($sql_classrooms);
$stmt_classrooms->execute();
$m_classrooms = $stmt_classrooms->fetchAll(PDO::FETCH_ASSOC);

$sql_staffs = 'SELECT * FROM m_carcon_staffs';
$stmt_staffs = $db->prepare($sql_staffs);
$stmt_staffs->execute();
$m_carcon_staffs = $stmt_staffs->fetchAll(PDO::FETCH_ASSOC);


// echo '<pre>';
// print_r($m_carcon_staffs);
// echo '</pre>';


// 日付の曜日出し
function day($datetime, $type)
{
  $week = ["日", "月", "火", "水", "木", "金", "土"];
  $timestamp = strtotime($datetime);
  return date('n月d日', $timestamp) . '(' . $week[date('w', $timestamp)] . ')';
}

?>



<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ドラッグ＆ドロップ 発展編 -DB連携-</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/style.css">
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
      border-width: 4px !important;
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



/* ぷにゅプルンのアニメーション設定 */
.punyu-move {
  transition: 
    transform 0.8s cubic-bezier(0.5, 1.75, 0.3, 0.8),
    border-radius 0.8s cubic-bezier(0.5, 1.75, 0.3, 0.8) !important;
}



  </style>
</head>

<body class="bg-light admin-wrapper">

  <?php
  require dirname(__FILE__) . '/sidebar.php';
  ?>


  <main class="container-fluid py-5">
    <header class=" mb-5">
      <h1 class="display-5 fw-bold text-center">予約日程表</h1>


      <details class="details">
        <summary class="details-summary card-header accordion__title">
          使い方について
        </summary>

        <dl class="p-3 details-content">
          <dt class="card-text">予約の入れ替え</dt>
          <dd class="mb-3">ドラッグ&ドロップで予約の入れ替えができます。</dd>

          <dt class="card-text">教室・講師</dt>
          <dd class="mb-3">初期値では未設定となっております。選択後、設定更新ボタンを押して確定してください。また、設定後も学生側の画面には表示されません。</dd>


          <dt class="card-text">キャリコン＋追加</dt>
          <dd class="mb-3">青いボタンからラインを追加できます。追加する日付を選択して追加してください。
            追加後、学生側のキャリコン＋の空き枠として表示されるようになります。
          </dd>

          <dt class="card-text">予約の削除</dt>
          <dd class="mb-3">既存の予約の削除は可能です。赤いボタン「Drop to Delete」へドラッグ&ドロップしてください。</dd>

          <dt class="card-text">新規予約</dt>
          <dd class="mb-3"> キャリコン＋の新規予約は学生アプリからのみ可能です。申請するようご連絡ください。
            必須キャリコンの予約については、コース詳細ページの一括予約ボタンから可能です。
            ※既に予約がある場合は使用できません。その場合は、学生側から申請するようにお願いします。
          </dd>


        </dl>
      </details>



      <div id="alert-wrapper"></div>
    </header>



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
    // print_r($s);
    // echo '</pre>';

    ?>


    <?php foreach ($grouped_students as $line_id => $tasks_in_line): ?>

      <?php
      $student = $tasks_in_line[0]; // 代表データ

      //教室と講師のプルダウンの初期値表示用
      $current_classroom_id = $student['classroom_id'];
      $current_staff_id     = $student['carcon_staff_id'];

      $current_date = $student['line_date'];

      ?>


      <?php
      if ($prev_date !== $current_date):

        // ブートストラップ対応のための記述　最初のループ以外は row を閉じる
        if ($prev_date !== null) echo '</div>';
      ?>

        <!-- ブートストラップ対応　1. 日付ラインエリア（rowの外に出すことで100%広がる） -->
        <div class="col-12 mt-5 mb-3">
          <div class="d-flex align-items-center gap-3 bg-secondary-subtle p-2 rounded shadow-sm">

            <!-- <div class="col-12" style="flex: 0 0 100%; max-width: 100%; width: 100%;">
            <div class="d-flex align-items-center mt-4 mb-2"> -->


            <!-- 俺追加↓ -->
            <button type="button" class="open-line-btn btn btn-primary btn-sm text-nowrap py-4 px-4">キャリコン＋ 追加</button>


            <!-- 俺追加↑ -->


            <!-- 日付ライン -->
            <div class="flex-grow-1 d-flex align-items-center">
              <div class="border-top border-secondary opacity-50 flex-grow-1"></div>
              <span class="mx-3 fw-bold text-secondary text-nowrap" style="font-size: 1.0rem;">

                <?= htmlspecialchars($current_date) ?>

              </span>
              <div class="border-top border-secondary opacity-50 flex-grow-1"></div>
            </div>


            <!-- 削除エリア -->
            <div class="delete-area bg-danger text-white rounded text-nowrap py-4 px-5"
              style="font-size: 0.75rem; cursor: pointer; border: 1px dashed white;">
              Drop to Delete
            </div>

          </div>
        </div>

        <!-- ブートストラップ対応　2. 再びカードを並べるための row を開始 -->
        <!-- 横のカードの数の指定 row-cols-md- 1~6 ←これ -->
        <div class="row row-cols-1 row-cols-md-4 g-3 w-100 px-3">

        <?php endif; ?>

        <!-- ブートストラップ対応　3. ここにカード（col）の処理 -->
        <div class="col">
          <div class="status-column border border-2 border-secondary-subtle rounded-3 p-1 bg-light shadow-sm <?= $bg_color_class ?>">

            <div class="d-flex justify-content-between align-items-start mb-0">
              <h2 class="h6 fw-bold text-center border-bottom pb-2 mb-2">
                <?= 'ID : ' . htmlspecialchars($line_id . ' / ' . day($current_date, 7)) ?>
              </h2>

              <!--  俺追加　削除用ボタン -->
              <button class="btn-close delete-status-btn"
                data-id="<?php echo $student['line_id']; ?>"
                style="font-size: 1.0rem;"></button>
            </div>




            <form action="schedule_class_staff_update_do.php" class="mb-2" method="POST">

              <input type="hidden" name="line_id" value="<?= htmlspecialchars($line_id) ?>">
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <select name="classroom_id" class="form-select form-select-sm small">
                    <option value="">教室選択</option>

                    <?php foreach ($m_classrooms as $class):
                      $selected = ($class['id'] == $current_classroom_id) ? 'selected' : '';
                    ?>

                      <option value="<?php echo $class['id']; ?>" <?php echo $selected; ?>>
                        <?php echo $class['name']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-6">
                  <select name="staff_id" class="form-select form-select-sm small">
                    <option value="">講師選択</option>

                    <?php foreach ($m_carcon_staffs as $staff):
                      $selected_staff = ($staff['id'] == $current_staff_id) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $staff['id']; ?>" <?php echo $selected_staff; ?>>
                        <?php echo $staff['last_name'] . $staff['first_name']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="text-center mt-2">
                <button type="submit" class="btn btn-primary btn-sm">教室・講師 / 設定更新</button>
              </div>

            </form>




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
                    data-slot-index="<?= $i ?>">

                    <!-- data-task-id=には 誰を動かしたかわかるため reservation_idをいれる-->
                    <?php if ($task): ?>
                      <div class="card 
                  <?php if ($task['is_plus_carcon'] === 0): echo 'bg-warning';
                      else: echo 'bg-info';
                      endif; ?> w-100 h-100 task-item border-0 shadow-none d-flex align-items-center justify-content-center"
                        id="task-<?= htmlspecialchars($task['reservation_id']) ?>"
                        data-task-id="<?= htmlspecialchars($task['reservation_id']) ?>"


                        draggable="true"

                        style="cursor: move; font-size: 0.75rem; font-weight: bold;">
                        <?php if ($task['is_plus_carcon'] === 0): echo '必 / ';
                        else: echo '＋ / ';
                        endif; ?>
                        <?= htmlspecialchars($task['classroom_name'] . ' ' . $task['student_no'] . ' ' . $task['last_name'] . $task['first_name']); ?>
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


      <dialog id="modal-line" class="modal-line form p-5 w-50">
        <div class="row mb-3">
          <h5 class="fw-bold">キャリコン＋ 追加</h5>
        </div>

        <div class="row mb-3">
          <div class="col-12 mb-3">
            <label for="line-date" class="form-label">追加する日付</label>
            <input type="date" id="line-date" class="line-date form-control" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="d-flex gap-3">
            <button class="cancel-line-btn btn btn-secondary flex-fill" type="button">キャンセル</button>
            <button class="btn btn-primary flex-fill" id="add-line-btn" type="button">追加</button>
          </div>
      </dialog>


  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="../js/script_schedule.js"></script>

</body>


</html>