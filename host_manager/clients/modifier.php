<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';

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

    $stmt = $pdo->prepare("
        UPDATE clients 
        SET full_name = ?, 
            email = ?, 
            phone = ?, 
            company = ?
        WHERE id = ?
    ");

    if($stmt->execute([
        $_POST['full_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['company'],
        $id
    ])){
        header("Location: liste.php?updated=1");
        exit();
    }else{
        $message = "Erreur: Email déjà utilisé par un autre client";
    }
}

$heure = date('H');

if ($heure <=18) {
    $bonjour = "Bonjour";
} else {
    $bonjour = "Bonsoir";
}

$aujourdhui = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Modifier Client - HostManager</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    background:#0f172a;
    color:#e2e8f0;
}


.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    padding:22px 16px;
    background:rgba(15,23,42,.97);
    border-right:1px solid #334155;
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
    color:white;
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    font-size:18px;
}

.logo span{
    font-size:20px;
    font-weight:800;
    color:#f8fafc;
}



.nav-title{
    color:#64748b;
    font-size:10px;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase;
    margin:10px 12px;
}

.nav-link{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 13px;
    margin-bottom:5px;
    border-radius:10px;
    color:#64748b;
    text-decoration:none;
    font-size:14px;
    font-weight:500;
    transition:.25s;
}

.nav-link i{
    width:18px;
    text-align:center;
    font-size:15px;
}

.nav-link:hover{
    color:#e2e8f0;
    background:rgba(99,102,241,.10);
    transform:translateX(3px);
}

.nav-link.active{
    color:white;
    background:linear-gradient(135deg,#6366f1,#4f46e5);
    box-shadow:0 8px 20px rgba(79,70,229,.25);
}


.sidebar-bottom{
    position:absolute;
    bottom:20px;
    left:16px;
    right:16px;
}

.logout{
    color:#ef4444;
}

.logout:hover{
    color:#fff;
    background:rgba(239,68,68,.12);
}


.content{
    margin-left:250px;
    width:calc(100% - 250px);
    min-height:100vh;
    padding:28px;

    background:
        linear-gradient(
            rgba(15,23,42,.78),
            rgba(15,23,42,.84)
        ),
        url("https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop");

    background-size:cover;
    background-position:center;
    background-attachment:fixed;
}



.topbar{
    min-height:78px;
    border-radius:18px;
    padding:16px 20px;
    margin-bottom:26px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    background:rgba(30,41,59,.82);
    border:1px solid #334155;

    backdrop-filter:blur(15px);
}

.welcome h1{
    font-size:23px;
    font-weight:800;
    color:#f8fafc;
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

    background:rgba(99,102,241,.12);
    color:var(--primary);
}



.box{
    width:100%;
    max-width:900px;
    margin:0 auto;

    padding:24px;

    border-radius:17px;

    background:rgba(30,41,59,.82);
    border:1px solid #334155;

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

    font-size:16px;
    font-weight:700;
    color:#f8fafc;
}

.box-title i{
    color:var(--primary);
}


.client-id{
    padding:7px 11px;
    border-radius:8px;

    background:rgba(99,102,241,.12);
    color:#a5b4fc;

    font-size:11px;
    font-weight:700;
}


.alert{
    display:flex;
    align-items:center;
    gap:10px;

    padding:12px 15px;
    margin-bottom:20px;

    border-radius:10px;

    background:rgba(239,68,68,.10);
    border:1px solid rgba(239,68,68,.25);

    color:#fca5a5;
    font-size:12px;
}


.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group.full{
    grid-column:1 / -1;
}

.form-group label{
    margin-bottom:8px;

    font-size:11px;
    font-weight:700;
    color:#94a3b8;
}

.form-group label span{
    color:var(--red);
}

.form-control{
    width:100%;
    padding:12px 14px;

    border-radius:10px;

    border:1px solid #334155;
    background:rgba(15,23,42,.55);

    color:#e2e8f0;

    font-family:'Inter',sans-serif;
    font-size:13px;

    outline:none;
    transition:.2s;
}

.form-control::placeholder{
    color:#64748b;
}

.form-control:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(99,102,241,.12);
}

textarea.form-control{
    min-height:110px;
    resize:vertical;
}



.form-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;

    margin-top:25px;
    padding-top:20px;

    border-top:1px solid #334155;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    padding:10px 15px;

    border-radius:10px;

    font-size:12px;
    font-weight:600;

    text-decoration:none;
    border:none;
    cursor:pointer;

    transition:.25s;
}

.btn-primary{
    color:white;
    background:linear-gradient(
        135deg,
        #6366f1,
        #4f46e5
    );
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(79,70,229,.30);
}

.btn-secondary{
    color:#cbd5e1;
    background:rgba(100,116,139,.12);
    border:1px solid #334155;
}

.btn-secondary:hover{
    background:rgba(100,116,139,.20);
    color:white;
}



body.light{
    background:#f1f5f9;
    color:#334155;
}

body.light .sidebar{
    background:#fff;
    border-color:#e2e8f0;
    box-shadow:0 5px 20px rgba(15,23,42,.05);
}

body.light .logo span{
    color:#1e293b;
}

body.light .nav-link{
    color:#64748b;
}

body.light .nav-link:hover{
    color:#334155;
    background:rgba(99,102,241,.08);
}

body.light .content{
    background:
        linear-gradient(
            rgba(241,245,249,.82),
            rgba(241,245,249,.88)
        ),
        url("https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop");

    background-size:cover;
    background-position:center;
    background-attachment:fixed;
}

body.light .topbar,
body.light .box{
    background:rgba(255,255,255,.90);
    border-color:#e2e8f0;
    box-shadow:0 8px 25px rgba(15,23,42,.06);
}

body.light .welcome h1,
body.light .box-title{
    color:#1e293b;
}

body.light .form-control{
    background:#fff;
    color:#334155;
    border-color:#e2e8f0;
}

body.light .form-group label{
    color:#64748b;
}

body.light .form-actions{
    border-color:#e2e8f0;
}

body.light .btn-secondary{
    color:#475569;
    background:#f8fafc;
    border-color:#e2e8f0;
}


@media(max-width:850px){

    .sidebar{
        width:70px;
        padding:22px 10px;
    }

    .logo{
        justify-content:center;
        padding-left:0;
        padding-right:0;
    }

    .logo span,
    .nav-title,
    .nav-link span{
        display:none;
    }

    .nav-link{
        justify-content:center;
        padding:12px;
    }

    .content{
        margin-left:70px;
        width:calc(100% - 70px);
        padding:20px;
    }

    .date-box{
        display:none;
    }
}

@media(max-width:600px){

    .content{
        padding:14px;
    }

    .topbar{
        padding:14px;
        margin-bottom:18px;
    }

    .welcome h1{
        font-size:18px;
    }

    .welcome p{
        font-size:11px;
    }

    .box{
        padding:18px;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-group.full{
        grid-column:auto;
    }

    .box-header{
        align-items:flex-start;
    }

    .form-actions{
        flex-direction:column-reverse;
    }

    .btn{
        width:100%;
    }
}

</style>

</head>

<body class="<?= $theme === 'light' ? 'light' : '' ?>">



<aside class="sidebar">

    <div class="logo">
        <div class="logo-icon">
            <i class="fa-solid fa-server"></i>
        </div>

        <span>HostManager</span>
    </div>

    <div class="nav-title">
        Menu principal
    </div>

    <a href="../administration/tableau_bord.php" class="nav-link">
        <i class="fa-solid fa-chart-line"></i>
        <span>Tableau de bord</span>
    </a>

    <a href="liste.php" class="nav-link active">
        <i class="fa-solid fa-users"></i>
        <span>Clients</span>
    </a>

    <a href="../domaines/liste.php" class="nav-link">
        <i class="fa-solid fa-globe"></i>
        <span>Domaines</span>
    </a>

    <a href="../hebergements/liste.php" class="nav-link">
        <i class="fa-solid fa-server"></i>
        <span>Hébergements</span>
    </a>

    <a href="../paiements/liste.php" class="nav-link">
        <i class="fa-solid fa-credit-card"></i>
        <span>Paiements</span>
    </a>

    <a href="../notifications/alertes.php" class="nav-link">
        <i class="fa-solid fa-bell"></i>
        <span>Alertes</span>
    </a>

    <div class="sidebar-bottom">

        <a href="../administration/deconnexion.php"
           class="nav-link logout">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>Déconnexion</span>

        </a>

    </div>

</aside>



<main class="content">

    <!-- TOPBAR -->

    <div class="topbar">

        <div class="welcome">

            <h1>
                <?= $bonjour ?> 👋
            </h1>

            <p>
                Modifiez les informations de votre client.
            </p>

        </div>

        <div class="top-actions">

            <div class="date-box">

                <i class="fa-regular fa-calendar"></i>

                <?= $aujourdhui ?>

            </div>

            <div class="notification">

                <i class="fa-regular fa-bell"></i>

            </div>

        </div>

    </div>


    

    <div class="box">

        <div class="box-header">

            <div class="box-title">

                <i class="fa-solid fa-user-pen"></i>

                Modifier le client

            </div>

            <div class="client-id">

                ID #<?= htmlspecialchars($client['id']) ?>

            </div>

        </div>


        <?php if($message): ?>

            <div class="alert">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-grid">

                

                <div class="form-group">

                    <label>
                        Nom complet <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        value="<?= htmlspecialchars($client['full_name']) ?>"
                        placeholder="Nom complet"
                        required
                    >

                </div>


                

                <div class="form-group">

                    <label>
                        Email <span>*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($client['email']) ?>"
                        placeholder="exemple@email.com"
                        required
                    >

                </div>


                

                <div class="form-group">

                    <label>
                        Téléphone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="<?= htmlspecialchars($client['phone'] ?? '') ?>"
                        placeholder="06XXXXXXXX"
                    >

                </div>


                

                <div class="form-group">

                    <label>
                        Entreprise
                    </label>

                    <input
                        type="text"
                        name="company"
                        class="form-control"
                        value="<?= htmlspecialchars($client['company'] ?? '') ?>"
                        placeholder="Nom de l'entreprise"
                    >

                </div>

            </div>


            

            <div class="form-actions">

                <a href="liste.php" class="btn btn-secondary">

                    <i class="fa-solid fa-xmark"></i>

                    Annuler

                </a>

                <button type="submit" class="btn btn-primary">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Enregistrer les modifications

                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>