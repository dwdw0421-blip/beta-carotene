<!DOCTYPE html>
<html lang="ja">

<head>
    <?php
    include('head_link.php');
    ?>
    <title>ユーザー｜TOP</title>
</head>

<body>
    <?php
    include('header.php');
    ?>

    <main class="user-login d-flex flex-column justify-content-center">
        <div class="user-wrapper user-card px-4 py-5 shadow">
            <h2 class="mb-5 text-center fw-bold">キャリコン予約</h2>
            <form action="check_login.php" method="post" class="d-flex flex-column align-items-center">
                <div class="mb-4">
                    <label for="name" class="mb-2 form-label">| ログインID</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="出席番号">
                </div>

                <div class="mb-5">
                    <label for="password" class="mb-2 form-label">| パスワード</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="パスワード">
                </div>

                <div class="text-center">
                    <input type="submit" value="ログイン" class="btn btn-primary px-5 py-2 btn-lg">
                </div>
            </form>
        </div>
    </main>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>