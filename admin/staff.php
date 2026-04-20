<?php
require_once __DIR__ . '/../includes/functions.php';

try {
    $db = db_connect();
    $sql = "SELECT * FROM m_admin_staffs";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $admin_staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>管理者一覧</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>管理者一覧</h1>
        <table>
            <thead>
                <tr>
                    <th>管理ID</th>
                    <th>スタッフID</th>
                    <th>スタッフ名</th>
                    <th>追加日時</th>
                    <th>更新日時</th>
                    <th>変更</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admin_staffs as $data): ?>
                    <tr>
                        <td><?php echo h($data["id"]); ?></td>
                        <td><?php echo h($data["staff_id"]); ?></td>
                        <td><?php echo h($data["last_name"] . $data["first_name"]); ?></td>
                        <td><?php echo h(format_date($data["created_at"], 1)); ?></td>
                        <td><?php echo h(format_date($data["updated_at"], 1)); ?></td>
                        <td><a href="staff_edit.php?id=<?php echo h($data["id"]); ?>">変更</a></td>
                        <td><a href="staff_del_do.php?id=<?php echo h($data["id"]); ?>">削除</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="staff_add.php">管理者を追加</a>
    </section>
</body>

</html>