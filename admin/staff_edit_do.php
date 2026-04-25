<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    if (!empty($_POST["id"]) && !empty($_POST["staff_id"]) && !empty($_POST["last_name"]) && !empty($_POST["first_name"])) {
        $id = (int)$_POST["id"];
        $staff_id = $_POST["staff_id"];
        $last_name = $_POST["last_name"];
        $first_name = $_POST["first_name"];
        $password = $_POST["password"];
        $pw_hash = "";

        if (!empty($password)) {
            if (check_preg_password($password)) {
                $pw_hash = password_hash($password, PASSWORD_DEFAULT);
            } else {
                header("location: staff_edit.php?id=" . $id);
                exit();
            }
        }

        try {
            $db = db_connect();
            $sql = empty($pw_hash) ?
                "UPDATE m_admin_staffs SET staff_id=:staff_id,last_name=:last_name,first_name=:first_name WHERE id=:id" :
                "UPDATE m_admin_staffs SET staff_id=:staff_id,last_name=:last_name,first_name=:first_name,password=:password WHERE id=:id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":staff_id", $staff_id, PDO::PARAM_STR);
            $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            if (!empty($pw_hash)) {
                $stmt->bindParam(":password", $pw_hash, PDO::PARAM_STR);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}

header("location: staff.php");
exit();
