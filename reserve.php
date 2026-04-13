<!DOCTYPE html>
<html lang="ja">

<?php
include('head.php');
?>


<body>
    <?php
    include('header.php');
    ?>
    <main>
        <section class="wrapper">
            <h1>面談予約</h1>

            <div class="reserve-card">
                <?php if () :?>
                <p class="category">必須</p>
                <?php else: ?>
                <p class="category">任意</p>
                <?php endif; ?>

                <div class="item">
                    <div class="day-item">
                        <label class="item-name">面談希望日</label>
                        <select class="form-select" aria-label="Default select example">
                            <option selected class="defo">選択してください</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>

                    <div class="time-item">
                        <label class="item-name">面談希望時刻</label>
                        <select class="form-select" aria-label="Default select example">
                            <option selected class="defo">選択してください</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>

                    <div class="check-group">
                        <label class="item-name">面談形式</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault1">
                            <label class="form-check-label" for="radioDefault1">
                                対面
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault2" checked>
                            <label class="form-check-label" for="radioDefault2">
                                ZOOM
                            </label>
                        </div>
                    </div>
                </div>

                <div class="btn">
                    <button id="next-btn">確認画面に進む</button>
                    <button id="return-btn">戻る</button>
                </div>

            </div>
        </section>
    </main>
    <!-- <footer></footer> -->
</body>

</html>