<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ドラッグ＆ドロップ 発展編 -DB連携-</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- style.css -->
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

<body class="bg-light admin-wrapper">

  <?php
  require dirname(__FILE__) . '/sidebar.php';
  ?>
  <main class="container py-5 admin-main-wrapper">


    <header class="text-center mb-5">
      <h1 class="display-5 fw-bold">ドラッグ＆ドロップ 発展編<br> -DB連携-</h1>
      <p class="text-muted">ドラッグ&ドロップでタスクを入れ替える度にDBに保存する</p>
      <div id="alert-wrapper"></div>
    </header>


    <!-- 俺追加↓ -->
    <div class="row mb-4 justify-content-center">
      <div class="col">
        <button type="button" class="btn btn-primary" id="open-line-btn">キャリコンプラス追加</button>
      </div>
    </div>
    <dialog class="form p-5" id="modal-line">
      <div class="row mb-3">
        <div class="col mb-3">
          <label for="line-title" class="form-label">開催日</label>
          <input type="text" name="line-title" id="line-title" class="form-control">
        </div>
      </div>
      <div class="d-flex gap-3">
        <button class="btn btn-secondary flex-fill" type="button" id="cancel-line-btn">Cancel</button>
        <button class="btn btn-primary flex-fill" type="button" id="add-line-btn">Add</button>
      </div>
    </dialog>
    <!-- 俺追加↑ -->


    <div class="row mb-4 justify-content-center">
      <div class="col">
        <button type="button" class="btn btn-primary" id="open-btn">生徒情報追加</button>
      </div>
    </div>
    <dialog class="form p-5" id="modal">
      <div class="row mb-3">
        <div class="col mb-3">
          <label for="task-title" class="form-label">生徒情報</label>
          <input type="text" name="task-title" id="task-title" class="form-control">
        </div>
      </div>
      <div class="d-flex gap-3">
        <button class="btn btn-secondary flex-fill" type="button" id="cancel-btn">Cancel</button>
        <button class="btn btn-primary flex-fill" type="button" id="add-btn">Add</button>
      </div>
    </dialog>





    <?php
require_once 'functions_test.php';
$pdo = db_connect();

// ステータス（3列）を取得
$sql = 'SELECT * FROM statuses';
$stmt = $pdo->query($sql);
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// タスクを全取得
$sql_tasks = 'SELECT * FROM tasks';
$stmt_tasks = $pdo->query($sql_tasks);
$all_tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);
?>




<!-- Bootstrapのグリッドを使用 -->
<div class="row row-cols-1 row-cols-md-6 g-3 mt-4 w-100 px-3">
  <?php foreach ($statuses as $status): ?>
    <div class="col">
      <div class="status-column border border-2 border-secondary-subtle rounded-3 p-2 bg-light shadow-sm">
        
      <div class="d-flex justify-content-between align-items-start mb-0">
        <h2 class="h6 fw-bold text-center border-bottom pb-0 mb-0">
            <?= htmlspecialchars($status['status']) ?>
          </h2>
          <!--  俺追加　削除用ボタン -->
            <button class="btn-close delete-status-btn"
    require_once 'functions_test.php';
    $pdo = db_connect();

    // ステータス（3列）を取得
    $sql = 'SELECT * FROM statuses';
    $stmt = $pdo->query($sql);
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // タスクを全取得
    $sql_tasks = 'SELECT * FROM tasks';
    $stmt_tasks = $pdo->query($sql_tasks);
    $all_tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);
    ?>




    <!-- Bootstrapのグリッドを使用 -->
    <div class="row row-cols-1 row-cols-md-6 g-3 mt-4 w-100 px-3">
      <?php foreach ($statuses as $status): ?>
        <div class="col">
          <div class="status-column border border-2 border-secondary-subtle rounded-3 p-2 bg-light shadow-sm">

            <div class="d-flex justify-content-between align-items-start mb-3">
              <h2 class="h6 fw-bold text-center border-bottom pb-2 mb-2">
                <?= htmlspecialchars($status['status']) ?>
              </h2>
              <!--  俺追加　削除用ボタン -->
              <button class="btn-close delete-status-btn"
                data-id="<?php echo $status['id']; ?>"
                style="font-size: 1.0rem;"></button>
            </div>

            <div class="task-slots">
              <?php
              $hours = ['10:00～', '11:00～', '12:00～', '14:00～', '15:00～', '16:00～'];

      <!--  俺追加　教室記入ボタン -->
    <div class="row mb-0 justify-content-center">
      <div class="col">
        <button type="button" class="btn btn-info btn-sm" id="open-held-btn">開催情報入力</button>
      </div>
    </div>
    <dialog class="form p-5" id="modal-held">
      <div class="row mb-3">
        <div class="col mb-3">
          <label for="held-title" class="form-label">生徒情報</label>
          <input type="text" name="held-title" id="held-title" class="form-control">
        </div>
      </div>
      <div class="d-flex gap-3">
        <button class="btn btn-secondary flex-fill" type="button" id="cancel-btn">Cancel</button>
        <button class="btn btn-primary flex-fill" type="button" id="add-btn">Add</button>
      </div>
    </dialog>

      <div class="h6 fw-bold text-left border-bottom pb-2 mb-2">
          <div>開催教室：</div>
          <div>講師：</div>
      </div>

        <div class="task-slots">
          <?php 
          $hours = ['10:00～', '11:00～', '12:00～', '14:00～', '15:00～', '16:00～'];
          
          // このステータスに属するタスクを抽出
          $current_tasks = array_filter($all_tasks, function($t) use ($status) {
              return (int)$t['status'] === (int)$status['id'];
          });

          for ($i = 0; $i < 6; $i++): 
              $task = null;
              foreach ($current_tasks as $t) {
              // このステータスに属するタスクを抽出
              $current_tasks = array_filter($all_tasks, function ($t) use ($status) {
                return (int)$t['status'] === (int)$status['id'];
              });

              for ($i = 0; $i < 6; $i++):
                $task = null;
                foreach ($current_tasks as $t) {
                  if (isset($t['slot_index']) && (int)$t['slot_index'] === $i) {
                    $task = $t;
                    break;
                  }
                }
              ?>
                <div class="d-flex align-items-center mb-1">
                  <!-- 左側：時間表示 -->
                  <small class="text-secondary fw-bold pe-2" style="width: 45px; text-align: right; font-size: 0.65rem;">
                    <?= $hours[$i] ?>
                  </small>

                  <!-- 右側：スロット -->
                  <div class="drop-zone border border-dashed rounded flex-grow-1 d-flex align-items-center justify-content-center"
                    style="height: 30px; background: #fff; border-color: #ddd; overflow: hidden;"
                    data-status-id="<?= $status['id'] ?>"
                    data-slot-index="<?= $i ?>"
                    ondragover="event.preventDefault()"
                    ondrop="handleDrop(event)">

                    <?php if ($task): ?>
                      <div class="card bg-warning w-100 h-100 task-item border-0 shadow-none d-flex align-items-center justify-content-center"
                        id="task-<?= $task['id'] ?>"
                        data-task-id="<?= $task['id'] ?>"
                        draggable="true"
                        ondragstart="handleDragStart(event)"
                        style="cursor: move; font-size: 0.75rem; font-weight: bold;">
                        <?= htmlspecialchars($task['title']) ?>
                      </div>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.6rem; opacity: 0.4;">+</span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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