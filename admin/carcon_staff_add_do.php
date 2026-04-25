<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    if (!empty($_POST["last_name"]) && !empty($_POST["first_name"])) {
        $last_name = $_POST["last_name"];
        $first_name = $_POST["first_name"];

        try {
            $db = db_connect();
            $sql = "INSERT INTO m_carcon_staffs (last_name, first_name, is_deleted) VALUES (:last_name, :first_name, :is_deleted)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            $stmt->bindValue(":is_deleted", 0, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}

header("location: carcon_staff.php");
exit();
