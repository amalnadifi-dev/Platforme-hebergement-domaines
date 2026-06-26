<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme']?? 'dark';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $entreprise = $_POST['entreprise'];
    $adresse = $_POST['adresse']; 
    
    $stmt = $pdo->prepare("INSERT INTO clients (full_name, email, telephone, entreprise, adresse) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $telephone, $entreprise, $adresse]);
    
    header("Location: liste.php?msg=added");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter Client - HostManager</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}

<?php if($theme == 'dark'):?>
body {font-family:'Inter',sans-serif; color:#e2e8f0; display:flex; min-height:100vh; background:#0f172a;}
.sidebar {width:240px; height:100vh; background:#1e293b; padding:20px; position:fixed; border-right:1px solid #334155;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px;}
.sidebar a {display:block; color:#cbd5e1; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px;}
.sidebar a:hover,.sidebar a.active {background:#334155; color:#fff;}
.box {background:rgba(30, 41, 59, 0.90); backdrop-filter:blur(10px); border:1px solid #334155;}
input, select, textarea {background:#1e293b; border:1px solid #334155; color:#e2e8f0;}
<?php else:?>
body {font-family:'Inter',sans-serif; color:#0f172a; display:flex; min-height:100vh; background:#f8fafc;}
.sidebar {width:240px; height:100vh; background:#ffffff; padding:20px; position:fixed; border-right:1px solid #e2e8f0;}
.sidebar h3 {color:#6366f1; margin-bottom:30px; font-size:20px; font-weight:700;}
.sidebar a {display:block; color:#64748b; text-decoration:none; padding:12px; border-radius:8px; margin-bottom:8px;}
.sidebar a:hover,.sidebar a.active {background:#f1f5f9; color:#0f172a;}
.box {background:rgba(255, 255, 255, 0.90); backdrop-filter:blur(10px); border:1px solid #e2e8f0;}
input, select, textarea {background:#fff; border:1px solid #e2e8f0; color:#0f172a;}
<?php endif;?>

.sidebar a i {width:20px; margin-right:10px;}

.content {
  margin-left:240px; 
  padding:30px; 
  width:calc(100% - 240px);
  min-height:100vh;
}

.box {padding:30px; border-radius:12px; max-width:600px;}
.box h1 {font-size:24px; margin-bottom:20px;}
.form-group {margin-bottom:20px;}
.form-group label {display:block; margin-bottom:8px; font-weight:600; font-size:14px;}
.form-group input, .form-group select, .form-group textarea {width:100%; padding:12px; border-radius:8px; font-size:14px;}
.btn-submit {background:#6366f1; color:#fff; padding:12px 24px; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:14px;}
.btn-submit:hover {background:#4f46e5;}
.btn-cancel {background:#64748b; color:#fff; padding:12px 24px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; display:inline-block; margin-left:10px;}
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
  <div class="box">
    <h1><i class="fa fa-user-plus"></i> Ajouter un Client</h1>
    
    <form method="POST">
      <div class="form-group">
        <label>Nom Complet *</label>
        <input type="text" name="full_name" required placeholder="Ex: Ahmed Benali">
      </div>

      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" required placeholder="client@email.com">
      </div>

      <div class="form-group">
        <label>Téléphone</label>
        <input type="tel" name="telephone" placeholder="06XXXXXXXX">
      </div>

      <div class="form-group">
        <label>Entreprise</label>
        <input type="text" name="entreprise" placeholder="Nom de l'entreprise">
      </div>

      <div class="form-group">
        <label>Adresse</label>
        <textarea name="adresse" rows="3" placeholder="Rue, Ville, Code Postal"></textarea>
      </div>

      <button type="submit" class="btn-submit"><i class="fa fa-save"></i> Enregistrer</button>
      <a href="liste.php" class="btn-cancel">Annuler</a>
    </form>
  </div>
</div>

</body>
</html>