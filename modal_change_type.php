<!-- 面談の「形式変更」モーダル処理（HTMLとJSの処理をまとめた手抜き実装） -->
<div class="modal fade" id="modal_change_type" tabindex="-1" aria-labelledby="modalLabelType" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabelType">予約の変更</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>現在の予約日</p>
                <p id="currentDate"></p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radioType" id="radioType1">
                    <label class="form-check-label" for="radioType1">
                        対面
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radioType" id="radioType2">
                    <label class="form-check-label" for="radioType2">
                        ZOOM
                    </label>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-primary ms-2">確認画面に進む</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("modal_change_type");

        modal.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget;

            const id = button.dataset.id;
            const currentType = button.dataset.currentType;
            const date = button.dataset.date;
            const slotText = button.dataset.slotText;

            // モーダル内の要素に反映
            document.getElementById("currentDate").textContent = date + " " + slotText;
            let radio;
            if (currentType === 1) {
                radio = document.getElementById("radioType1");
            } else {
                radio = document.getElementById("radioType2");
            }
            console.log(radio);
            radio.checked = true;
        });
    });
</script>