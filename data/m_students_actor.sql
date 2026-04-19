UPDATE `m_students`
SET 
    `last_name` = CASE `id`
        WHEN 1 THEN '阿部' WHEN 2 THEN '堺' WHEN 3 THEN '佐藤' WHEN 4 THEN '菅田' WHEN 5 THEN '山崎'
        WHEN 6 THEN '横浜' WHEN 7 THEN '吉沢' WHEN 8 THEN '小栗' WHEN 9 THEN '神木' WHEN 10 THEN '竹内'
        WHEN 11 THEN '綾瀬' WHEN 12 THEN '新垣' WHEN 13 THEN '長澤' WHEN 14 THEN '北川' WHEN 15 THEN '石原'
        WHEN 16 THEN '有村' WHEN 17 THEN '広瀬' WHEN 18 THEN '浜辺' WHEN 19 THEN '今田' WHEN 20 THEN '小松'
        WHEN 21 THEN '橋本' WHEN 22 THEN '永野' WHEN 23 THEN '川口' WHEN 24 THEN '戸田' WHEN 25 THEN '高畑'
        ELSE `last_name`
    END,
    `first_name` = CASE `id`
        WHEN 1 THEN '寛' WHEN 2 THEN '雅人' WHEN 3 THEN '健' WHEN 4 THEN '将暉' WHEN 5 THEN '賢人'
        WHEN 6 THEN '流星' WHEN 7 THEN '亮' WHEN 8 THEN '旬' WHEN 9 THEN '隆之介' WHEN 10 THEN '涼真'
        WHEN 11 THEN 'はるか' WHEN 12 THEN '結衣' WHEN 13 THEN 'まさみ' WHEN 14 THEN '景子' WHEN 15 THEN 'さとみ'
        WHEN 16 THEN '架純' WHEN 17 THEN 'すず' WHEN 18 THEN '美波' WHEN 19 THEN '美桜' WHEN 20 THEN '菜奈'
        WHEN 21 THEN '環奈' WHEN 22 THEN '芽郁' WHEN 23 THEN '春奈' WHEN 24 THEN '恵梨香' WHEN 25 THEN '充希'
        ELSE `first_name`
    END,
    `updated_at` = NOW()
WHERE `id` BETWEEN 1 AND 25;
