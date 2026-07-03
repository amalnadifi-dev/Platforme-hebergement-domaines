<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'] ?? 0;

if($_POST){
    $stmt = $pdo->prepare("UPDATE paiements SET montant=?, methode=?, statut=?, description=? WHERE id=?");
    $stmt->execute([$_POST['montant'], $_POST['methode'], $_POST['statut'], $_POST['description'], $id]);
    header("Location: liste.php");
    exit();
}

$p = $pdo->query("SELECT p.*, c.full_name FROM paiements p JOIN clients c ON p.id_client = c.id WHERE p.id = $id")->fetch();
if(!$p){ die("Paiement introuvable"); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Paiement #<?= $p['id'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {margin:0; padding:0; box-sizing:border-box;}
        body {font-family:'Inter',sans-serif; color:#e2e8f0; display:flex; min-height:100vh; background:#0f172a;}
        .sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155;}
        .sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px;}
        .sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
        .sidebar a:hover,.sidebar a.active {background:#334155; color:#fff;}
        .sidebar a i {width:20px; margin-right:10px;}
        .content {margin-left:240px; padding:30px; width:calc(100% - 240px);}
        .box {background:#1e293b; padding:30px; border-radius:16px; border:1px solid #334155; max-width:700px;}
        .header {display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;}
        .header h1 {font-size:28px; font-weight:700;}
        .btn {padding:10px 20px; border-radius:8px; text-decoration:none; border:none; cursor:pointer; font-weight:500; display:inline-flex; align-items:center; gap:8px;}
        .btn-dark {background:#334155; color:#fff;}
        .btn-success {background:#10b981; color:#fff;}
        .btn-dark:hover {background:#475569;}
        .btn-success:hover {background:#059669;}
        .form-group {margin-bottom:20px;}
        .form-group label {display:block; color:#cbd5e1; font-weight:600; margin-bottom:8px; font-size:14px;}
        .form-group input, .form-group select, .form-group textarea {
            width:100%; padding:12px; background:#0f172a; border:1px solid #334155; 
            border-radius:8px; color:#e2e8f0; font-family:'Inter',sans-serif; font-size:14px;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline:none; border-color:#6366f1;
        }
        .form-actions {display:flex; gap:12px; margin-top:30px;}
    </style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="../administration/tableau_bord.php"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="../clients/liste.php"><i class="fa fa-users"></i> Clients</a>
  <a href="../domaines/liste.php"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="liste.php" class="active"><i class="fa fa-credit-card"></i> Paiements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
    <div class="header">
        <h1><i class="fa fa-pen"></i> Modifier Paiement #<?= $p['id'] ?></h1>
        <a href="liste.php" class="btn btn-dark"><i class="fa fa-arrow-left"></i> Retour</a>
    </div>

    <div class="box">
        <form method="POST">
            <div class="form-group">
                <label><i class="fa fa-user"></i> Client</label>
                <input type="text" value="<?= htmlspecialchars($p['full_name']) ?>" disabled>
            </div>

            <div class="form-group">
                <label><i class="fa fa-euro-sign"></i> Montant</label>
                <input type="number" step="0.01" name="montant" value="<?= $p['montant'] ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa fa-credit-card"></i> Méthode de Paiement</label>
                <select name="methode" required>
                    <option value="Carte" <?= $p['methode']=='Carte'?'selected':'' ?>>Carte Bancaire</option>
                    <option value="Virement" <?= $p['methode']=='Virement'?'selected':'' ?>>Virement</option>
                    <option value="PayPal" <?= $p['methode']=='PayPal'?'selected':'' ?>>PayPal</option>
                    <option value="Espèces" <?= $p['methode']=='Espèces'?'selected':'' ?>>Espèces</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa fa-check-circle"></i> Statut</label>
                <select name="statut" required>
                    <option value="payé" <?= $p['statut']=='payé'?'selected':'' ?>>Payé</option>
                    <option value="en attente" <?= $p['statut']=='en attente'?'selected':'' ?>>En attente</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa fa-comment"></i> Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Enregistrer</button>
                <a href="liste.php" class="btn btn-dark"><i class="fa fa-times"></i> Annuler</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>