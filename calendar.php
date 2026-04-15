<?php
// 1. 年月の取得（デフォルトは現在）
$year = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
$month = isset($_GET['m']) ? (int)$_GET['m'] : date('n');

// 2. 前後の月のリンク用データ
$currentDate = new DateTime("$year-$month-01");
$prevDate = (clone $currentDate)->modify('-1 month');
$nextDate = (clone $currentDate)->modify('+1 month');

// 3. その月の土曜日をすべて抽出して配列に格納
$saturdays = [];
$start = clone $currentDate;
$end = (clone $currentDate)->modify('last day of this month');

if ($start->format('w') != 6) {
    $start->modify('next saturday');
}

$interval = new DateInterval('P7D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

foreach ($period as $date) {
    if ($date->format('n') == $month) {
        $saturdays[] = $date;
    }
}

// 4. 表示する時間枠の定義
$timeSlots = [
    "10:00~",
    "11:00~",
    "12:00~",
    "14:00~",
    "15:00~",
    "16:00~"
];
?>

<section class="user-wrapper calendar">
    <h2>
        <img src="./img/calendar-title.jpg" alt="カレンダータイトル">
    </h2>

    <div class="d-flex flex-column px-3 py-5 gap-4">
        <div class="d-flex flex-row gap-3">
            <a href="?y=<?= $prevDate->format('Y') ?>&m=<?= $prevDate->format('n') ?>" class="arrow">
                < 前の月
                    </a>
                    <h3><?= $year ?>年 <?= $month ?>月</h3>
                    <a href="?y=<?= $nextDate->format('Y') ?>&m=<?= $nextDate->format('n') ?>" class="arrow">
                        次の月 >
                    </a>
        </div>

        <table class="user-table">
            <thead class="user-table-title">
                <tr>
                    <th>時間 / 日付</th>
                    <?php foreach ($saturdays as $date): ?>
                        <th class="text-center">
                            <div class="date-label"><?= $date->format('n/j') ?></div>
                            <div class="day-label">(土)</div>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($timeSlots as $slot): ?>
                    <tr class="">
                        <td><?= $slot ?></td>
                        <?php foreach ($saturdays as $date): ?>
                            <td class="text-center bg-white rounded-2">
                                -
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>