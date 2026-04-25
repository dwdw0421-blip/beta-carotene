<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET["id"]) ? (int)$_GET["id"] : "";

if (empty($id)) {
    header("location: carcon_staff.php");
    exit();
}

try {
    $db = db_connect();
    $sql = "UPDATE m_carcon_staffs SET is_deleted=:is_deleted WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(":is_deleted", 1, PDO::PARAM_INT);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    exit($e->getMessage());
}

header("location: carcon_staff.php");
exit();
