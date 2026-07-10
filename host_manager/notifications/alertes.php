<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';

// Domaines li 9erbo ysaliw fi 30 jours wla salaw
$stmt_domaines = $pdo->query("
    SELECT d.*, c.full_name, 'domaine' as type,
    DATEDIFF(d.date_expiration, CURDATE()) as jours_restants
    FROM domaines d 
    JOIN clients c ON d.id_client = c.id 
    WHERE DATEDIFF(d.date_expiration, CURDATE()) <= 30
    ORDER BY d.date_expiration ASC
");
$domaines_alertes = $stmt_domaines->fetchAll();

// Hébergements li 9erbo ysaliw fi 30 jours wla salaw
$stmt_hebergements = $pdo->query("
    SELECT h.*, c.full_name, 'hebergement' as type,
    DATEDIFF(h.date_expiration, CURDATE()) as jours_restants
    FROM hebergements h 
    JOIN clients c ON h.id_client = c.id 
    WHERE DATEDIFF(h.date_expiration, CURDATE()) <= 30
    ORDER BY h.date_expiration ASC
");
$hebergements_alertes = $stmt_hebergements->fetchAll();

// Njme3hom kolchi
$alertes = array_merge($domaines_alertes, $hebergements_alertes);
usort($alertes, function($a, $b) {
    return $a['jours_restants'] - $b['jours_restants'];
});

$total_critique = 0;
$total_warning = 0;
foreach($alertes as $a){
    if($a['jours_restants'] < 0) $total_critique++;
    elseif($a['jours_restants'] <= 7) $total_critique++;
    else $total_warning++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Alertes - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}

<?php if($theme == 'dark'): ?>
body {font-family:'Inter',sans-serif; color:#e2e8f0; display:flex; min-height:100vh; background:#0f172a;}
.sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155; z-index:10;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px;}
.sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
.sidebar a:hover, .sidebar a.active {background:#334155; color:#fff;}
.box {background:rgba(30, 41, 59, 0.85); backdrop-filter:blur(10px); border:1px solid #334155;}
.stat-card {background:rgba(30, 41, 59, 0.85); backdrop-filter:blur(10px); border:1px solid #334155;}
th {color:#94a3b8;}
th, td {border-bottom:1px solid #334155;}
<?php else: ?>
body {font-family:'Inter',sans-serif; color:#0f172a; display:flex; min-height:100vh; background:#f8fafc;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0; box-shadow:2px 0 8px rgba(0,0,0,0.05); z-index:10;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s; font-weight:500;}
.sidebar a:hover, .sidebar a.active {background:#f1f5f9; color:#0f172a;}
.box {background:rgba(255, 255, 255, 0.85); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
.stat-card {background:rgba(255, 255, 255, 0.85); backdrop-filter:blur(10px); border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
th {color:#64748b;}
td {color:#334155;}
th, td {border-bottom:1px solid #f1f5f9;}
<?php endif; ?>

.sidebar a i {width:20px; margin-right:10px;}

/* SORA BAYNA DABA - KHFIFT BEZZAF */
.content {
  margin-left:240px; 
  padding:30px; 
  width:calc(100% - 240px);
  min-height:100vh;
  position:relative;
  z-index:1;
}

.content::before {
  content:'';
  position:fixed;
  top:0;
  left:240px;
  right:0;
  bottom:0;
  background: 
    linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.60)),
    url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
  z-index:-1;
}

<?php if($theme != 'dark'): ?>
.content::before {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.60), rgba(241, 245, 249, 0.65)),
    url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
}
<?php endif; ?>

.header h1 {font-size:28px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5);}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:24px; border-radius:12px; display:flex; align-items:center; gap:16px; transition:0.2s;}
.stat-card:hover {transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.4);}
.stat-icon {width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px;}
.stat-icon.critique {background:#fee2e2; color:#dc2626;}
.stat-icon.warning {background:#fef3c7; color:#d97706;}
.stat-icon.info {background:#dbeafe; color:#3b82f6;}
.stat-info h3 {font-size:32px; font-weight:700; margin-bottom:4px;}
.stat-info p {font-size:14px; color:#64748b;}
.box {padding:24px; border-radius:12px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px;}
table {width:100%; border-collapse:collapse;}
th, td {padding:14px; text-align:left; font-size:14px;}
th {font-weight:600; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;}
.badge {padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;}
.badge-expire {background:#fee2e2; color:#dc2626;}
.badge-critique {background:#fef3c7; color:#d97706;}
.badge-warning {background:#fef9c3; color:#ca8a04;}
.type-icon {width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; margin-right:10px;}
.type-domaine {background:#dbeafe; color:#3b82f6;}
.type-hebergement {background:#f3e8ff; color:#a855f7;}
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
  <a href="../domaines/liste.php"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="alertes.php" class="active"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <div class="header">
    <h1><i class="fa fa-bell"></i> Centre d'Alertes</h1>
    <p>Domaines et hébergements nécessitant votre attention</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon critique"><i class="fa fa-triangle-exclamation"></i></div>
      <div class="stat-info">
        <h3><?= $total_critique ?></h3>
        <p>Critiques / Expirés</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon warning"><i class="fa fa-clock"></i></div>
      <div class="stat-info">
        <h3><?= $total_warning ?></h3>
        <p>Expirent bientôt</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon info"><i class="fa fa-list-check"></i></div>
      <div class="stat-info">
        <h3><?= count($alertes) ?></h3>
        <p>Total alertes</p>
      </div>
    </div>
  </div>

  <div class="box">
    <h2><i class="fa fa-list"></i> Liste des Alertes</h2>
    <table>
      <thead>
        <tr>
          <th>Type</th>
          <th>Nom</th>
          <th>Client</th>
          <th>Date Expiration</th>
          <th>Jours Restants</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($alertes)): ?>
          <tr><td colspan="6" class="empty">
            <i class="fa fa-check-circle"></i><br>
            Aucune alerte pour le moment<br>
            <small>Tous vos services sont à jour</small>
          </td></tr>
        <?php else: ?>
          <?php foreach($alertes as $a): 
            $jours = $a['jours_restants'];
            if($jours < 0) {
              $statut_class = 'badge-expire';
              $statut_text = 'Expiré';
            } elseif($jours <= 7) {
              $statut_class = 'badge-critique';
              $statut_text = 'Urgent';
            } else {
              $statut_class = 'badge-warning';
              $statut_text = 'Bientôt';
            }
            $nom = $a['type'] == 'domaine' ? $a['nom_domaine'] : $a['plan'];
          ?>
          <tr>
            <td>
              <span class="type-icon <?= $a['type'] == 'domaine' ? 'type-domaine' : 'type-hebergement' ?>">
                <i class="fa <?= $a['type'] == 'domaine' ? 'fa-globe' : 'fa-server' ?>"></i>
              </span>
              <?= ucfirst($a['type']) ?>
            </td>
            <td><strong><?= htmlspecialchars($nom) ?></strong></td>
            <td><?= htmlspecialchars($a['full_name']) ?></td>
            <td><?= date('d/m/Y', strtotime($a['date_expiration'])) ?></td>
            <td><strong><?= $jours ?></strong> jours</td>
            <td><span class="badge <?= $statut_class ?>"><?= $statut_text ?></span></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>