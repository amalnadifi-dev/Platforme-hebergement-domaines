<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();


if(!$client){
    header("Location: liste.php");
    exit();
}

$message = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $stmt = $pdo->prepare("UPDATE clients SET full_name=?, email=?, phone=?, company=? WHERE id=?");
    if($stmt->execute([$_POST['full_name'], $_POST['email'], $_POST['phone'], $_POST['company'], $id])){
        header("Location: liste.php?updated=1");
        exit();
    } else {
        $message = "Erreur: Email déjà utilisé par un autre client";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Client - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;display:flex}
.sidebar{width:240px;height:100vh;background:#1e293b;padding:20px;position:fixed;border-right:1px solid #334155}
.sidebar h3{color:#6366f1;margin-bottom:30px;font-size:20px}
.sidebar h3 i{margin-right:8px}
.sidebar a{display:block;color:#cbd5e1;text-decoration:none;padding:12px;border-radius:8px;margin-bottom:8px;transition:0.2s}
.sidebar a:hover,.sidebar a.active{background:#334155;color:#fff}
.sidebar a i{width:20px;margin-right:10px}
.content{margin-left:240px;padding:30px;width:calc(100% - 240px)}
.header{margin-bottom:30px}
.header h1{font-size:28px;font-weight:700}
.form-box{background:#1e293b;padding:24px;border-radius:12px;border:1px solid #334155;max-width:600px}
.form-group{margin-bottom:20px}
.form-group label{display:block;margin-bottom:8px;color:#cbd5e1;font-weight:500;font-size:14px}
.form-group input{width:100%;padding:12px;background:#0f172a;border:1px solid #334155;color:#fff;border-radius:8px;font-size:15px}
.form-group input:focus{outline:none;border-color:#6366f1}
.btn{background:#6366f1;color:#fff;padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-weight:500;font-size:15px}
.btn:hover{background:#4f46e5}
.btn-secondary{background:#334155;margin-left:10px;text-decoration:none;display:inline-block}
.btn-secondary:hover{background:#475569}
.alert{background:#ef4444;color:#fff;padding:12px;border-radius:8px;margin-bottom:16px}
</style>
</head>
<body>

<div class="sidebar">
  <h3><i class="fa fa-cloud"></i> HostManager</h3>
  <a href="../administration/tableau_bord.php"><i class="fa fa-gauge"></i> Tableau de bord</a>
  <a href="liste.php" class="active"><i class="fa fa-users"></i> Clients</a>
  <a href="../domaines/liste.php"><i class="fa fa-globe"></i> Domaines</a>
  <a href="../hebergements/liste.php"><i class="fa fa-server"></i> Hébergements</a>
  <a href="../notifications/alertes.php"><i class="fa fa-bell"></i> Alertes</a>
  <a href="../administration/deconnexion.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
  <div class="header">
    <h1>Modifier Client #<?= $client['id'] ?></h1>
  </div>
  
  <div class="form-box">
    <?php if($message): ?>
      <div class="alert"><?= $message ?></div>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label>Nom Complet *</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($client['full_name']) ?>" required>
      </div>
      
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
      </div>
      
      <div class="form-group">
        <label>Téléphone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($client['phone']) ?>">
      </div>
      
      <div class="form-group">
        <label>Entreprise</label>
        <input type="text" name="company" value="<?= htmlspecialchars($client['company']) ?>">
      </div>
      
      <button type="submit" class="btn"><i class="fa fa-save"></i> Enregistrer les modifications</button>
      <a href="liste.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>
</div>

</body>
</html>