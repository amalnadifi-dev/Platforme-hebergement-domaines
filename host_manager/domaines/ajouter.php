<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';
$message = "";


$clients = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom_domaine = $_POST['nom_domaine'];
    $id_client = $_POST['id_client'];
    $date_enregistrement = $_POST['date_enregistrement'];
    $date_expiration = $_POST['date_expiration'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO domaines
            (nom_domaine, id_client, date_enregistrement, date_expiration, statut)
            VALUES (?, ?, ?, ?, 'actif')
        ");

        $stmt->execute([
            $nom_domaine,
            $id_client,
            $date_enregistrement,
            $date_expiration
        ]);

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ajouter Domaine - HostManager</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --blue: #3b82f6;
    --green: #10b981;
    --orange: #f59e0b;
    --red: #ef4444;
    --purple: #a855f7;

    

<?php if($theme == 'dark'): ?>

body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    color: #e2e8f0;
    display: flex;

    background:
        linear-gradient(
            rgba(15,23,42,.78),
            rgba(15,23,42,.84)
        ),
        url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.sidebar {
    background: rgba(15,23,42,.97);
    border-right: 1px solid #334155;
}

.content {
    background: transparent;
}

.topbar,
.box {
    background: rgba(30,41,59,.82);
    border: 1px solid #334155;
}

.form-group input,
.form-group select {
    background: rgba(15,23,42,.85);
    border: 1px solid #334155;
    color: #f8fafc;
}

.form-group input::placeholder {
    color: #64748b;
}

.form-group select option {
    background: #1e293b;
    color: #fff;
}

.form-group label {
    color: #cbd5e1;
}

.back-link {
    color: #818cf8;
}

<?php else: ?>



body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    color: #0f172a;
    display: flex;

    background:
        linear-gradient(
            rgba(248,250,252,.88),
            rgba(241,245,249,.92)
        ),
        url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.sidebar {
    background: #fff;
    border-right: 1px solid #e2e8f0;
    box-shadow: 2px 0 8px rgba(0,0,0,.05);
}

.topbar,
.box {
    background: rgba(255,255,255,.90);
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(15,23,42,.06);
}

.form-group input,
.form-group select {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #0f172a;
}

.form-group input::placeholder {
    color: #94a3b8;
}

.form-group select option {
    background: #fff;
    color: #0f172a;
}

.form-group label {
    color: #334155;
}

.back-link {
    color: #6366f1;
}

<?php endif; ?>




.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    padding: 22px 16px;
    z-index: 100;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px 28px;
}

.logo-icon {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: linear-gradient(
        135deg,
        #6366f1,
        #8b5cf6
    );

    color: #fff;
    font-size: 18px;
}

.logo span {
    font-size: 20px;
    font-weight: 800;
}

.nav-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #64748b;
    margin: 10px 12px;
    text-transform: uppercase;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 12px 13px;
    margin-bottom: 5px;

    border-radius: 10px;

    text-decoration: none;

    color: #64748b;

    font-size: 14px;
    font-weight: 500;

    transition: .25s;
}

.sidebar a i {
    width: 20px;
    text-align: center;
    font-size: 15px;
}

.sidebar a:hover {
    background: rgba(99,102,241,.10);
    color: #6366f1;
    transform: translateX(3px);
}

.sidebar a.active {
    color: #fff;

    background: linear-gradient(
        135deg,
        #6366f1,
        #4f46e5
    );

    box-shadow:
        0 8px 20px rgba(99,102,241,.25);
}

.sidebar-bottom {
    position: absolute;
    bottom: 20px;
    left: 16px;
    right: 16px;
}

.logout {
    color: #ef4444 !important;
}

.logout:hover {
    background: rgba(239,68,68,.10) !important;
    color: #ef4444 !important;
}




.content {
    margin-left: 250px;
    width: calc(100% - 250px);
    min-height: 100vh;
    padding: 28px;
}




.topbar {
    min-height: 78px;

    border-radius: 18px;

    padding: 16px 20px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 26px;

    backdrop-filter: blur(15px);
}

.welcome h1 {
    font-size: 23px;
    font-weight: 800;
    margin-bottom: 5px;
}

.welcome p {
    font-size: 13px;
    color: #64748b;
}

.top-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.date-box {
    display: flex;
    align-items: center;
    gap: 8px;

    font-size: 13px;
    color: #64748b;
}

.date-box i {
    color: #6366f1;
}

.notification {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: rgba(99,102,241,.10);

    color: #6366f1;

    text-decoration: none;
}




.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    text-decoration: none;

    font-size: 12px;
    font-weight: 600;

    margin-bottom: 16px;

    transition: .2s;
}

.back-link:hover {
    transform: translateX(-3px);
}




.box {
    width: 100%;
    max-width: 760px;

    padding: 26px;

    border-radius: 17px;

    backdrop-filter: blur(15px);
}

.box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 24px;
}

.box-title {
    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 17px;
    font-weight: 700;
}

.box-title i {
    color: #6366f1;
}




.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.form-group {
    margin-bottom: 4px;
}

.form-group.full {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: .4px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;

    transform: translateY(-50%);

    color: #64748b;

    font-size: 13px;

    pointer-events: none;
}

.form-group input,
.form-group select {
    width: 100%;

    padding: 12px 14px 12px 40px;

    border-radius: 10px;

    font-family: 'Inter', sans-serif;
    font-size: 13px;

    outline: none;

    transition: .25s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #6366f1;

    box-shadow:
        0 0 0 3px rgba(99,102,241,.10);
}

.form-group select {
    appearance: none;
    cursor: pointer;
}




.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 10px;

    margin-top: 25px;

    padding-top: 20px;

    border-top: 1px solid rgba(100,116,139,.18);
}

.btn-cancel,
.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    padding: 10px 16px;

    border-radius: 10px;

    text-decoration: none;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition: .25s;
}

.btn-cancel {
    color: #64748b;

    background: rgba(100,116,139,.10);

    border: 1px solid rgba(100,116,139,.15);
}

.btn-cancel:hover {
    color: #334155;
    transform: translateY(-2px);
}

.btn-submit {
    border: none;

    color: #fff;

    background: linear-gradient(
        135deg,
        #6366f1,
        #4f46e5
    );

    box-shadow:
        0 6px 15px rgba(99,102,241,.22);
}

.btn-submit:hover {
    transform: translateY(-2px);

    box-shadow:
        0 10px 22px rgba(99,102,241,.30);
}




.error {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 12px 14px;

    margin-bottom: 20px;

    border-radius: 10px;

    background: rgba(239,68,68,.10);

    border: 1px solid rgba(239,68,68,.25);

    color: #ef4444;

    font-size: 12px;
    font-weight: 500;
}




@media (max-width: 850px) {

    .sidebar {
        width: 70px;
        padding: 20px 10px;
    }

    .logo {
        justify-content: center;
        padding: 8px 0 28px;
    }

    .logo span,
    .nav-title,
    .sidebar a span {
        display: none;
    }

    .sidebar a {
        justify-content: center;
        padding: 13px;
    }

    .sidebar a i {
        margin: 0;
    }

    .content {
        margin-left: 70px;
        width: calc(100% - 70px);
        padding: 20px;
    }

    .date-box {
        display: none;
    }

    .box {
        max-width: 100%;
    }
}


@media (max-width: 600px) {

    .content {
        padding: 14px;
    }

    .topbar {
        padding: 14px 16px;
        margin-bottom: 18px;
    }

    .welcome h1 {
        font-size: 18px;
    }

    .welcome p {
        font-size: 11px;
    }

    .box {
        padding: 18px;
        border-radius: 14px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
    }
}

</style>
</head>

<body>




<div class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            <i class="fa-solid fa-cloud"></i>
        </div>

        <span>HostManager</span>

    </div>


    <div class="nav-title">
        Navigation
    </div>


    <a href="../administration/tableau_bord.php">
        <i class="fa-solid fa-gauge"></i>
        <span>Tableau de bord</span>
    </a>


    <a href="../clients/liste.php">
        <i class="fa-solid fa-users"></i>
        <span>Clients</span>
    </a>


    <a href="liste.php" class="active">
        <i class="fa-solid fa-globe"></i>
        <span>Domaines</span>
    </a>


    <a href="../hebergements/liste.php">
        <i class="fa-solid fa-server"></i>
        <span>Hébergements</span>
    </a>


    <a href="../paiements/liste.php">
        <i class="fa-solid fa-credit-card"></i>
        <span>Paiements</span>
    </a>


    <a href="../notifications/alertes.php">
        <i class="fa-solid fa-bell"></i>
        <span>Alertes</span>
    </a>


    <div class="sidebar-bottom">

        <a href="../administration/deconnexion.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>

    </div>

</div>





<div class="content">


    

    <div class="topbar">

        <div class="welcome">

            <h1>Nouveau domaine</h1>

            <p>
                Enregistrez un nouveau domaine dans votre espace HostManager.
            </p>

        </div>


        <div class="top-actions">

            <div class="date-box">

                <i class="fa-regular fa-calendar"></i>

                <?= date('d/m/Y') ?>

            </div>


            <a href="../notifications/alertes.php" class="notification">

                <i class="fa-regular fa-bell"></i>

            </a>

        </div>

    </div>



  

    <a href="liste.php" class="back-link">

        <i class="fa-solid fa-arrow-left"></i>

        Retour à la liste

    </a>



    

    <div class="box">


        <div class="box-header">

            <div class="box-title">

                <i class="fa-solid fa-globe"></i>

                Ajouter un Domaine

            </div>

        </div>



        <?php if($message): ?>

            <div class="error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>



        <form method="POST">


            <div class="form-grid">


                

                <div class="form-group full">

                    <label>
                        Nom de Domaine *
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-globe"></i>

                        <input
                            type="text"
                            name="nom_domaine"
                            placeholder="exemple.com"
                            required
                        >

                    </div>

                </div>



              

                <div class="form-group full">

                    <label>
                        Client *
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-user"></i>

                        <select name="id_client" required>

                            <option value="">
                                -- Choisir un client --
                            </option>

                            <?php foreach($clients as $c): ?>

                                <option value="<?= $c['id'] ?>">

                                    <?= htmlspecialchars($c['full_name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Date Enregistrement *
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-regular fa-calendar-plus"></i>

                        <input
                            type="date"
                            name="date_enregistrement"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >

                    </div>

                </div>





                <div class="form-group">

                    <label>
                        Date Expiration *
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-regular fa-calendar-xmark"></i>

                        <input
                            type="date"
                            name="date_expiration"
                            required
                        >

                    </div>

                </div>


            </div>





            <div class="form-actions">

                <a href="liste.php" class="btn-cancel">

                    <i class="fa-solid fa-xmark"></i>

                    Annuler

                </a>


                <button type="submit" class="btn-submit">

                    <i class="fa-solid fa-save"></i>

                    Enregistrer le domaine

                </button>

            </div>


        </form>

    </div>


</div>


</body>
</html>