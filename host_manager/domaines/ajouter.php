<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';
$message = "";

// Jib liste dyal clients bach nkhtaro
$clients = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom_domaine = $_POST['nom_domaine'];
    $id_client = $_POST['id_client'];
    $date_enregistrement = $_POST['date_enregistrement'];
    $date_expiration = $_POST['date_expiration'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO domaines (nom_domaine, id_client, date_enregistrement, date_expiration, statut) VALUES (?, ?, ?, ?, 'actif')");
        $stmt->execute([$nom_domaine, $id_client, $date_enregistrement, $date_expiration]);
        header("Location: liste.php?success=1");
        exit();
    } catch(PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter Domaine - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}
<?php if($theme == 'dark'): ?>
body {font-family: 'Inter', sans-serif; color:#e2e8f0; display:flex; background: linear-gradient(rgba(15, 23, 42, 0.93), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2074') center/cover fixed; min-height:100vh;}
.sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px;}
.sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s;}
.sidebar a:hover, .sidebar a.active {background:#334155; color:#fff;}
.box {background:#1e293b; border:1px solid #334155;}
input, select {background:#0f172a; border:1px solid #334155; color:#fff;}
input:focus, select:focus {border-color:#6366f1;}
<?php else: ?>
body {font-family: 'Inter', sans-serif; color:#0f172a; display:flex; background: linear-gradient(rgba(248, 250, 252, 0.88), rgba(241, 245, 249, 0.92)), url('https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=2070') center/cover fixed; min-height:100vh;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0; box-shadow:2px 0 8px rgba(0,0,0,0.05);}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px; transition:0.2s; font-weight:500;}
.sidebar a:hover, .sidebar a.active {background:#f1f5f9; color:#0f172a;}
.box {background:#ffffff; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.08);}
input, select {background:#ffffff; border:1px solid #e2e8f0; color:#0f172a;}
input:focus, select:focus {border-color:#6366f1;}
<?php endif; ?>
.sidebar a i {width:20px; margin-right:10px;}
.content {margin-left:240px; padding:30px; width:calc(100% - 240px);}
.box {padding:32px; border-radius:12px; max-width:600px;}
.box h1 {margin-bottom:24px; font-size:24px;}
.form-group {margin-bottom:20px;}
.form-group label {display:block; margin-bottom:8px; font-weight:600; font-size:14px;}
.form-group input, .form-group select {width:100%; padding:12px 16px; border-radius:8px; font-size:14px; font-family:'Inter'; transition:0.2s;}
.form-group input:focus, .form-group select:focus {outline:none; box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
.btn-submit {background:#6366f1; color:#fff; padding:12px 24px; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:15px;}
.btn-submit:hover {opacity:0.9;}
.error {background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:12px; border-radius:8px; margin-bottom:20px;}
.back-link {color:#6366f1; text-decoration:none; font-weight:500; display:inline-block; margin-bottom:20px;}
</style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="../administration/tableau_bord.php"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="../clients/liste.php"><i class="fa fa-users"></i> Clients</a>
  <a href="liste.php" class="active"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <a href="liste.php" class="back-link"><i class="fa fa-arrow-left"></i> Retour à la liste</a>
  
  <div class="box">
    <h1><i class="fa fa-globe"></i> Ajouter un Domaine</h1>
    
    <?php if($message): ?>
      <div class="error"><?= $message ?></div>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label>Nom de Domaine *</label>
        <input type="text" name="nom_domaine" placeholder="exemple.com" required>
      </div>
      <div class="form-group">
        <label>Client *</label>
        <select name="id_client" required>
          <option value="">-- Choisir un client --</option>
          <?php foreach($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Date Enregistrement *</label>
        <input type="date" name="date_enregistrement" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="form-group">
        <label>Date Expiration *</label>
        <input type="date" name="date_expiration" required>
      </div>
      <button type="submit" class="btn-submit"><i class="fa fa-save"></i> Enregistrer</button>
    </form>
  </div>
</div>

</body>
</html>