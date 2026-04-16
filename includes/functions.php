<?php
require_once __DIR__ . '/config.php';

// DB接続関数
function db_connect()
{
    try {
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=utf8mb4';
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        exit('DB接続エラー: ' . $e->getMessage());
    }
}

// XSS対策用のエスケープ関数
function h($string)
{
    if (is_null($string)) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


// 日付のフォーマット用関数
function format_date($datetime, $type)
{
    $format_types = [
        1 => 'Y年m月d日 H:i:s',
        2 => 'Y年m月',
        3 => 'Y年m月d日',
    ];
    return date($format_types[$type], strtotime($datetime));
}

//教室IDから教室名を返す関数
function get_classrooms_list()
{
    $room = array();
    try {
        //m_classroomsテーブルから全レコードを取得
        $db = db_connect();
        $sql = 'SELECT * FROM m_classrooms';
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $room[$row['id']] = $row['name'];
        }
        return $room;
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}


//コース種別IDからコース種別名を返す関数
function get_course_types_list()
{
    $course_types = array();
    try {
        //m_course_typesテーブルから全レコードを取得
        $db = db_connect();
        $sql = 'SELECT * FROM m_course_types';
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $course_types[$row['id']] = $row['name'];
        }
        return $course_types;
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}

//在籍ステータスIDから在籍ステータス名を返す関数
function get_enrollments_list()
{
    $enrollments_types = array();
    try {
        //m_course_typesテーブルから全レコードを取得
        $db = db_connect();
        $sql = 'SELECT * FROM m_enrollments';
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $enrollments_types[$row['id']] = $row['name'];
        }
        return $enrollments_types;
    } catch (PDOException $e) {
        exit('エラー: ' . $e->getMessage());
    }
}

//slot_indexから時間枠を返す関数
function get_slot_list()
{
    $slot_time = [
        0 => '10:00～',
        1 => '11:00～',
        2 => '12:00～',
        3 => '14:00～',
        4 => '15:00～',
        5 => '16:00～',
    ];
    return $slot_time;
}
