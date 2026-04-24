<header>
    <div class="py-3 text-center shadow">
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
                    <?php echo h($student_result['student_name']); ?>
                </p>
            </div>
        </div>
    </div>
</header>