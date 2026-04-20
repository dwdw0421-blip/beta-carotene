<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['login_admin_id'])) {
    header('location:index.php');
    exit();
}

$message = $_SESSION['res_message'] ?? '';
unset($_SESSION['res_message']);
$type = ['danger', 'primary'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- style.css -->
    <link rel="stylesheet" href="../css/style.css">
    <title>管理者ログイン</title>
</head>

<body>
    <main class="user-login d-flex flex-column justify-content-center">
        <div id="user-wrapper message-area">
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo $type[$message['type']]; ?> alert-dismissible" role="alert">
                    <div>
                        <?php echo $message['msg']; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="user-wrapper user-card px-4 py-5 shadow">
            <h2 class="mb-5 text-center fw-bold">管理者ログイン</h2>
            <form action="check_login.php" method="post" class="d-flex flex-column align-items-center">
                <div class="mb-4">
                    <label for="staff_id" class="mb-2 form-label">
                        | 管理者ID
                    </label>
                    <input type="text" name="staff_id" id="staff_id" class="form-control" placeholder=" 例：recarent">
                </div>

                <div class="mb-5">
                    <label for="password" class="mb-2 form-label">
                        | パスワード
                    </label>
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