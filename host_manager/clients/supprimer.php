<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
$stmt->execute([$id]);
header("Location: liste.php?deleted=1");
exit();
?>