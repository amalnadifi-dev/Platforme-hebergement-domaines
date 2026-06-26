<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'] ?? 0;

if($id){
    try {
        $stmt = $pdo->prepare("DELETE FROM domaines WHERE id = ?");
        $stmt->execute([$id]);
    } catch(PDOException $e) {
    }
}

header("Location: liste.php?deleted=1");
exit();
?>