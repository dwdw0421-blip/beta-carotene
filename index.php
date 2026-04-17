<!DOCTYPE html>
<html lang="ja">

<head>
    <?php
    include('head_link.php');
    ?>
    <title>ユーザー｜TOP</title>
</head>

<body class="mb-10">
    <?php
    include('header.php');
    ?>

    <main class="mt-5 d-md-flex flex-row">
        <div class="col-md-6">
            <!-- 次回の予約日時 -->
            <section class="user-wrapper mb-5">
                <h2 class="user-section_title mb-5 text-center">
                    次回の予約日時
                </h2>
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコン（必須面談）</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="d-flex flex-row gap-4 fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                                <dd>10:00～11:00</dd>
                            </div>
                        </div>
                        <dt class="user-card_subtitle mb-2 fs-6">形式</dt>
                        <dd class="fw-bold fs-5">対面 or Zoom</dd>
                    </dl>
                </div>
                <div class="user-card  px-4 py-4 shadow rounded-4">
                    <h3 class="mb-4 fw-bold">キャリコン（任意面談）</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">予約日時</dt>
                            <div class="d-flex flex-row gap-4 fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                                <dd>10:00～11:00</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">形式</dt>
                            <dd class="fw-bold fs-5">対面 or Zoom</dd>
                        </div>
                    </dl>
                </div>
            </section>
            <!-- 申請ステータス -->
            <section class="user-wrapper mb-7">
                <h2 class="user-section_title text-center mb-5">申請ステータス</h2>
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold fs-5">変更申請</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                            <div class="d-flex flex-row fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                            </div>
                        </div>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                            <dd class="fw-bold fs-5">承認待ち or 承認済み or 棄却</dd>
                        </div>
                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">メッセージ</dt>
                            <dd class="fw-bold fs-5">棄却のため、再申請をお願いします。</dd>
                        </div>
                    </dl>
                </div>
                <div class="user-card px-4 py-4 shadow mb-4 rounded-4">
                    <h3 class="mb-4 fw-bold">キャンセル申請</h3>
                    <dl>
                        <div class="mb-2">
                            <dt class="user-card_subtitle mb-2 fs-6">申請日時</dt>
                            <div class="d-flex flex-row gap-4 fw-bold fs-5">
                                <dd>4月 10日 (土)</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="user-card_subtitle mb-2 fs-6">ステータス</dt>
                            <dd class="fw-bold fs-5">承認待ち or 承認済み</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </div>

        <!-- カレンダー -->
        <div class="col-md-6">
            <?php
            include('calendar.php')
            ?>
        </div>
    </main>

    <?php
    include('bottom_bar.php')
    ?>
</body>

</html>