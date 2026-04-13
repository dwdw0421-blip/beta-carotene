<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-64UC4BEhTGwk3eGpak4nO2jqtl7liTS+juXkSJ2gPAQPmlClQO7s5UgCeR6US48g" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="./css/style.css">
    <title>ユーザー｜ログイン</title>
</head>

<body>
    <?php
    include('header.php');
    ?>

    <main>
        <h1>キャリコン予約</h1>

        <form action="">
            <div>
                <label for="">| ログインID</label>
                <input type="text" name="id" id="id" class="">
            </div>

            <div>
                <label for="">| パスワード</label>
                <input type="text" name="password" id="password" class="">
            </div>
        </form>
    </main>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-jdSIJTK9l6XwXj3RixpVDXtMcA2bFd9O81RlLAwhpr2oXRqvQP88rr16IeFXTgFE" crossorigin="anonymous"></script>
</body>

</html>