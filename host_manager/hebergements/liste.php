<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';

if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM hebergements WHERE id =?");
    $stmt->execute([$id]);
    header("Location: liste.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("
    SELECT h.*, c.full_name 
    FROM hebergements h 
    JOIN clients c ON h.id_client = c.id 
    ORDER BY h.date_expiration ASC
");
$hebergements = $stmt->fetchAll();

$total_hebergements = count($hebergements);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Hébergements - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}

<?php if($theme == 'dark'):?>
body {font-family:'Inter',sans-serif; color:#e2e8f0; display:flex; min-height:100vh; background:#0f172a;}
.sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155; z-index:100;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700; display:flex; align-items:center; gap:10px;}
.sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
.sidebar a:hover, .sidebar a.active {background:#334155; color:#fff;}
.box {background:rgba(30, 41, 59, 0.90); backdrop-filter:blur(10px); border:1px solid #334155;}
.stat-card {background:rgba(30, 41, 59, 0.90); backdrop-filter:blur(10px); border:1px solid #334155;}
th {color:#94a3b8;}
th, td {border-bottom:1px solid #334155;}
<?php else:?>
body {font-family:'Inter',sans-serif; color:#0f172a; display:flex; min-height:100vh; background:#f8fafc;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0; box-shadow:2px 0 8px rgba(0,0,0,0.05); z-index:100;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700; display:flex; align-items:center; gap:10px;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s; font-weight:500;}
.sidebar a:hover, .sidebar a.active {background:#f1f5f9; color:#0f172a;}
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
    linear-gradient(rgba(15, 23, 42, 0.60), rgba(15, 23, 42, 0.65)),
    url('https://images.unsplash.com/photo-1601597111158-2fceff292cdc?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}

<?php if($theme!= 'dark'):?>
.content {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.65), rgba(241, 245, 249, 0.70)),
    url('https://images.unsplash.com/photo-1601597111158-2fceff292cdc?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}
<?php endif;?>

.header h1 {font-size:28px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5);}

.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:24px; border-radius:12px; display:flex; align-items:center; gap:16px; transition:0.2s;}
.stat-card:hover {transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.4);}
.stat-icon {width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px;}
.stat-icon.total {background:#dbeafe; color:#3b82f6;}
.stat-icon.active {background:#dcfce7; color:#16a34a;}
.stat-icon.expire {background:#fee2e2; color:#dc2626;}
.stat-info h3 {font-size:32px; font-weight:700; margin-bottom:4px;}
.stat-info p {font-size:14px; color:#64748b;}

.box {padding:24px; border-radius:12px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; justify-content:space-between;}
.btn-add {background:#6366f1; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; transition:0.2s;}
.btn-add:hover {background:#4f46e5; transform:translateY(-1px);}
table {width:100%; border-collapse:collapse;}
th, td {padding:14px; text-align:left; font-size:14px;}
th {font-weight:600; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;}
tr:hover {background:rgba(99,102,241,0.08);}
.badge {padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;}
.badge-actif {background:#d1fae5; color:#065f46;}
.badge-expire {background:#fee2e2; color:#991b1b;}
.badge-suspendu {background:#fef3c7; color:#92400e;}
.btn-action {padding:6px 10px; border-radius:6px; text-decoration:none; font-size:13px; margin-right:5px; transition:0.2s;}
.btn-edit {background:#3b82f6; color:#fff;}
.btn-edit:hover {background:#2563eb;}
.btn-delete {background:#ef4444; color:#fff;}
.btn-delete:hover {background:#dc2626;}
.empty {text-align:center; padding:60px; color:#64748b;}
.server-icon {width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg, #6366f1, #4f46e5); color:#fff; display:inline-flex; align-items:center; justify-content:center; margin-right:10px; font-size:16px;}
.progress-bar {width:100%; height:6px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-top:4px;}
.progress-fill {height:100%; background:linear-gradient(90deg, #6366f1, #8b5cf6); border-radius:10px;}
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
  <a href="liste.php" class="active"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../paiements/liste.php"><i class="fa fa-credit-card"></i> Paiements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'):?>
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> Hébergement supprimé avec succès
    </div>
  <?php endif;?>

  <div class="header">
    <h1><i class="fa fa-server"></i> Hébergements</h1>
    <p>Gérez vos plans d'hébergement et serveurs</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon total"><i class="fa fa-server"></i></div>
      <div class="stat-info">
        <h3><?= $total_hebergements?></h3>
        <p>Total Hébergements</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon active"><i class="fa fa-check-circle"></i></div>
      <div class="stat-info">
        <h3><?= count(array_filter($hebergements, fn($h) => $h['statut'] == 'actif'))?></h3>
        <p>Actifs</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon expire"><i class="fa fa-exclamation-triangle"></i></div>
      <div class="stat-info">
        <h3><?= count(array_filter($hebergements, fn($h) => $h['statut'] == 'expire'))?></h3>
        <p>Expirés</p>
      </div>
    </div>
  </div>

  <div class="box">
    <h2>
      <span><i class="fa fa-list"></i> Liste des Hébergements</span>
      <a href="ajouter.php" class="btn-add"><i class="fa fa-plus"></i> Nouvel Hébergement</a>
    </h2>
    <table>
      <thead>
        <tr>
          <th>Plan</th>
          <th>Client</th>
          <th>Espace Disque</th>
          <th>Bande Passante</th>
          <th>Date Expiration</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($hebergements)):?>
          <tr><td colspan="7" class="empty">
            <i class="fa fa-server" style="font-size:48px; margin-bottom:16px; opacity:0.5;"></i><br>
            Aucun hébergement enregistré<br>
            <small>Commencez par ajouter votre premier plan</small>
          </td></tr>
        <?php else:?>
          <?php foreach($hebergements as $h): 
            $jours = floor((strtotime($h['date_expiration']) - time()) / 86400);
            $espace_percent = min(($h['espace'] / 10000) * 100, 100);
            $bp_percent = min(($h['bande_passante'] / 100000) * 100, 100);
         ?>
          <tr>
            <td>
              <span class="server-icon"><i class="fa fa-server"></i></span>
              <strong><?= htmlspecialchars($h['plan'])?></strong>
            </td>
            <td><?= htmlspecialchars($h['full_name'])?></td>
            <td>
              <strong><?= number_format($h['espace'])?> MB</strong>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $espace_percent?>%"></div>
              </div>
            </td>
            <td>
              <strong><?= number_format($h['bande_passante'])?> MB</strong>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $bp_percent?>%"></div>
              </div>
            </td>
            <td>
              <?= date('d/m/Y', strtotime($h['date_expiration']))?><br>
              <small style="color:#64748b;"><?= $jours?> jours</small>
            </td>
            <td>
              <span class="badge badge-<?= $h['statut']?>">
                <?= ucfirst($h['statut'])?>
              </span>
            </td>
            <td>
              <a href="modifier.php?id=<?= $h['id']?>" class="btn-action btn-edit"><i class="fa fa-pen"></i></a>
              <a href="liste.php?delete=<?= $h['id']?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet hébergement ?')"><i class="fa fa-trash"></i></a>
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