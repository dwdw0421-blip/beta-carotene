document.addEventListener('DOMContentLoaded', () => {
  setupEventListener();
  setupDeleteButtons(); 
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
   
    // スロット（ドロップゾーン）の処理
    const dropZone = e.target.closest('.drop-zone');
    if (dropZone) dropZone.classList.add('drag-over'); 

  });

  document.addEventListener('dragleave', (e) => {
    const deleteArea = e.target.closest('.delete-area');
    if (deleteArea) deleteArea.classList.remove('bg-danger', 'text-white');

     // スロット（ドロップゾーン）の処理
    const dropZone = e.target.closest('.drop-zone');
    // チラつき防止（枠の外に完全に出たときだけクラスを消す）
    if (dropZone && !dropZone.contains(e.relatedTarget)) {
      dropZone.classList.remove('drag-over'); //CSSのクラスを解除
    }
});
  

  document.addEventListener('drop', async (e) => {
    e.preventDefault();

    // ドロップした瞬間に、すべての枠の強調色（青）を消す
    document.querySelectorAll('.drop-zone').forEach(zone => {
      zone.classList.remove('drag-over');
    });

    const elementId = e.dataTransfer.getData('text/plain');
    const dragItem = document.getElementById(elementId);
    if (!dragItem) return;

    const taskId = dragItem.dataset.taskId;

    // A. 削除エリアへのドロップ
    const deleteArea = e.target.closest('.delete-area');

    const originSlot = dragItem.parentElement;

    if (deleteArea) {
      deleteArea.classList.remove('bg-danger', 'text-white');
      if (confirm('本当に削除して大丈夫ですか？')) {
        deleteTask(taskId); 
      }
      return; 
    }

    // B. スロット(drop-zone)へのドロップ
    const targetSlot = e.target.closest('.drop-zone');
    if (targetSlot) {
      const originZone = dragItem.parentElement; //移動前の枠
      const existingItem = targetSlot.querySelector('.task-item');//移動先タスク


    // ◎追加　座標の記録（アニメーション用）
      const dragItemRect = dragItem.getBoundingClientRect();
      const existingItemRect = existingItem ? existingItem.getBoundingClientRect() : null;

      
    //入替処理
      if (existingItem) {
        originSlot.appendChild(existingItem); // 相手を自分の元いた場所へ

   
    // ◎追加　相手（existingItem）をぷにゅプルンさせる　（アニメーション用）
      const dx = targetSlot.getBoundingClientRect().left - originSlot.getBoundingClientRect().left;
      const dy = targetSlot.getBoundingClientRect().top - originSlot.getBoundingClientRect().top;
      applyPunyuSwap(existingItem, dx, dy);



        updateDatabase(
          existingItem.dataset.taskId, 
          originSlot.dataset.statusId, 
          originSlot.dataset.slotIndex
        ); // 相手の分を保存
      }else{
            //空き表示
            if (originZone && originZone !== targetSlot) {
                originZone.innerHTML = '<span class="text-muted" style="font-size: 0.65rem;">【 空き 】</span>';
            }
      }

      // 移動先の「空き」文字を消して自分が入る
      const placeholder = targetSlot.querySelector('span');
      if (placeholder) placeholder.remove();
      targetSlot.appendChild(dragItem);


    // ◎追加　自分（dragItem）をぷにゅプルンさせる　（アニメーション用）
    const dx = dragItemRect.left - dragItem.getBoundingClientRect().left;
    const dy = dragItemRect.top - dragItem.getBoundingClientRect().top;
    applyPunyuSwap(dragItem, dx, dy);
      

      //DBの更新
      updateDatabase(
        dragItem.dataset.taskId, 
        targetSlot.dataset.statusId, 
        targetSlot.dataset.slotIndex
      );
    }
  });

  // --- 2. ボタン・モーダル関連 ---
  const openBtn = document.getElementById('open-btn');
  const addBtn = document.getElementById('add-btn');
  const cancelBtn = document.getElementById('cancel-btn');
  const modal = document.getElementById('modal');
  
  const openLineBtn = document.querySelectorAll('.open-line-btn');
  const addLineBtn = document.getElementById('add-line-btn'); // ID注意 これはidが確実
  const cancelLineBtn = document.querySelector('.cancel-line-btn');//モーダルは一つなのでALL無しのquerySelector単数形で
  const modalLine = document.querySelector('.modal-line');//モーダルは一つなのでALL無しのquerySelector単数形で


  if(openBtn) openBtn.onclick = () => modal.showModal();
  if(cancelBtn) cancelBtn.onclick = () => modal.close();
  if(cancelLineBtn) cancelLineBtn.onclick = () => modalLine.close();

  //querySelectorAllで取得すると配列のような感じになるので、forEachで全部のボタンに命令する
  openLineBtn.forEach(btn => {
    btn.onclick = () => modalLine.showModal();
  });

  

  // タスク登録ボタン
  if(addBtn) {
    addBtn.onclick = async () => {
      const taskTitle = document.getElementById('task-title').value;
      if (!taskTitle) return alert('タスク名を入力してください');
      await addTask({ title: taskTitle });
    };
  }

  // キャリコンラインの追加
  if (addLineBtn) {
    addLineBtn.onclick = async () => {
      const lineTitle = document.getElementById('line-date').value;
      if (!lineTitle) return alert('日付を入力してください');
      await addLine({ date: lineTitle, classroom_id: null}); //追加時はnullを登録
    };
  }
}


// --- 3. 通信系関数 ---

// データベース更新
async function updateDatabase(taskId, statusId, slotIndex) {
  const cleanId = typeof taskId === 'string' ? taskId.replace('task-', '') : taskId;
  const data = {
    id: parseInt(cleanId),
    status: parseInt(statusId),
    slot_index: parseInt(slotIndex)
  };

  try {
    const res = await fetch('schedule_student_update_do.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    console.log('保存成功:', result);

    // showMsg(result); // 更新成功メッセージ表示
    
  } catch (error) {
    console.error('保存失敗:', error);
  }
}


async function deleteTask(taskId) {
  const id = parseInt(String(taskId).replace(/[^\d]/g, ''));
  const res = await fetch('schedule_student_delete_task_do.php', {//ファイルパス注意
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  });
  const json = await res.json();
  
  // メッセージを出して、OKを押したらリロード
  alert(json.msg); 
  location.reload();
}


// 新規タスク送信
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


// ライン追加
async function addLine(newLineData) {
  try {
    // PHPファイル名を「my_add_line.php」に統一
    const res = await fetch('schedule_student_add_do.php', { //ファイルパス注意
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newLineData)
    });

    const result = await res.json();

 if (res.ok && result.success) {
      // 成功した時だけリロード
      location.reload();
    } else {
      alert('エラーが発生しました: ' + (result.error || '不明なエラー'));
    }
  } catch (error) { 
    console.error('通信エラー:', error);
    alert('サーバーと通信できませんでした');
  }
}



// 俺追加　lineを×ボタンで削除する
function setupDeleteButtons() {
  document.querySelectorAll('.delete-status-btn').forEach(btn => {
    btn.onclick = async (e) => {
      // 確認ダイアログ
      if (!confirm('この予約枠を削除しますか？')) return;

      const id = e.target.dataset.id; // ボタンの data-id を取得


      try {
        const res = await fetch('./schedule_student_delete_do.php', {//ファイルパス注意
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


// メッセージの表示
// function showMsg(json) {
//   const toast = document.createElement('div');
//   toast.popover = 'manual';
//   toast.classList.add('msg-toast', 'alert', 'alert-success', 'shadow', 'p-3');
//   toast.textContent = json.msg;

//   document.body.append(toast);
//   toast.showPopover();

//   repositionToasts();

//   setTimeout(() => {
//     if (document.body.contains(toast)) {
//       toast.hidePopover();
//       toast.remove();
//       repositionToasts();
//     }
//   }, 3000);
// }

// トーストの位置調整用関数
// function repositionToasts() {
//   const toasts = Array.from(document.querySelectorAll('.msg-toast'));
//   let currentOffset = 20;
//   toasts.forEach(toast => {
//     toast.style.bottom = `${currentOffset}px`;
//     currentOffset += toast.offsetHeight + 10;
//   });
// }




/**
 * tasksテーブルを更新する非同期関数
 * @param {Object} dataObject DB更新に必要な値の配列
 */
async function updateTask(dataObject) {
  try {
    const res = await fetch('./schedule_student_update_do.php', {//ファイルパス注意
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





/**
 * ◎追加　要素を「ぷにゅプルン」と現在の位置へ戻す　（アニメーション用）
 */
function applyPunyuSwap(el, dx, dy) {
  if (!el) return;
  el.classList.remove('punyu-move');
  el.style.transition = 'none';

  // 相手のいた場所からスタート（少し潰す）
  el.style.transform = `translate(${dx}px, ${dy}px) scale(1.2, 0.8)`;
  el.style.borderRadius = "50%";

  el.offsetHeight; // 強制描画

  el.classList.add('punyu-move');
  // 自分の本来の位置（0,0）へ戻る
  el.style.transform = 'translate(0, 0) scale(1, 1)';
  el.style.borderRadius = ""; // 元の形状に戻す

  setTimeout(() => {
    el.classList.remove('punyu-move');
    el.style.transform = '';
  }, 800);
}




// セレクトボックスで同じものを選んだ時に色を付ける
document.addEventListener('DOMContentLoaded', () => {
  const allSelects = document.querySelectorAll('.group-select');

  const checkDuplicates = (groupName) => {
    const targetGroup = document.querySelectorAll(`.group-select[data-group="${groupName}"]`);
    const values = Array.from(targetGroup).map(s => s.value).filter(v => v !== "");

    targetGroup.forEach(s => {
      const isDuplicate = values.filter(v => v === s.value).length > 1;
      // セレクトボックスの次の要素（メッセージ用div）を取得
      const msgElement = s.nextElementSibling;

      if (isDuplicate && s.value !== "") {
        s.style.backgroundColor = "#f8d7da";
        s.style.borderColor = "#dc3545";
        if (msgElement && msgElement.classList.contains('duplicate-msg')) {
          msgElement.style.display = "block"; // メッセージを表示
        }
      } else {
        s.style.backgroundColor = "";
        s.style.borderColor = "";
        if (msgElement && msgElement.classList.contains('duplicate-msg')) {
          msgElement.style.display = "none"; // メッセージを隠す
        }
      }
    });
  };

  allSelects.forEach(select => {
    select.addEventListener('change', () => {
      checkDuplicates(select.getAttribute('data-group'));
    });
  });

  const groupNames = new Set(Array.from(allSelects).map(s => s.getAttribute('data-group')));
  groupNames.forEach(name => checkDuplicates(name));
});



// 日程カードの表示枚数を変更 
const select = document.getElementById('columnSelect');

select.addEventListener('change', () => {
  const newValue = select.value;
  // すべての対象要素を取得
  const targets = document.querySelectorAll('.column-target');

  targets.forEach(row => {
    // 既存の row-cols-md-* クラスを正規表現で探して新しい値に置換
    const currentClass = row.className;
    const regex = /row-cols-md-\d+/;

    if (regex.test(currentClass)) {
      row.className = currentClass.replace(regex, `row-cols-md-${newValue}`);
    } else {
      row.classList.add(`row-cols-md-${newValue}`);
    }
  });
});
