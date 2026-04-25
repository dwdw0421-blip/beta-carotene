<?php
try {
    $line_sql = "SELECT id, date FROM carcon_lines WHERE date >= CURDATE()";
    $stmt = $db->prepare($line_sql);
    $stmt->execute();
    $line_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cell_sql = "SELECT
                    carcon_lines.id AS line_id,
                    carcon_lines.date AS date,
                    carcon_reservation_details.slot_index AS slot_index
                FROM carcon_reservations 
                INNER JOIN carcon_lines ON carcon_lines.id = carcon_reservations.carcon_line_id
                INNER JOIN carcon_reservation_details ON carcon_reservation_details.id = carcon_reservations.carcon_reservation_detail_id
                WHERE carcon_reservations.is_deleted = 0";
    $stmt = $db->prepare($cell_sql);
    $stmt->execute();
    $cell_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result_calender = [];
    foreach ($line_result as $line) {
        $result_calender[$line["id"]] = [
            "id" => $line["id"],
            "date" => $line["date"],
            // 枠全体を初期化
            "slot_index" => [
                0 => false,
                1 => false,
                2 => false,
                3 => false,
                4 => false,
                5 => false,
            ],
        ];
        // 枠が存在しているところだけtureに変更
        foreach ($cell_result as $waku) {
            if ($waku["line_id"] === $line["id"]) {
                $result_calender[$line["id"]]["slot_index"][$waku["slot_index"]] = true;
            }
        }
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}

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

?>

<section class="user-wrapper calendar shadow rounded-4">
    <h2>
        <img class="calender-img" src="./img/calendar-title.jpg" alt="カレンダータイトル">
    </h2>

    <div class="d-flex flex-column px-1 py-5 gap-4">
        <div class="d-flex flex-row gap-3 justify-content-center">
            <a href="?y=<?= $prevDate->format('Y') ?>&m=<?= $prevDate->format('n') ?>" class="arrow">
                <span class="material-symbols-outlined">
                    arrow_back_ios
                </span>
            </a>
            <h3><?= $year ?>年 <?= $month ?>月</h3>
            <a href="?y=<?= $nextDate->format('Y') ?>&m=<?= $nextDate->format('n') ?>" class="arrow">
                <span class="material-symbols-outlined">
                    arrow_forward_ios
                </span>
            </a>
        </div>

        <table class="user-table">
            <thead class="user-table-title">
                <tr>
                    <th></th>
                    <?php foreach ($saturdays as $date): ?>
                        <th class="text-center">
                            <div class="date-label"><?= $date->format('n/j') ?></div>
                            <div class="day-label">(土)</div>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                // 時間枠の数
                $timeSlots = 6;
                for ($i = 0; $i < $timeSlots; $i++) : ?>
                    <tr class="">
                        <td><?= get_slot_time_by_index($i) ?></td>
                        <?php foreach ($saturdays as $date): ?>
                            <td class="text-center bg-white rounded-2">
                                <?php
                                // 現在の予約枠のデータを取得して、同じ日付の枠が一つでも存在する場合は
                                // 「予約可能」と表示する
                                foreach ($result_calender as $result_line):
                                    $res_date = new DateTime($result_line["date"]);
                                    $res_text = '✕<br>予約不可';
                                    // 同じ日付チェック
                                    if ($res_date->format('Y-m-d') === $date->format('Y-m-d')) {
                                        // 既に枠が埋まってないかチェック（埋まってなかったら予約可能にしてbreak）
                                        if (!$result_line["slot_index"][$i]) {
                                            $res_text = '<form action="./reserve.php" method="post">
                                                            <input type="hidden" name="day" value="' . h($date->format('Y-m-d')) . '">
                                                            <input type="hidden" name="time" value="' . h($i) . '">
                                                            <button type="submit" class="btn btn-link p-0 text-decoration-none reserve-link-button">
                                                                ○<br>予約可能
                                                            </button>
                                                        </form>';
                                            break;
                                        }
                                    }
                                ?>
                                <?php endforeach; ?>
                                <?php echo $res_text; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</section>