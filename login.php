<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['id'])) {
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
    <?php
    include('head_link.php');
    ?>
    <title>ログイン</title>
</head>

<body>
    <?php
    include('header.php');
    ?>

    <main class="user-login d-flex flex-column justify-content-center">
        <div class="user-wrapper message-area">
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo $type[$message['type']]; ?> alert-dismissible" role="alert">
                    <div>
                        <?php echo $message['msg']; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="user-wrapper user-card px-4 py-5 shadow rounded-2">
            <h2 class="mb-5 text-center fw-bold">キャリコン予約</h2>
            <form action="login_do.php" method="post" class="d-flex flex-column align-items-center">
                <div class="mb-4">
                    <label for="login_id" class="mb-2 form-label">
                        | ログインID
                    </label>
                    <input type="text" name="login_id" id="login_id" class="form-control" placeholder=" 例：202646A01">
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