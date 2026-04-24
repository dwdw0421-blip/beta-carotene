<?php
if (isset($_SESSION['id']) && isset($db)) {
    try {
        $header_sql = 'SELECT last_name, first_name FROM m_students WHERE id = :id';
        $header_stmt = $db->prepare($header_sql);
        $header_stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
        $header_stmt->execute();
        $header_user = $header_stmt->fetch(PDO::FETCH_ASSOC);

        $header_user_name = $header_user ? h($header_user['last_name'] . ' ' . $header_user['first_name']) : 'ゲスト';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $header_user_name = 'エラー';
    }
} else {
    $header_user_name = '未ログイン';
}
?>

<header>
    <div class="py-3 text-center shadow mb-5">
        <div class="container px-3">
            <h1 class="mb-2">
                <a href="./index.php">
                    <img class="user-logo img-fluid" src="./img/user-logo.svg" alt="創造社リカレントスクール" style="max-height: 35px;">
                </a>
            </h1>

            <div class="d-flex align-items-center justify-content-center gap-1 text-secondary pt-2 mx-auto border-top" style="max-width: 280px;">
                <span class="material-symbols-outlined fs-6">account_circle</span>
                <p class="mb-0" style="font-size: 0.85rem; letter-spacing: -0.5px;">
                    <span class="text-muted">ログイン中：</span>
                    <?php echo h($header_user_name); ?>
                </p>
            </div>
        </div>
    </div>
</header>