<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");


$id = $_GET['id'] ?? 0;
$id = (int)$id; 

if($id > 0){
    try {
        
        $check = $pdo->prepare("SELECT id FROM paiements WHERE id = ?");
        $check->execute([$id]);
        
        if($check->rowCount() > 0){
          
            $stmt = $pdo->prepare("DELETE FROM paiements WHERE id = ?");
            $stmt->execute([$id]);
            
      s
            header("Location: liste.php?msg=deleted");
            exit();
        } else {

            header("Location: liste.php?msg=notfound");
            exit();
        }
    } catch(PDOException $e) {
        
        header("Location: liste.php?msg=error");
        exit();
    }
} else
    header("Location: liste.php?msg=invalid");
    exit();
}
?>