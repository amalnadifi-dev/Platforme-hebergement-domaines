<?php
require_once("auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';

$total_clients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$total_domaines = $pdo->query("SELECT COUNT(*) FROM domaines")->fetchColumn();
$total_hebergements = $pdo->query("SELECT COUNT(*) FROM hebergements")->fetchColumn();
$expirations = $pdo->query("SELECT COUNT(*) FROM domaines WHERE date_expiration BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$domaines_actifs = $pdo->query("SELECT COUNT(*) FROM domaines WHERE date_expiration > NOW()")->fetchColumn();
$derniers_clients = $pdo->query("SELECT * FROM clients ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>HostManager - Tableau de bord</title>
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
<?php else:?>
body {font-family:'Inter',sans-serif; color:#0f172a; display:flex; min-height:100vh; background:#f8fafc;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0; box-shadow:2px 0 8px rgba(0,0,0,0.05); z-index:100;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s; font-weight:500;}
.sidebar a:hover,.sidebar a.active {background:#f1f5f9; color:#0f172a;}
.box {background:rgba(255, 255, 255, 0.90); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
.stat-card {background:rgba(255, 255, 255, 0.90); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
<?php endif;?>

.sidebar a i {width:20px; margin-right:10px;}

.content {
  margin-left:240px; 
  padding:30px; 
  width:calc(100% - 240px);
  min-height:100vh;
  position:relative;
  background: 
    linear-gradient(rgba(15, 23, 42, 0.70), rgba(15, 23, 42, 0.75)),
    url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}

<?php if($theme!= 'dark'):?>
.content {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.75), rgba(241, 245, 249, 0.80)),
    url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}
<?php endif;?>

.header h1 {font-size:36px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5); font-size:16px;}

.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:28px; border-radius:16px; transition:0.3s; position:relative; overflow:hidden;}
.stat-card:hover {transform:translateY(-5px); box-shadow:0 12px 30px rgba(0,0,0,0.5);}
.stat-icon {width:64px; height:64px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:16px;}
.stat-icon.clients {background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff;}
.stat-icon.domaines {background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff;}
.stat-icon.hebergements {background:linear-gradient(135deg, #a855f7, #9333ea); color:#fff;}
.stat-icon.alertes {background:linear-gradient(135deg, #ef4444, #dc2626); color:#fff;}
.stat-info h3 {font-size:38px; font-weight:700; margin-bottom:6px;}
.stat-info p {font-size:14px; color:#64748b; font-weight:500;}

.grid-2 {display:grid; grid-template-columns:1fr 1fr; gap:20px;}
.box {padding:24px; border-radius:16px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px;}
.client-item {display:flex; align-items:center; gap:12px; padding:14px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
.client-item:hover {background:rgba(99,102,241,0.1);}
.avatar {width:40px; height:40px; border-radius:50%; background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600;}
.alert-box {padding:16px; background:rgba(239,68,68,0.15); border:1px solid #ef4444; border-radius:12px; display:flex; align-items:center; gap:12px; margin-bottom:20px;}
.alert-box i {font-size:24px; color:#ef4444;}

@media (max-width: 968px){
.grid-2 {grid-template-columns:1fr;}
}
</style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="tableau_bord.php" class="active"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="../clients"><i class="fa fa-users"></i> Clients</a>
  <a href="../domaines"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergement"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../paiements"><i class="fa fa-credit-card"></i> Paiements</a>
  <a href="../notifications"><i class="fa fa-bell"></i> Alertes</a>
  <a href="deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <div class="header">
    <p>Centre de contrôle - Gérez votre infrastructure d'hébergement</p>
  </div>

  <?php if($expirations > 0):?>
  <div class="alert-box">
    <i class="fa fa-exclamation-triangle"></i>
    <div>
      <strong>Attention!</strong> <?= $expirations?> domaine(s) expirent dans moins de 30 jours.
    </div>
  </div>
  <?php endif;?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon clients"><i class="fa fa-users"></i></div>
      <div class="stat-info">
        <h3><?= $total_clients?></h3>
        <p>Nombre total de clients</p>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon domaines"><i class="fa fa-globe"></i></div>
      <div class="stat-info">
        <h3><?= $domaines_actifs?></h3>
        <p>Domaines Actifs</p>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon hebergements"><i class="fa fa-server"></i></div>
      <div class="stat-info">
        <h3><?= $total_hebergements?></h3>
        <p>Hébergements</p>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon alertes"><i class="fa fa-bell"></i></div>
      <div class="stat-info">
        <h3><?= $expirations?></h3>
        <p>Expiration 30j</p>
      </div>
    </div>
  </div>

  <div class="grid-2">
    <div class="box">
      <h2><i class="fa fa-user-plus"></i> Derniers Clients</h2>
      <?php if(empty($derniers_clients)):?>
        <div style="padding:40px; text-align:center; color:#64748b;">
          <i class="fa fa-user-slash" style="font-size:36px; margin-bottom:12px; opacity:0.5;"></i><br>
          Aucun client pour le moment
        </div>
      <?php else:?>
        <?php foreach($derniers_clients as $c): 
          $nom_parts = explode(' ', trim($c['full_name']));
          $initials = strtoupper(substr($nom_parts[0], 0, 1). (isset($nom_parts[1])? substr($nom_parts[1], 0, 1) : ''));
?>
        <div class="client-item">
          <div class="avatar"><?= $initials?></div>
          <div>
            <strong><?= htmlspecialchars($c['full_name'])?></strong><br>
            <small style="color:#64748b;"><?= htmlspecialchars($c['email'])?></small>
          </div>
        </div>
        <?php endforeach;?>
      <?php endif;?>
    </div>

    <div class="box">
      <h2><i class="fa fa-chart-line"></i> Vue d'ensemble</h2>
      <div style="padding:20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #334155;">
          <span style="color:#94a3b8;">Clients actifs</span>
          <strong><?= $total_clients?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #334155;">
          <span style="color:#94a3b8;">Domaines enregistrés</span>
          <strong><?= $total_domaines?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #334155;">
          <span style="color:#94a3b8;">Hébergements actifs</span>
          <strong><?= $total_hebergements?></strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#94a3b8;">Domaines actifs</span>
          <strong style="color:#10b981;"><?= $domaines_actifs?></strong>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>