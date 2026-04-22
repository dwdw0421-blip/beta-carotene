<!-- 面談の「日付変更」モーダル処理（HTMLとJSの処理をまとめた手抜き実装） -->
<div class="modal fade" id="modal_change_detail" tabindex="-1" aria-labelledby="modalLabelDetail" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabelDetail">予約の変更</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>現在の予約日</p>
                <p id="currentDateDetail"></p>
                <select id="detailDataList" class="form-select form-select-sm" aria-label="Small select example">
                    <option selected>変更相手を選択してください</option>
                </select>
            </div>
            <div class="modal-footer justify-content-end">
                <!-- 変更元のID -->
                <input type="hidden" name="current_detail_id" id="currentDetailId" value="">
                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-primary ms-2">確認画面に進む</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("modal_change_detail");
        const currentDetailId = document.getElementById("currentDetailId");

        modal.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget;

            const id = button.dataset.id;
            const date = button.dataset.date;
            const slotText = button.dataset.slotText;
            const classDataList = JSON.parse(button.dataset.classDataList);

            // hiddenに変更元IDをセット
            currentDetailId.value = id;

            // モーダル内の要素に反映
            document.getElementById("currentDateDetail").textContent = date + " " + slotText;
            // 選択肢に表示する予約済み枠設定
            const select = document.getElementById("detailDataList");
            classDataList.forEach((data, i) => {
                const option = document.createElement("option");
                option.value = data.detail_id;
                option.textContent = `${data.date} ${data.slot_index} | ${data.student_name}`;

                // 必要ならJS用に保持
                option.dataset.date = data.date;
                option.dataset.slotIndex = data.slot_index;
                option.dataset.studentName = data.student_name;

                select.appendChild(option);
            });
        });
    });
</script>

<form action="receive.php" method="post">
    <select name="detail_id">
        <option value="1" data-date="2026-04-18" data-slot_index="0" data-student_name="苗字_1_1 名前_1_1">
            2026-04-18 0 | 苗字_1_1 名前_1_1
        </option>
    </select>

    <button type="submit">送信</button>
</form>