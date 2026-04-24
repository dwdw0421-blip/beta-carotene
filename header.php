<?php
try {
    //学生情報を取得
    $sql = 'SELECT
    CONCAT(m_students.last_name , " " , m_students.first_name) AS student_name
    FROM m_students 
    WHERE m_students.id  = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $login_id, PDO::PARAM_INT);
    // SQLの実行
    $stmt->execute();
    $student_result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー:' . $e->getMessage());
}
?>

<header>
    <div class="py-3 text-center shadow-sm">
        <div class="container px-3">
            <h1 class="mb-2">
                <a href="./index.php">
                    <img class="user-logo img-fluid" src="./img/user-logo.svg" alt="創造社リカレントスクール" style="max-height: 35px;">
                </a>
            </h1>

            <div class="d-flex align-items-center justify-content-center gap-1 text-secondary pt-2 mx-auto border-top" style="max-width: 280px;">
                <span class="material-symbols-outlined fs-6" style="font-size: 1.1rem;">account_circle</span>
                <p class="mb-0" style="font-size: 0.85rem; letter-spacing: -0.5px;">
                    <span class="text-muted">ログイン中：</span>
                    <?php echo h($student_result['student_name']); ?>
                </p>
            </div>
        </div>
    </div>
</header>