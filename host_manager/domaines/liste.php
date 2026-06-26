<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';


$stmt = $pdo->query("
    SELECT d.*, c.full_name, c.email
    FROM domaines d 
    LEFT JOIN clients c ON d.id_client = c.id 
    ORDER BY d.date_expiration ASC
");
$domaines = $stmt->fetchAll();

$total_domaines = count($domaines);
$domaines_actifs = count(array_filter($domaines, function($d){ 
    return strtotime($d['date_expiration']) > time(); 
}));
$domaines_expire = count(array_filter($domaines, function($d){ 
    return strtotime($d['date_expiration']) <= time(); 
}));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Domaines - HostManager</title>
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
    url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop') center/cover fixed no-repeat;
}

<?php if($theme!= 'dark'):?>
.content {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.70), rgba(241, 245, 249, 0.75)),
    url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop') center/cover fixed no-repeat;
}
<?php endif;?>

.header h1 {font-size:28px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5);}
.alert {padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-weight:500;}
.alert-success {background:#dcfce7; color:#16a34a; border:1px solid #86efac;}
.alert-error {background:#fee2e2; color:#dc2626; border:1px solid #fca5a5;}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:24px; border-radius:12px; display:flex; align-items:center; gap:16px; transition:0.2s;}
.stat-card:hover {transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.4);}
.stat-icon {width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px;}
.stat-icon.total {background:#dbeafe; color:#3b82f6;}
.stat-icon.actif {background:#dcfce7; color:#16a34a;}
.stat-icon.expire {background:#fee2e2; color:#dc2626;}
.stat-info h3 {font-size:32px; font-weight:700; margin-bottom:4px;}
.stat-info p {font-size:14px; color:#64748b;}
.box {padding:24px; border-radius:12px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; justify-content:space-between;}
.btn-add {background:#6366f1; color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:500; transition:0.2s;}
.btn-add:hover {background:#4f46e5;}
table {width:100%; border-collapse:collapse;}
th, td {padding:14px; text-align:left; font-size:14px;}
th {font-weight:600; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;}
.badge {padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;}
.badge-active {background:#dcfce7; color:#16a34a;}
.badge-expire {background:#fee2e2; color:#dc2626;}
.badge-warning {background:#fef3c7; color:#d97706;}
.actions {display:flex; gap:8px;}
.btn-action {padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:12px; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;}
.btn-edit {background:#fef3c7; color:#d97706;}
.btn-edit:hover {background:#fde68a;}
.btn-delete {background:#fee2e2; color:#dc2626;}
.btn-delete:hover {background:#fecaca;}
.empty {text-align:center; padding:60px; color:#64748b;}
.empty i {font-size:48px; margin-bottom:16px; opacity:0.5;}
tr:hover {background:rgba(99,102,241,0.08);}
</style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="../administration/tableau_bord.php"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="../clients/liste.php"><i class="fa fa-users"></i> Clients</a>
  <a href="liste.php" class="active"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../paiements/liste.php"><i class="fa fa-credit-card"></i> Paiements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <div class="header">
    <h1><i class="fa fa-globe"></i> Domaines</h1>
    <p>Gérez tous vos noms de domaine</p>
  </div>

  <?php if(isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> Domaine supprimé avec succès!
    </div>
  <?php elseif(isset($_GET['deleted']) && $_GET['deleted'] == 0): ?>
    <div class="alert alert-error">
      <i class="fa fa-exclamation-circle"></i> Domaine introuvable!
    </div>
  <?php elseif(isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <i class="fa fa-exclamation-circle"></i> Erreur lors de la suppression!
    </div>
  <?php endif; ?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon total"><i class="fa fa-globe"></i></div>
      <div class="stat-info">
        <h3><?= $total_domaines?></h3>
        <p>Total Domaines</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon actif"><i class="fa fa-check-circle"></i></div>
      <div class="stat-info">
        <h3><?= $domaines_actifs?></h3>
        <p>Domaines Actifs</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon expire"><i class="fa fa-exclamation-triangle"></i></div>
      <div class="stat-info">
        <h3><?= $domaines_expire?></h3>
        <p>Expirés</p>
      </div>
    </div>
  </div>

  <div class="box">
    <h2>
      <span><i class="fa fa-list"></i> Liste des Domaines</span>
      <a href="ajouter.php" class="btn-add"><i class="fa fa-plus"></i> Nouveau Domaine</a>
    </h2>
    <table>
      <thead>
        <tr>
          <th>Domaine</th>
          <th>Client</th>
          <th>Date Expiration</th>
          <th>Jours Restants</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($domaines)):?>
          <tr><td colspan="6" class="empty">
            <i class="fa fa-globe"></i><br>
            Aucun domaine pour le moment<br>
            <small>Commencez par ajouter votre premier domaine</small>
          </td></tr>
        <?php else:?>
          <?php foreach($domaines as $d): 
            $jours = floor((strtotime($d['date_expiration']) - time()) / 86400);
            if($jours < 0) {
                $statut_class = 'badge-expire';
                $statut_text = 'Expiré';
            } elseif($jours <= 30) {
                $statut_class = 'badge-warning';
                $statut_text = 'Expire bientôt';
            } else {
                $statut_class = 'badge-active';
                $statut_text = 'Actif';
            }
        ?>
          <tr>
            <td><strong><?= htmlspecialchars($d['nom_domaine'])?></strong></td>
            <td><?= htmlspecialchars($d['full_name']?? 'N/A')?></td>
            <td><?= date('d/m/Y', strtotime($d['date_expiration']))?></td>
            <td><?= $jours?> jours</td>
            <td><span class="badge <?= $statut_class?>"><?= $statut_text?></span></td>
            <td class="actions">
              <a href="modifier.php?id=<?= $d['id']?>" class="btn-action btn-edit" title="Modifier"><i class="fa fa-pen"></i></a>
              <a href="supprimer.php?id=<?= $d['id']?>" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce domaine ?')"><i class="fa fa-trash"></i></a>
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