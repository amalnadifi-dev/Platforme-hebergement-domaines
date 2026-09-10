<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';
$message = "";

$clients = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_client = $_POST['id_client'];
    $plan = $_POST['plan'];
    $espace = $_POST['espace'];
    $bande_passante = $_POST['bande_passante'];
    $date_debut = $_POST['date_debut'];
    $date_expiration = $_POST['date_expiration'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO hebergements
            (id_client, plan, espace, bande_passante, date_debut, date_expiration, statut)
            VALUES (?, ?, ?, ?, ?, ?, 'actif')
        ");

        $stmt->execute([
            $id_client,
            $plan,
            $espace,
            $bande_passante,
            $date_debut,
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

<title>Ajouter Hébergement - HostManager</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{
    --primary:#6366f1;
    --primary-dark:#4f46e5;
    --blue:#3b82f6;
    --green:#10b981;
    --orange:#f59e0b;
    --red:#ef4444;
    --purple:#a855f7;
}

body{
    font-family:'Inter',sans-serif;
    min-height:100vh;
    display:flex;
    transition:.3s;
}



<?php if($theme == 'dark'): ?>

body{
    color:#e2e8f0;
    background:
        linear-gradient(
            rgba(15,23,42,.78),
            rgba(15,23,42,.84)
        ),
        url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2074')
        center/cover fixed no-repeat;
}

.sidebar{
    background:rgba(15,23,42,.97);
    border-right:1px solid #334155;
}

.box,
.topbar{
    background:rgba(30,41,59,.82);
    border:1px solid #334155;
}

.form-group input,
.form-group select{
    background:#0f172a;
    border:1px solid #334155;
    color:#e2e8f0;
}

.form-group input::placeholder{
    color:#64748b;
}

<?php else: ?>



body{
    color:#0f172a;
    background:
        linear-gradient(
            rgba(248,250,252,.88),
            rgba(241,245,249,.92)
        ),
        url('https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=2070')
        center/cover fixed no-repeat;
}

.sidebar{
    background:#fff;
    border-right:1px solid #e2e8f0;
    box-shadow:2px 0 8px rgba(0,0,0,.05);
}

.box,
.topbar{
    background:rgba(255,255,255,.90);
    border:1px solid #e2e8f0;
    box-shadow:0 1px 3px rgba(0,0,0,.08);
}

.form-group input,
.form-group select{
    background:#fff;
    border:1px solid #e2e8f0;
    color:#0f172a;
}

.form-group input::placeholder{
    color:#94a3b8;
}

<?php endif; ?>




.sidebar{
    width:250px;
    height:100vh;
    padding:22px 16px;
    position:fixed;
    left:0;
    top:0;
    z-index:100;
}

.logo{
    display:flex;
    align-items:center;
    gap:10px;
    padding:8px 12px 28px;
}

.logo-icon{
    width:40px;
    height:40px;
    border-radius:11px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:18px;
    background:linear-gradient(
        135deg,
        #6366f1,
        #8b5cf6
    );
}

.logo span{
    font-size:20px;
    font-weight:800;
}

.nav-title{
    font-size:10px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#64748b;
    margin:10px 12px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 13px;
    margin-bottom:5px;
    border-radius:10px;
    text-decoration:none;
    color:#64748b;
    font-size:14px;
    font-weight:500;
    transition:.25s;
}

.sidebar a i{
    width:20px;
    text-align:center;
    font-size:15px;
}

.sidebar a:hover{
    background:rgba(99,102,241,.10);
    color:var(--primary);
    transform:translateX(3px);
}

.sidebar a.active{
    color:#fff;
    background:linear-gradient(
        135deg,
        #6366f1,
        #4f46e5
    );
    box-shadow:0 8px 20px rgba(79,70,229,.25);
}

.sidebar-bottom{
    position:absolute;
    bottom:20px;
    left:16px;
    right:16px;
}

.sidebar-bottom a{
    color:#ef4444;
}



.content{
    margin-left:250px;
    width:calc(100% - 250px);
    min-height:100vh;
    padding:28px;
}




.topbar{
    min-height:78px;
    border-radius:18px;
    padding:16px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:26px;
    backdrop-filter:blur(15px);
}

.welcome h1{
    font-size:23px;
    font-weight:800;
    margin-bottom:5px;
}

.welcome p{
    font-size:13px;
    color:#64748b;
}

.top-actions{
    display:flex;
    align-items:center;
    gap:15px;
}

.date-box{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color:#64748b;
}

.date-box i{
    color:var(--primary);
}

.notification{
    width:40px;
    height:40px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--primary);
    background:rgba(99,102,241,.10);
}




.box{
    border-radius:17px;
    padding:28px;
    max-width:850px;
    backdrop-filter:blur(15px);
}

.box-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:25px;
}

.box-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:17px;
    font-weight:700;
}

.box-title i{
    color:var(--primary);
}

.back-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:9px 13px;
    border-radius:10px;
    color:#64748b;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    transition:.25s;
}

.back-btn:hover{
    background:rgba(99,102,241,.10);
    color:var(--primary);
}




.error{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 15px;
    border-radius:10px;
    margin-bottom:22px;
    font-size:12px;
    background:rgba(239,68,68,.10);
    border:1px solid rgba(239,68,68,.25);
    color:#ef4444;
}




.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.form-group{
    margin-bottom:2px;
}

.form-group.full{
    grid-column:1/-1;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-size:12px;
    font-weight:700;
    color:#64748b;
}

.form-group label span{
    color:var(--red);
}

.form-group input,
.form-group select{
    width:100%;
    height:46px;
    padding:0 14px;
    border-radius:10px;
    outline:none;
    font-family:'Inter',sans-serif;
    font-size:13px;
    transition:.25s;
}

.form-group input:focus,
.form-group select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(99,102,241,.10);
}




.form-actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:10px;
    margin-top:28px;
    padding-top:20px;
    border-top:1px solid rgba(148,163,184,.15);
}

.btn-cancel{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:11px 17px;
    border-radius:10px;
    text-decoration:none;
    color:#64748b;
    font-size:12px;
    font-weight:600;
    transition:.25s;
}

.btn-cancel:hover{
    background:rgba(148,163,184,.10);
}

.btn-submit{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:11px 17px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    color:#fff;
    font-family:'Inter',sans-serif;
    font-size:12px;
    font-weight:600;
    background:linear-gradient(
        135deg,
        #6366f1,
        #4f46e5
    );
    box-shadow:0 6px 15px rgba(79,70,229,.20);
    transition:.25s;
}

.btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 22px rgba(79,70,229,.30);
}




@media(max-width:850px){

    .sidebar{
        width:70px;
        padding:22px 10px;
    }

    .logo{
        justify-content:center;
        padding:8px 0 28px;
    }

    .logo span,
    .nav-title,
    .sidebar a span{
        display:none;
    }

    .sidebar a{
        justify-content:center;
        padding:12px;
    }

    .sidebar a i{
        margin:0;
    }

    .content{
        margin-left:70px;
        width:calc(100% - 70px);
    }

    .date-box{
        display:none;
    }
}

@media(max-width:650px){

    .content{
        padding:18px;
    }

    .topbar{
        padding:15px;
        margin-bottom:18px;
    }

    .welcome h1{
        font-size:18px;
    }

    .box{
        padding:20px;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-group.full{
        grid-column:auto;
    }

    .form-actions{
        flex-direction:column-reverse;
        align-items:stretch;
    }

    .btn-submit,
    .btn-cancel{
        justify-content:center;
        width:100%;
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

    <div class="nav-title">Navigation</div>

    <a href="../administration/tableau_bord.php">
        <i class="fa-solid fa-gauge"></i>
        <span>Tableau de bord</span>
    </a>

    <a href="../clients/liste.php">
        <i class="fa-solid fa-users"></i>
        <span>Clients</span>
    </a>

    <a href="../domaines/liste.php">
        <i class="fa-solid fa-globe"></i>
        <span>Domaines</span>
    </a>

    <a href="liste.php" class="active">
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

        <a href="../administration/deconnexion.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>

    </div>

</div>




<div class="content">

    <div class="topbar">

        <div class="welcome">

            <h1>Nouveau hébergement</h1>

            <p>
                Créez un nouvel hébergement pour un client.
            </p>

        </div>

        <div class="top-actions">

            <div class="date-box">
                <i class="fa-regular fa-calendar"></i>
                <?= date('d/m/Y') ?>
            </div>

            <div class="notification">
                <i class="fa-regular fa-bell"></i>
            </div>

        </div>

    </div>


    <div class="box">

        <div class="box-header">

            <div class="box-title">
                <i class="fa-solid fa-server"></i>
                Informations de l'hébergement
            </div>

            <a href="liste.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Retour
            </a>

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
                        Client <span>*</span>
                    </label>

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


                <div class="form-group">

                    <label>
                        Plan <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="plan"
                        placeholder="Basic, Pro, Business..."
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Espace (MB) <span>*</span>
                    </label>

                    <input
                        type="number"
                        name="espace"
                        placeholder="Ex: 5000"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Bande passante (MB) <span>*</span>
                    </label>

                    <input
                        type="number"
                        name="bande_passante"
                        placeholder="Ex: 10000"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Date début <span>*</span>
                    </label>

                    <input
                        type="date"
                        name="date_debut"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Date expiration <span>*</span>
                    </label>

                    <input
                        type="date"
                        name="date_expiration"
                        required
                    >

                </div>

            </div>


            <div class="form-actions">

                <a href="liste.php" class="btn-cancel">
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </a>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-save"></i>
                    Enregistrer
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>