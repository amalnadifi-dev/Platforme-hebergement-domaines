<?php
require_once("../adminitration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'] ?? 0;
$p = $pdo->query("
    SELECT p.*, c.full_name, c.email, c.phone, c.address 
    FROM paiements p 
    JOIN clients c ON p.id_client = c.id 
    WHERE p.id = $id
")->fetch();

if(!$p){ die("Paiement introuvable"); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Paiement #<?= $p['id'] ?></title>
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
        .box {background:#1e293b; padding:30px; border-radius:16px; border:1px solid #334155;}
        .header {display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;}
        .header h1 {font-size:28px; font-weight:700;}
        .btn {padding:10px 20px; border-radius:8px; text-decoration:none; border:none; cursor:pointer; font-weight:500; display:inline-flex; align-items:center; gap:8px;}
        .btn-dark {background:#334155; color:#fff;}
        .btn-dark:hover {background:#475569;}
        .detail-grid {display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;}
        .detail-item {background:#0f172a; padding:20px; border-radius:12px; border:1px solid #334155;}
        .detail-item label {color:#94a3b8; font-size:13px; font-weight:600; text-transform:uppercase; display:block; margin-bottom:8px;}
        .detail-item p {font-size:18px; font-weight:600; color:#fff;}
        .badge {padding:6px 14px; border-radius:6px; font-size:13px; font-weight:600;}
        .badge-success {background:#10b981; color:#fff;}
        .badge-warning {background:#f59e0b; color:#fff;}
        @media (max-width: 768px){ .detail-grid {grid-template-columns:1fr;} }
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
        <h1><i class="fa fa-receipt"></i> Détails Paiement #<?= $p['id'] ?></h1>
        <a href="liste.php" class="btn btn-dark"><i class="fa fa-arrow-left"></i> Retour</a>
    </div>

    <div class="box">
        <h2 style="margin-bottom:20px; color:#6366f1;"><i class="fa fa-info-circle"></i> Informations du Paiement</h2>
        
        <div class="detail-grid">
            <div class="detail-item">
                <label>Client</label>
                <p><?= htmlspecialchars($p['full_name']) ?></p>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <p><?= htmlspecialchars($p['email']) ?></p>
            </div>
            <div class="detail-item">
                <label>Téléphone</label>
                <p><?= htmlspecialchars($p['phone']) ?></p>
            </div>
            <div class="detail-item">
                <label>Adresse</label>
                <p><?= htmlspecialchars($p['address']) ?></p>
            </div>
            <div class="detail-item">
                <label>Montant</label>
                <p style="color:#10b981;"><?= number_format($p['montant'], 2) ?> €</p>
            </div>
            <div class="detail-item">
                <label>Méthode de Paiement</label>
                <p><?= htmlspecialchars($p['methode']) ?></p>
            </div>
            <div class="detail-item">
                <label>Date de Paiement</label>
                <p><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></p>
            </div>
            <div class="detail-item">
                <label>Statut</label>
                <p>
                    <?php if($p['statut'] == 'payé'): ?>
                        <span class="badge badge-success">Payé</span>
                    <?php else: ?>
                        <span class="badge badge-warning">En attente</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <label>Description</label>
                <p><?= htmlspecialchars($p['description'] ?? 'Aucune description') ?></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>