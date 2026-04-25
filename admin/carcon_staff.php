<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['login_admin_id'])) {
    header('location:login.php');
    exit();
}

try {
    $db = db_connect();
    $sql = "SELECT * FROM m_carcon_staffs WHERE is_deleted=0";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $carcon_staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit($e->getMessage());
}
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
    <title>キャリコン担当者一覧</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1 class="display-5 fw-bold text-center">キャリコン担当者一覧</h1>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">管理ID</th>
                    <th scope="col">スタッフ名</th>
                    <th scope="col">追加日時</th>
                    <th scope="col">更新日時</th>
                    <th scope="col">変更</th>
                    <th scope="col">削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carcon_staffs as $data): ?>
                    <tr>
                        <td><?php echo h($data["id"]); ?></td>
                        <td><?php echo h($data["last_name"] . $data["first_name"]); ?></td>
                        <td><?php echo h(format_date($data["created_at"], 1)); ?></td>
                        <td><?php echo h(format_date($data["updated_at"], 1)); ?></td>
                        <td><a href="carcon_staff_edit.php?id=<?php echo h($data["id"]); ?>" class="btn btn-primary d-inline-block">変更</a></td>
                        <td><a href="carcon_staff_del_do.php?id=<?php echo h($data["id"]);  ?>" onclick="return confirm('削除してよろしいですか？');" class="btn btn-danger d-inline-block">削除</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="carcon_staff_add.php" class="btn btn-primary d-inline-block">キャリコン担当者を追加</a>
    </section>
</body>

</html>