<!-- 面談の「日付変更」モーダル処理（HTMLとJSの処理をまとめた手抜き実装） -->
<div class="modal fade" id="modal_change_detail" tabindex="-1" aria-labelledby="modalLabelDetail" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabelDetail">予約の変更</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./change_request.php" method="post">
                <div class="modal-body">
                    <p>現在の予約日</p>
                    <p id="currentDateDetail"></p>
                    <select id="changeDetailData" name="changeDetailData" class="form-select form-select-sm" aria-label="Small select example">
                        <option selected>変更相手を選択してください</option>
                    </select>
                </div>
                <div class="modal-footer justify-content-end">
                    <!-- 変更元のID -->
                    <input type="hidden" name="currentDetailId_detail" id="currentDetailId_detail" value="">
                    <input type="hidden" name="request_detail" id="request_detail" value=1>
                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">閉じる</button>
                    <button type="submit" class="btn btn-primary ms-2">確認画面に進む</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("modal_change_detail");
        const currentDetailId = document.getElementById("currentDetailId_detail");

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
            const select = document.getElementById("changeDetailData");
            classDataList.forEach((data, i) => {
                const option = document.createElement("option");
                option.value = data.detail_id;
                option.textContent = `${data.date} ${data.time} | ${data.student_name}`;

                // 必要ならJS用に保持
                option.dataset.date = data.date;
                option.dataset.slotIndex = data.slot_index;
                option.dataset.studentName = data.student_name;

                select.appendChild(option);
            });
        });
    });
</script>