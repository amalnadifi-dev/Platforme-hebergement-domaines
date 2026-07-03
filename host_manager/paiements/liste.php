<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';


if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM paiements WHERE id =?");
    $stmt->execute([$id]);
    header("Location: liste.php?msg=deleted");
    exit;
}


$stmt = $pdo->query("
    SELECT p.*, c.full_name, c.email 
    FROM paiements p 
    LEFT JOIN clients c ON p.id_client = c.id 
    ORDER BY p.id DESC
");
$paiements = $stmt->fetchAll();

$total_revenus = array_sum(array_column($paiements, 'montant'));

$debut_mois = date('Y-m-01');
$fin_mois = date('Y-m-t');

$revenus_mois = 0;
$paiements_mois = 0;
foreach($paiements as $p){
    if($p['date_paiement'] >= $debut_mois && $p['date_paiement'] <= $fin_mois){
        $revenus_mois += $p['montant'];
        $paiements_mois++;
    }
}

$revenus_attente = array_sum(array_map(function($p) { 
    return $p['statut'] == 'en_attente' ? $p['montant'] : 0; 
}, $paiements));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Paiements - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}

<?php if($theme == 'dark'):?>
body {font-family:'Inter',sans-serif; color:#e2e8f0; display:flex; min-height:100vh; background:#0f172a;}
.sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155; z-index:100;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px;}
.sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
.sidebar a:hover,.sidebar a.active {background:#334155; color:#fff;}
.box {background:rgba(30, 41, 59, 0.90); backdrop-filter:blur(10px); border:1px solid #334155;}
.stat-card {background:rgba(30, 41, 59, 0.90); backdrop-filter:blur(10px); border:1px solid #334155;}
th {color:#94a3b8;}
th, td {border-bottom:1px solid #334155;}
<?php else:?>
body {font-family:'Inter',sans-serif; color:#0f172a; display:flex; min-height:100vh; background:#f8fafc;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0; box-shadow:2px 0 8px rgba(0,0,0,0.05); z-index:100;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s; font-weight:500;}
.sidebar a:hover,.sidebar a.active {background:#f1f5f9; color:#0f172a;}
.box {background:rgba(255, 255, 255, 0.90); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
.stat-card {background:rgba(255, 255, 255, 0.90); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
th {color:#64748b;}
td {color:#334155;}
th, td {border-bottom:1px solid #f1f5f9;}
<?php endif;?>

.sidebar a i {width:20px; margin-right:10px;}

.content {
  margin-left:240px; 
  padding:30px; 
  width:calc(100% - 240px);
  min-height:100vh;
  position:relative;
  background: 
    linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.70)),
    url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}

<?php if($theme!= 'dark'):?>
.content {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.70), rgba(241, 245, 249, 0.75)),
    url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}
<?php endif;?>

.header h1 {font-size:28px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5);}

.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:24px; border-radius:12px; display:flex; align-items:center; gap:16px; transition:0.2s;}
.stat-card:hover {transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.4);}
.stat-icon {width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px;}
.stat-icon.total {background:linear-gradient(135deg, #10b981, #059669); color:#fff;}
.stat-icon.mois {background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff;}
.stat-icon.nb {background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff;}
.stat-icon.attente {background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff;}
.stat-info h3 {font-size:32px; font-weight:700; margin-bottom:4px;}
.stat-info p {font-size:14px; color:#64748b;}

.box {padding:24px; border-radius:12px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; justify-content:space-between;}
.btn-add {background:#10b981; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; transition:0.2s;}
.btn-add:hover {background:#059669; transform:translateY(-1px);}
table {width:100%; border-collapse:collapse;}
th, td {padding:14px; text-align:left; font-size:14px;}
th {font-weight:600; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;}
.badge {padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;}
.badge-payé {background:#dcfce7; color:#16a34a;}
.badge-en_attente {background:#fef3c7; color:#d97706;}
.badge-annulé {background:#fee2e2; color:#dc2626;}
.actions {display:flex; gap:8px;}
.btn-action {padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:12px; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;}
.btn-view {background:#dbeafe; color:#3b82f6;}
.btn-view:hover {background:#bfdbfe;}
.btn-edit {background:#fef3c7; color:#d97706;}
.btn-edit:hover {background:#fde68a;}
.btn-delete {background:#fee2e2; color:#dc2626;}
.btn-delete:hover {background:#fecaca;}
.empty {text-align:center; padding:60px; color:#64748b;}
.empty i {font-size:48px; margin-bottom:16px; opacity:0.5;}
tr:hover {background:rgba(16,185,129,0.08);}
.montant {font-size:16px; font-weight:700; color:#10b981;}
.alert {padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;}
.alert-success {background:#dcfce7; color:#16a34a; border:1px solid #86efac;}
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
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'):?>
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> Paiement supprimé avec succès
    </div>
  <?php endif;?>

  <div class="header">
    <h1><i class="fa fa-credit-card"></i> Gestion des Paiements</h1>
    <p>Suivi des factures et transactions</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon total"><i class="fa fa-euro-sign"></i></div>
      <div class="stat-info">
        <h3><?= number_format($total_revenus, 2)?>€</h3>
        <p>Revenus Total</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon mois"><i class="fa fa-calendar"></i></div>
      <div class="stat-info">
        <h3><?= number_format($revenus_mois, 2)?>€</h3>
        <p>Ce Mois</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon nb"><i class="fa fa-receipt"></i></div>
      <div class="stat-info">
        <h3><?= $paiements_mois?></h3>
        <p>Paiements ce mois</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon attente"><i class="fa fa-clock"></i></div>
      <div class="stat-info">
        <h3><?= number_format($revenus_attente, 2)?>€</h3>
        <p>En Attente</p>
      </div>
    </div>
  </div>

  <div class="box">
    <h2>
      <span><i class="fa fa-list"></i> Historique des Paiements</span>
      <a href="ajouter.php" class="btn-add"><i class="fa fa-plus"></i> Nouveau Paiement</a>
    </h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Méthode</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($paiements)):?>
          <tr><td colspan="7" class="empty">
            <i class="fa fa-receipt"></i><br>
            Aucun paiement enregistré<br>
            <small>Les paiements apparaîtront ici</small>
          </td></tr>
        <?php else:?>
          <?php foreach($paiements as $p): 
            $statut_class = 'badge-'. str_replace('_', '_', $p['statut']);
        ?>
          <tr>
            <td><strong>#<?= $p['id']?></strong></td>
            <td>
              <?= htmlspecialchars($p['full_name']?? 'Client supprimé')?><br>
              <small style="color:#64748b;"><?= htmlspecialchars($p['email']?? '')?></small>
            </td>
            <td class="montant"><?= number_format($p['montant'], 2)?>€</td>
            <td><?= ucfirst($p['methode'])?></td>
            <td><?= date('d/m/Y', strtotime($p['date_paiement']))?></td>
            <td><span class="badge <?= $statut_class?>"><?= ucfirst(str_replace('_', ' ', $p['statut']))?></span></td>
            <td class="actions">
              <a href="facture.php?id=<?= $p['id']?>" class="btn-action btn-view" title="Facture"><i class="fa fa-file-pdf"></i></a>
              <a href="modifier.php?id=<?= $p['id']?>" class="btn-action btn-edit" title="Modifier"><i class="fa fa-pen"></i></a>
              <a href="liste.php?delete=<?= $p['id']?>" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce paiement ?')"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach;?>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>