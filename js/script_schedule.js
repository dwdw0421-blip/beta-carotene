window.addEventListener('DOMContentLoaded', async () => {
  // イベントリスナーの登録
  setupEventListener();
  init();
});

// 初期化関数
async function init() {
  
  console.log("初期化開始: ドラッグ＆ドロップを設定します");

  // ドラッグ可能なアイテムすべてにイベントを設定
  document.querySelectorAll('.task-item').forEach(task => {
    task.addEventListener('dragstart', handleDragStart);
  });

  // ドロップ先の枠すべてにイベントを設定
  document.querySelectorAll('.drop-zone').forEach(zone => {
    zone.addEventListener('dragover', (e) => e.preventDefault());
    zone.addEventListener('drop', handleDrop);
  });
}

// 最後に init を実行
init();



//  11で追加　差し替え
document.addEventListener('DOMContentLoaded', () => {
  const zones = document.querySelectorAll('.drop-target');

  // ドラッグ開始
  document.addEventListener('dragstart', (e) => {
    if (e.target.classList.contains('task-item')) {
      e.dataTransfer.setData('text/plain', e.target.id);
      e.target.style.opacity = '0.5';
    }
  });

  document.addEventListener('dragend', (e) => {
    if (e.target.classList.contains('task-item')) {
      e.target.style.opacity = '1';
    }
  });

  zones.forEach(zone => {
    zone.addEventListener('dragover', (e) => {
      e.preventDefault(); // ドロップを許可
      zone.style.backgroundColor = 'rgba(0,0,0,0.05)';
    });

    zone.addEventListener('dragleave', () => {
      zone.style.backgroundColor = 'transparent';
    });

    zone.addEventListener('drop', (e) => {
      e.preventDefault();
      zone.style.backgroundColor = 'transparent';
      
      // 1枠1タスク制限：すでにタスクがあれば拒否
      if (zone.querySelector('.task-item')) {
        return;
      }

      const id = e.dataTransfer.getData('text/plain');
      const taskEl = document.getElementById(id);
      if (!taskEl) return;

      const originZone = taskEl.parentElement;

      // 移動先の「空き」文字を消してタスクを追加
      const placeholder = zone.querySelector('.placeholder-text');
      if (placeholder) placeholder.remove();
      zone.appendChild(taskEl);

      // 元の場所を「空き」表示に戻す
      if (originZone && originZone !== zone) {
        originZone.innerHTML = '<span class="placeholder-text text-muted" style="font-size: 0.7rem;">(空き)</span>';
      }

      // DB更新（必要であれば関数を呼び出す）
      const taskId = taskEl.dataset.taskId;
      const statusId = zone.dataset.statusId;
      console.log(`Saved: Task ${taskId} to Status ${statusId}`);
    });
  });
});

// -------------------------------------

function setupEventListener() {
  // --- 1. ドラッグ＆ドロップ関連 ---
  document.addEventListener('dragstart', (e) => {
    if (e.target.classList.contains('task-item')) {
      e.dataTransfer.setData('text/plain', e.target.id);
    }
  });

  document.addEventListener('dragover', (e) => {
    e.preventDefault();
    const deleteArea = e.target.closest('.delete-area');
    if (deleteArea) deleteArea.classList.add('bg-danger', 'text-white');
  });

  document.addEventListener('dragleave', (e) => {
    const deleteArea = e.target.closest('.delete-area');
    if (deleteArea) deleteArea.classList.remove('bg-danger', 'text-white');
  });

  document.addEventListener('drop', async (e) => {
    e.preventDefault();
    const elementId = e.dataTransfer.getData('text/plain');
    const dragItem = document.getElementById(elementId);
    if (!dragItem) return;

    const taskId = dragItem.dataset.taskId;

    // A. 削除エリアへのドロップ
    const deleteArea = e.target.closest('.delete-area');
    if (deleteArea) {
      deleteArea.classList.remove('bg-danger', 'text-white');
      if (confirm('このタスクを削除しますか？')) {
        deleteTask(taskId); 
      }
      return; 
    }

    // B. スロット(drop-zone)へのドロップ
    const targetSlot = e.target.closest('.drop-zone');
    if (targetSlot && !targetSlot.querySelector('.task-item')) {
      const originZone = dragItem.parentElement;
      const placeholder = targetSlot.querySelector('span');
      if (placeholder) placeholder.remove();
      targetSlot.appendChild(dragItem);

      if (originZone && originZone !== targetSlot) {
        originZone.innerHTML = '<span class="text-muted" style="font-size: 0.6rem; opacity: 0.4;">+</span>';
      }
      updateDatabase(taskId, targetSlot.dataset.statusId, targetSlot.dataset.slotIndex);
    }
  });

  // --- 2. ボタン・モーダル関連 ---
  const openBtn = document.getElementById('open-btn');
  const addBtn = document.getElementById('add-btn');
  const cancelBtn = document.getElementById('cancel-btn');
  const modal = document.getElementById('modal');
  
  const openLineBtn = document.getElementById('open-line-btn');
  const addLineBtn = document.getElementById('add-line-btn'); // ID注意
  const cancelLineBtn = document.getElementById('cancel-line-btn');
  const modalLine = document.getElementById('modal-line');

  // const openHeldBtn = document.getElementById('open-line-btn');
  // const addHeldBtn = document.getElementById('add-line-btn'); // ID注意
  // const cancelHeldBtn = document.getElementById('cancel-line-btn');
  // const modalHeld = document.getElementById('modal-held');


  if(openBtn) openBtn.onclick = () => modal.showModal();
  if(openLineBtn) openLineBtn.onclick = () => modalLine.showModal();

  if(openLineBtn) openLineBtn.onclick = () => modalLine.showModal();

  if(cancelBtn) cancelBtn.onclick = () => modal.close();
  if(cancelLineBtn) cancelLineBtn.onclick = () => modalLine.close();

  // タスク登録ボタン
  if(addBtn) {
    addBtn.onclick = async () => {
      const taskTitle = document.getElementById('task-title').value;
      if (!taskTitle) return alert('タスク名を入力してください');
      await addTask({ title: taskTitle });
    };
  }

  // ステータス(line)登録ボタン 
  if (addLineBtn) {
    addLineBtn.onclick = async () => {
      const lineTitle = document.getElementById('line-title').value;
      if (!lineTitle) return alert('名前を入力してください');
      await addLine({ title: lineTitle });
    };
  }


// 教室情報登録ボタン 
  // if (addHeldBtn) {
  //   addHeldBtn.onclick = async () => {
  //     const heldTitle = document.getElementById('held-title').value;
  //     if (!heldTitle) return alert('名前を入力してください');
  //     await addHeld({ title: heldTitle });
  //   };
  // }


}




// --- 3. 通信系関数 ---

function deleteTask(taskId) {
  const id = parseInt(String(taskId).replace(/[^\d]/g, ''));
  if (!id) return;

  // 1. 命令を出す（結果を一切待たない）
  fetch('schedule_student_delete_do.php', { //ファイルパス注意
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id }),
    keepalive: true  // ページ遷移中も通信を維持する設定
  });

  // 2. 待機せずに即座に強制リロード
  window.location.reload();
}

async function addTask(newTaskData) {
  try {
    
    const res = await fetch('schedule_student_add_do.php', { //ファイルパス注意
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newTaskData)
    });
    if (!res.ok) throw new Error('通信エラー');
    location.reload(); // 登録後はリロードが確実
  } catch (error) { console.error(error); }
}

async function addLine(newLineData) {
  try {
    // PHPファイル名を「my_add_line.php」に統一
    const res = await fetch('schedule_add_do.php', { //ファイルパス注意
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newLineData)
    });
    if (!res.ok) throw new Error('通信エラー');
    location.reload();
  } catch (error) { console.error(error); }
}










// /**
//  * タスクを取得する非同期関数
//  *  * @returns {Array} タスクのJSONデータ
//  */
// async function getTaskData() {
//   try {
//     let res = await fetch('./get_task_data.php');//ファイルパス注意 ◎
//     if (!res.ok) throw new Error('タスクデータの取得に失敗しました');
//     let json = await res.json();
//     return json;
//   } catch (error) {
//     console.error(error);
//     showMsg({ msg: 'エラー: ' + error.message });
//   }
// }

// /**
//  * ステータスを取得する非同期関数
//  * @returns ステータスのJSONデータ
//  */
// async function getStatusData() {
//   try {
//     let res = await fetch('./get_status_data.php');//ファイルパス注意 ◎
//     if (!res.ok) throw new Error('ステータスデータの取得に失敗しました');
//     let json = await res.json();
//     return json;
//   } catch (error) {
//     console.error(error);
//     showMsg({ msg: 'エラー: ' + error.message });
//   }
// }



/**
 * tasksテーブルを更新する非同期関数
 * @param {Object} dataObject DB更新に必要な値の配列
 */
async function updateTask(dataObject) {
  try {
    const res = await fetch('./schedule_student_update.php', {//ファイルパス注意
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(dataObject), // JSON文字列を送信
    });
    if (!res.ok) throw new Error('更新に失敗しました');
    const json = await res.json();
    showMsg(json);
  } catch (error) {
    console.error(error);
    showMsg({ msg: 'エラー: ' + error.message });
  }
}

async function deleteTask(taskId) {
  const id = parseInt(String(taskId).replace(/[^\d]/g, ''));
  const res = await fetch('schedule_student_delete_do.php', {//ファイルパス注意
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  });
  const json = await res.json();
  
  // メッセージを出して、OKを押したらリロード
  alert(json.msg); 
  location.reload();
}

// メッセージの表示
function showMsg(json) {
  const toast = document.createElement('div');
  toast.popover = 'manual';
  toast.classList.add('msg-toast', 'alert', 'alert-success', 'shadow', 'p-3');
  toast.textContent = json.msg;

  document.body.append(toast);
  toast.showPopover();

  repositionToasts();

  setTimeout(() => {
    if (document.body.contains(toast)) {
      toast.hidePopover();
      toast.remove();
      repositionToasts();
    }
  }, 3000);
}

// トーストの位置調整用関数
function repositionToasts() {
  const toasts = Array.from(document.querySelectorAll('.msg-toast'));
  let currentOffset = 20;
  toasts.forEach(toast => {
    toast.style.bottom = `${currentOffset}px`;
    currentOffset += toast.offsetHeight + 10;
  });
}



// 俺追加　lineを×ボタンで削除する
function setupDeleteButtons() {
  document.querySelectorAll('.delete-status-btn').forEach(btn => {
    btn.onclick = async (e) => {
      // 確認ダイアログ
      if (!confirm('このlineを削除しますか？')) return;

      const id = e.target.dataset.id; // ボタンの data-id を取得

      try {
        const res = await fetch('./schedule_del_do.php', {//ファイルパス注意
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        if (!res.ok) throw new Error('削除に失敗しました');
        
        const json = await res.json();
        if (json.status === 'error') {
  // タスクが残っている場合はポップアップ（アラート）を出す
  alert(json.msg);
  // リロードさせずにここで処理を終了する

  return; 
}
        
        // 画面を更新
        location.reload(); 
        
      } catch (error) {
        console.error(error);
        alert(error.message);
      }
    };
  });
}

// 最初の読み込み時に実行
setupDeleteButtons();




// 11で追加

// ドラッグ開始時：タスクのIDを保存
function handleDragStart(e) {
  e.dataTransfer.setData("text/plain", e.target.id);
}

// ドロップ時：要素の移動とDB保存
async function handleDrop(e) {
  e.preventDefault();
  const zone = e.currentTarget;

  // すでにタスクが入っているスロットなら中断
  if (zone.querySelector('.task-item')) return;

  const id = e.dataTransfer.getData("text/plain");
  const taskEl = document.getElementById(id);
  if (!taskEl) return;

  const originZone = taskEl.parentElement;

  // 1. 移動先の「空き」文字を消してタスクを配置
  const placeholder = zone.querySelector('span');
  if (placeholder) placeholder.remove();
  zone.appendChild(taskEl);

  // 2. 移動元の枠を「空き」表示に戻す
  if (originZone && originZone !== zone) {
    originZone.innerHTML = '<span class="text-muted small" style="font-size: 0.65rem;">(空き)</span>';
  }

  // 3. データベース更新処理 (JSON送信)
  const taskId = taskEl.dataset.taskId;
  const newStatusId = zone.dataset.statusId;
  
  await updateDatabase(taskId, newStatusId);
}

async function updateDatabase(taskId, statusId, slotIndex) {
  // 「task-123」のような文字列から「123」だけを取り出して数値にする
  const cleanId = typeof taskId === 'string' ? taskId.replace('task-', '') : taskId;

  const data = {
    id: parseInt(cleanId),
    status: parseInt(statusId),
    slot_index: parseInt(slotIndex)
  };

  console.log("送信データ:", data); // デバッグ用

  try {
    const response = await fetch('schedule_student_update.php', {//ファイルパス注意
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await response.json();
    console.log('成功:', result.msg);
  } catch (error) {
    console.error('保存失敗:', error);
  }
}




async function handleDrop(e) {
  e.preventDefault();
  const zone = e.currentTarget;
  if (zone.querySelector('.task-item')) return;

  const idStr = e.dataTransfer.getData("text/plain");
  const taskEl = document.getElementById(idStr);
  if (!taskEl) return;

  const originZone = taskEl.parentElement;
  const placeholder = zone.querySelector('span');
  if (placeholder) placeholder.remove();
  zone.appendChild(taskEl);

  if (originZone && originZone !== zone) {
    originZone.innerHTML = '<span class="text-muted small" style="font-size: 0.65rem;">(空き)</span>';
  }

  // 送信データ作成
  const taskId = taskEl.dataset.taskId;
  const newStatusId = zone.dataset.statusId;
  const newSlotIndex = zone.dataset.slotIndex; // 追加

  // updateDatabaseを呼び出し（JSON形式）
  updateDatabase(taskId, newStatusId, newSlotIndex);
}







