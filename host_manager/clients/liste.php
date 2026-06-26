<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';


if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id =?");
    $stmt->execute([$id]);
    header("Location: liste.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("
    SELECT c.*, 
    COUNT(DISTINCT d.id) as total_domaines,
    COUNT(DISTINCT h.id) as total_hebergements
    FROM clients c 
    LEFT JOIN domaines d ON c.id = d.id_client 
    LEFT JOIN hebergements h ON c.id = h.id_client 
    GROUP BY c.id 
    ORDER BY c.id DESC
");
$clients = $stmt->fetchAll();

$total_clients = count($clients);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Clients - HostManager</title>
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
    linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.60)),
    url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}

<?php if($theme!= 'dark'):?>
.content {
  background: 
    linear-gradient(rgba(248, 250, 252, 0.60), rgba(241, 245, 249, 0.65)),
    url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=2070&auto=format&fit=crop') center/cover fixed no-repeat;
}
<?php endif;?>

.header h1 {font-size:28px; font-weight:700; margin-bottom:8px; text-shadow:0 2px 8px rgba(0,0,0,0.5);}
.header p {color:#94a3b8; margin-bottom:30px; text-shadow:0 1px 4px rgba(0,0,0,0.5);}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-card {padding:24px; border-radius:12px; display:flex; align-items:center; gap:16px; transition:0.2s;}
.stat-card:hover {transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.4);}
.stat-icon {width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px;}
.stat-icon.clients {background:#dbeafe; color:#3b82f6;}
.stat-icon.domaines {background:#fef3c7; color:#d97706;}
.stat-icon.hebergements {background:#f3e8ff; color:#a855f7;}
.stat-info h3 {font-size:32px; font-weight:700; margin-bottom:4px;}
.stat-info p {font-size:14px; color:#64748b;}
.box {padding:24px; border-radius:12px;}
.box h2 {font-size:18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; justify-content:space-between;}
.btn-add {background:#6366f1; color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:500; transition:0.2s;}
.btn-add:hover {background:#4f46e5;}
table {width:100%; border-collapse:collapse;}
th, td {padding:14px; text-align:left; font-size:14px;}
th {font-weight:600; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;}
.avatar {width:36px; height:36px; border-radius:50%; background:#6366f1; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:600; margin-right:10px;}
.badge {padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;}
.badge-active {background:#dcfce7; color:#16a34a;}
.badge-inactive {background:#fee2e2; color:#dc2626;}
.actions {display:flex; gap:8px;}
.btn-action {padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:12px; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;}
.btn-edit {background:#fef3c7; color:#d97706;}
.btn-edit:hover {background:#fde68a;}
.btn-delete {background:#fee2e2; color:#dc2626;}
.btn-delete:hover {background:#fecaca;}
.empty {text-align:center; padding:60px; color:#64748b;}
.empty i {font-size:48px; margin-bottom:16px; opacity:0.5;}
tr:hover {background:rgba(99,102,241,0.08);}
.alert {padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;}
.alert-success {background:#dcfce7; color:#16a34a; border:1px solid #86efac;}
</style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="../administration/tableau_bord.php"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="liste.php" class="active"><i class="fa fa-users"></i> Clients</a>
  <a href="../domaines/liste.php"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../paiements/liste.php"><i class="fa fa-credit-card"></i> Paiements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'):?>
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> Client supprimé avec succès
    </div>
  <?php endif;?>

  <div class="header">
    <h1><i class="fa fa-users"></i> Gestion des Clients</h1>
    <p>Gérez vos clients et leurs services</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon clients"><i class="fa fa-users"></i></div>
      <div class="stat-info">
        <h3><?= $total_clients?></h3>
        <p>Total Clients</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon domaines"><i class="fa fa-globe"></i></div>
      <div class="stat-info">
        <h3><?= array_sum(array_column($clients, 'total_domaines'))?></h3>
        <p>Domaines Actifs</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon hebergements"><i class="fa fa-server"></i></div>
      <div class="stat-info">
        <h3><?= array_sum(array_column($clients, 'total_hebergements'))?></h3>
        <p>Hébergements</p>
      </div>
    </div>
  </div>

  <div class="box">
    <h2>
      <span><i class="fa fa-list"></i> Liste des Clients</span>
      <a href="ajouter.php" class="btn-add"><i class="fa fa-plus"></i> Nouveau Client</a>
    </h2>
    <table>
      <thead>
        <tr>
          <th>Client</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Domaines</th>
          <th>Hébergements</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($clients)):?>
          <tr><td colspan="7" class="empty">
            <i class="fa fa-user-slash"></i><br>
            Aucun client pour le moment<br>
            <small>Commencez par ajouter votre premier client</small>
          </td></tr>
        <?php else:?>
          <?php foreach($clients as $c): 
            $telephone = $c['telephone']?? $c['phone']?? 'N/A';
            $statut = $c['statut']?? 'actif';
            $statut_class = $statut == 'actif'? 'badge-active' : 'badge-inactive';
            
            $nom_parts = explode(' ', trim($c['full_name']));
            $initials = strtoupper(substr($nom_parts[0], 0, 1). (isset($nom_parts[1])? substr($nom_parts[1], 0, 1) : ''));
       ?>
          <tr>
            <td>
              <span class="avatar"><?= $initials?></span>
              <strong><?= htmlspecialchars($c['full_name'])?></strong>
            </td>
            <td><?= htmlspecialchars($c['email'])?></td>
            <td><?= htmlspecialchars($telephone)?></td>
            <td><strong><?= $c['total_domaines']?></strong></td>
            <td><strong><?= $c['total_hebergements']?></strong></td>
            <td><span class="badge <?= $statut_class?>"><?= ucfirst($statut)?></span></td>
            <td class="actions">
           
              <a href="modifier.php?id=<?= $c['id']?>" class="btn-action btn-edit" title="Modifier"><i class="fa fa-pen"></i></a>
              <a href="liste.php?delete=<?= $c['id']?>" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')"><i class="fa fa-trash"></i></a>
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