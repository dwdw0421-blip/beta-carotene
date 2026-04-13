<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="./css/style.css">
    <title>ユーザー｜ログイン</title>
</head>

<body>
    <?php
    include('header.php');
    ?>

    <main>
        <div class="user-wrapper user-login px-4 py-5">
            <h1 class="mb-5 text-center">キャリコン予約</h1>
            <form action="check_login.php" method="post">
                <div class="">
                    <div class="mb-5 flex-column">
                        <label for="id" class="mb-2 form-label">| ログインID</label>
                        <input type="text" name="id" id="id" class="form-control" placeholder="出席番号">
                    </div>
                    <div>
                        <label for="password" class="mb-2 form-label">| パスワード</label>
                        <input type="text" name="password" id="password" class="form-control" placeholder="パスワード">
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>