<?php

require_once __DIR__ . '/../includes/functions.php';

if (!empty($_POST)) {
    if (!empty($_POST["id"]) && !empty($_POST["last_name"]) && !empty($_POST["first_name"])) {
        $id = (int)$_POST["id"];
        $last_name = $_POST["last_name"];
        $first_name = $_POST["first_name"];

        try {
            $db = db_connect();
            $sql = "UPDATE m_carcon_staffs SET last_name=:last_name,first_name=:first_name WHERE id=:id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}

header("location: carcon_staff.php");
exit();
