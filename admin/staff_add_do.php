<?php

require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    if (!empty($_POST["password"]) && !empty($_POST["staff_id"]) && !empty($_POST["last_name"]) && !empty($_POST["first_name"])) {
        $staff_id = $_POST["staff_id"];
        $last_name = $_POST["last_name"];
        $first_name = $_POST["first_name"];
        $password = $_POST["password"];
        $pw_hash = "";

        if (check_preg_password($password)) {
            $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        } else {
            header("location: staff_add.php");
            exit();
        }

        try {
            $db = db_connect();
            $sql = "INSERT INTO m_admin_staffs (staff_id, last_name, first_name, password, is_deleted) VALUES (:staff_id, :last_name, :first_name, :password, :is_deleted)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":staff_id", $staff_id, PDO::PARAM_STR);
            $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            $stmt->bindParam(":password", $pw_hash, PDO::PARAM_STR);
            $stmt->bindValue(":is_deleted", 0, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}

header("location: staff.php");
exit();
