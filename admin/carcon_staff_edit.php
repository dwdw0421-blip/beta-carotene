<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$id = (isset($_GET["id"]) ? (int)$_GET["id"] : "");

if (empty($id)) {
    header("location: staff.php");
    exit();
}

try {
    $db = db_connect();
    $sql = "SELECT * FROM m_carcon_staffs WHERE id=:id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>キャリコン担当者を編集</title>
</head>

<body class="admin-wrapper">
    <?php
    require dirname(__FILE__) . '/sidebar.php';
    ?>
    <section class="admin-main-wrapper">
        <h1>キャリコン担当者を編集</h1>
        <form class="row card-body bg-light" action="./carcon_staff_edit_do.php" method="post" onsubmit="return confirm('キャリコン担当者データを変更しますか？')">

            <div class="mb-2">
                <label class="form-label" for="id">管理ID</label>
                <p><?php echo h($result["id"]); ?></p>
            </div>

            <div class="mb-2">
                <label class="form-label" for="last_name">苗字</label>
                <input class="form-control" type="text" name="last_name" id="last_name" value="<?php echo h($result["last_name"]); ?>" required>
            </div>

            <div class="mb-2">
                <label class="form-label" for="last_name">名前</label>
                <input class="form-control" type="text" name="first_name" id="first_name" value="<?php echo h($result["first_name"]); ?>" required>
            </div>

            <input type="hidden" name="id" value="<?php echo h($result["id"]); ?>">
            <input type="submit" class="btn btn-outline-danger d-inline-block" value="変更する">
        </form>
    </section>
</body>

</html>