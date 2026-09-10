<?php

require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';



if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("
        DELETE FROM hebergements
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: liste.php?msg=deleted");
    exit;
}


$stmt = $pdo->query("
    SELECT
        h.*,
        c.full_name,
        c.email,

        CASE
            WHEN h.date_expiration < CURDATE()
                THEN 'expire'

            WHEN h.date_expiration <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                THEN 'expire_bientot'

            ELSE 'actif'
        END AS statut_auto

    FROM hebergements h

    LEFT JOIN clients c
        ON h.id_client = c.id

    ORDER BY h.date_expiration ASC
");

$hebergements = $stmt->fetchAll();



$total_hebergements = count($hebergements);

$total_actifs = count(
    array_filter(
        $hebergements,
        fn($h) => $h['statut_auto'] === 'actif'
    )
);

$total_bientot = count(
    array_filter(
        $hebergements,
        fn($h) => $h['statut_auto'] === 'expire_bientot'
    )
);

$total_expires = count(
    array_filter(
        $hebergements,
        fn($h) => $h['statut_auto'] === 'expire'
    )
);



$aujourdhui = date('d/m/Y');

$heure = (int) date('H');

if ($heure <=18) {
    $bonjour = "Bonjour";
} else {
    $bonjour = "Bonsoir";
}


?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>HostManager - Hébergements</title>




<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
>




<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>


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
}




body {

    font-family: 'Inter', sans-serif;

    min-height: 100vh;

    display: flex;
}



<?php if ($theme == 'dark'): ?>

body {

    color: #e2e8f0;

    background: #0f172a;
}


.sidebar {

    background: rgba(15,23,42,0.97);

    border-right: 1px solid #334155;
}


.content {

    background:

        linear-gradient(
            rgba(15,23,42,0.78),
            rgba(15,23,42,0.84)
        ),

        url('https://images.unsplash.com/photo-1601597111158-2fceff292cdc?q=80&w=2070&auto=format&fit=crop')

        center/cover fixed no-repeat;
}


.card,
.box,
.topbar,
.stat-card {

    background: rgba(30,41,59,0.82);

    border: 1px solid #334155;
}


table th {

    color: #94a3b8;
}


table td {

    color: #e2e8f0;
}


tbody tr {

    border-bottom: 1px solid #334155;
}




<?php else: ?>

body {

    color: #0f172a;

    background: #f1f5f9;
}


.sidebar {

    background: #ffffff;

    border-right: 1px solid #e2e8f0;

    box-shadow:
        2px 0 10px rgba(0,0,0,0.05);
}


.content {

    background:

        linear-gradient(
            rgba(248,250,252,0.86),
            rgba(241,245,249,0.90)
        ),

        url('https://images.unsplash.com/photo-1601597111158-2fceff292cdc?q=80&w=2070&auto=format&fit=crop')

        center/cover fixed no-repeat;
}


.card,
.box,
.topbar,
.stat-card {

    background: rgba(255,255,255,0.90);

    border: 1px solid #e2e8f0;

    box-shadow:
        0 5px 20px rgba(15,23,42,0.05);
}


table th {

    color: #64748b;
}


table td {

    color: #334155;
}


tbody tr {

    border-bottom: 1px solid #f1f5f9;
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

    background:

        linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
        );

    border-radius: 11px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;
}


.logo span {

    font-size: 20px;

    font-weight: 800;
}




.nav-title {

    font-size: 10px;

    text-transform: uppercase;

    letter-spacing: 1px;

    color: #64748b;

    margin: 10px 12px;
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

    transition: 0.25s;
}


.sidebar a i {

    width: 20px;

    text-align: center;
}


.sidebar a:hover {

    background:
        rgba(99,102,241,0.10);

    color: var(--primary);

    transform: translateX(3px);
}


.sidebar a.active {

    background:

        linear-gradient(
            135deg,
            #6366f1,
            #4f46e5
        );

    color: white;

    box-shadow:
        0 7px 18px rgba(99,102,241,0.25);
}




.sidebar-bottom {

    position: absolute;

    bottom: 20px;

    left: 16px;

    right: 16px;
}


.sidebar-bottom a {

    color: #ef4444;
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

    color: var(--primary);
}


.notification {

    width: 40px;

    height: 40px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
        rgba(99,102,241,0.10);

    color: var(--primary);

    text-decoration: none;
}



.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 15px;

    margin-bottom: 22px;
}


.stat-card {

    padding: 20px;

    border-radius: 16px;

    transition: 0.25s;

    position: relative;

    overflow: hidden;
}


.stat-card:hover {

    transform: translateY(-4px);
}


.stat-top {

    display: flex;

    justify-content: space-between;

    margin-bottom: 17px;
}


.stat-icon {

    width: 43px;

    height: 43px;

    border-radius: 11px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;
}




.stat-icon.green {

    background:

        linear-gradient(
            135deg,
            #10b981,
            #059669
        );
}


.stat-icon.blue {

    background:

        linear-gradient(
            135deg,
            #3b82f6,
            #2563eb
        );
}


.stat-icon.orange {

    background:

        linear-gradient(
            135deg,
            #f59e0b,
            #d97706
        );
}


.stat-icon.purple {

    background:

        linear-gradient(
            135deg,
            #a855f7,
            #9333ea
        );
}


.stat-number {

    font-size: 27px;

    font-weight: 800;

    margin-bottom: 5px;
}


.stat-title {

    font-size: 11px;

    color: #64748b;

    font-weight: 600;
}


.stat-link {

    margin-top: 12px;

    color: var(--primary);

    font-size: 10px;
}




.box {

    border-radius: 17px;

    padding: 22px;

    backdrop-filter: blur(15px);
}


.box-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 20px;
}


.box-title {

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 16px;

    font-weight: 700;
}


.box-title i {

    color: var(--primary);
}




.btn-add {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 10px 15px;

    border-radius: 10px;

    background:

        linear-gradient(
            135deg,
            #6366f1,
            #4f46e5
        );

    color: white;

    text-decoration: none;

    font-size: 12px;

    font-weight: 600;

    transition: 0.25s;
}


.btn-add:hover {

    transform: translateY(-2px);

    box-shadow:
        0 7px 18px rgba(99,102,241,0.25);
}




.table-wrapper {

    width: 100%;

    overflow-x: auto;
}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 950px;
}


th {

    padding: 13px 12px;

    text-align: left;

    font-size: 10px;

    text-transform: uppercase;

    letter-spacing: 0.7px;

    font-weight: 700;
}


td {

    padding: 14px 12px;

    font-size: 12px;
}


tbody tr {

    transition: 0.2s;
}


tbody tr:hover {

    background:
        rgba(99,102,241,0.07);
}



.client-cell {

    display: flex;

    align-items: center;

    gap: 11px;
}


.avatar {

    width: 40px;

    height: 40px;

    border-radius: 11px;

    background:

        linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    font-size: 12px;

    font-weight: 700;

    flex-shrink: 0;
}


.client-name {

    font-weight: 700;

    font-size: 12px;
}


.client-email {

    font-size: 10px;

    color: #64748b;

    margin-top: 3px;
}



.plan-name {

    font-weight: 700;

    font-size: 12px;
}


.plan-icon {

    width: 35px;

    height: 35px;

    border-radius: 9px;

    background:
        rgba(99,102,241,0.12);

    color: var(--primary);

    display: inline-flex;

    align-items: center;

    justify-content: center;

    margin-right: 8px;
}




.usage {

    min-width: 120px;
}


.usage-text {

    display: flex;

    justify-content: space-between;

    font-size: 10px;

    margin-bottom: 6px;

    color: #64748b;
}


.progress {

    width: 100%;

    height: 6px;

    background:
        rgba(100,116,139,0.15);

    border-radius: 10px;

    overflow: hidden;
}


.progress-bar {

    height: 100%;

    border-radius: 10px;

    background:

        linear-gradient(
            90deg,
            #6366f1,
            #8b5cf6
        );
}




.date-expiration {

    font-weight: 600;

    font-size: 12px;
}


.date-expiration small {

    display: block;

    margin-top: 4px;

    font-size: 10px;

    color: #64748b;
}



.badge {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 10px;

    font-weight: 700;
}


.badge-actif {

    background:
        rgba(16,185,129,0.12);

    color: #10b981;
}


.badge-expire_bientot {

    background:
        rgba(245,158,11,0.12);

    color: #f59e0b;
}


.badge-expire {

    background:
        rgba(239,68,68,0.12);

    color: #ef4444;
}



.actions {

    display: flex;

    gap: 7px;
}


.btn-action {

    width: 32px;

    height: 32px;

    border-radius: 9px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    text-decoration: none;

    transition: 0.2s;

    font-size: 11px;
}




.btn-edit {

    background:
        rgba(245,158,11,0.12);

    color: #f59e0b;
}


.btn-edit:hover {

    background: #f59e0b;

    color: white;
}




.btn-delete {

    background:
        rgba(239,68,68,0.12);

    color: #ef4444;
}


.btn-delete:hover {

    background: #ef4444;

    color: white;
}




.alert {

    padding: 13px 16px;

    border-radius: 11px;

    margin-bottom: 20px;

    background:
        rgba(16,185,129,0.10);

    border:
        1px solid rgba(16,185,129,0.25);

    color: #10b981;

    font-size: 12px;

    display: flex;

    align-items: center;

    gap: 9px;
}


.empty {

    text-align: center;

    padding: 70px 20px;

    color: #64748b;
}


.empty i {

    font-size: 42px;

    margin-bottom: 15px;

    opacity: 0.5;
}


.empty p {

    font-size: 13px;

    margin-bottom: 5px;
}


.empty small {

    font-size: 11px;
}




@media(max-width:1200px) {

    .stats-grid {

        grid-template-columns:
            repeat(2,1fr);
    }
}


@media(max-width:850px) {

    .sidebar {

        width: 70px;

        padding: 15px 10px;
    }


    .logo span,
    .nav-title,
    .sidebar a span {

        display: none;
    }


    .logo {

        justify-content: center;
    }


    .sidebar a {

        justify-content: center;
    }


    .content {

        margin-left: 70px;

        width:
            calc(100% - 70px);

        padding: 18px;
    }


    .date-box {

        display: none;
    }
}


@media(max-width:600px) {

    .stats-grid {

        grid-template-columns: 1fr;
    }


    .topbar {

        padding: 15px;
    }


    .welcome h1 {

        font-size: 18px;
    }


    .btn-add {

        padding: 9px 11px;
    }


    .box {

        padding: 15px;
    }
}

</style>

</head>


<body>


<aside class="sidebar">


    <div class="logo">

        <div class="logo-icon">

            <i class="fa-solid fa-cloud"></i>

        </div>

        <span>HostManager</span>

    </div>


    <div class="nav-title">
        MENU PRINCIPAL
    </div>


    <a href="../administration/tableau_bord.php">

        <i class="fa-solid fa-gauge-high"></i>

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

</aside>





<main class="content">




    <div class="topbar">


        <div class="welcome">

            <h1>

                <?= $bonjour ?> 👋

            </h1>

            <p>

                Gérez vos hébergements et vos ressources depuis cet espace.

            </p>

        </div>


        <div class="top-actions">


            <div class="date-box">

                <i class="fa-regular fa-calendar"></i>

                <?= $aujourdhui ?>

            </div>


            <a
                href="../notifications/alertes.php"
                class="notification"
            >

                <i class="fa-regular fa-bell"></i>

            </a>

        </div>

    </div>




    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>

        <div class="alert">

            <i class="fa-solid fa-circle-check"></i>

            Hébergement supprimé avec succès

        </div>

    <?php endif; ?>


    <div class="stats-grid">



        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon green">

                    <i class="fa-solid fa-server"></i>

                </div>

            </div>


            <div class="stat-number">

                <?= $total_hebergements ?>

            </div>


            <div class="stat-title">

                Total hébergements

            </div>


            <div class="stat-link">

                <i class="fa-solid fa-database"></i>

                Tous les hébergements

            </div>

        </div>





        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon blue">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

            </div>


            <div class="stat-number">

                <?= $total_actifs ?>

            </div>


            <div class="stat-title">

                Hébergements actifs

            </div>


            <div class="stat-link">

                <i class="fa-solid fa-arrow-up"></i>

                Services actifs

            </div>

        </div>



  

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon orange">

                    <i class="fa-solid fa-clock"></i>

                </div>

            </div>


            <div class="stat-number">

                <?= $total_bientot ?>

            </div>


            <div class="stat-title">

                Expirent bientôt

            </div>


            <div class="stat-link">

                <i class="fa-solid fa-hourglass-half"></i>

                Dans les 30 jours

            </div>

        </div>





        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon purple">

                    <i class="fa-solid fa-circle-xmark"></i>

                </div>

            </div>


            <div class="stat-number">

                <?= $total_expires ?>

            </div>


            <div class="stat-title">

                Hébergements expirés

            </div>


            <div class="stat-link">

                <i class="fa-solid fa-triangle-exclamation"></i>

                À renouveler

            </div>

        </div>


    </div>


    <div class="box">


        <div class="box-header">


            <div class="box-title">

                <i class="fa-solid fa-server"></i>

                Liste des hébergements

            </div>


            <a
                href="ajouter.php"
                class="btn-add"
            >

                <i class="fa-solid fa-plus"></i>

                Nouvel hébergement

            </a>

        </div>



        <?php if(empty($hebergements)): ?>


            <div class="empty">

                <i class="fa-solid fa-server"></i>

                <p>

                    Aucun hébergement enregistré

                </p>

                <small>

                    Commencez par ajouter votre premier hébergement.

                </small>

            </div>


        <?php else: ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>Plan</th>

                            <th>Client</th>

                            <th>Espace disque</th>

                            <th>Bande passante</th>

                            <th>Date expiration</th>

                            <th>Statut</th>

                            <th>Actions</th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php foreach($hebergements as $h): ?>


                        <?php

                   

                        $nom = $h['full_name'] ?? 'Client supprimé';

                        $parts = preg_split('/\s+/', trim($nom));

                        $initials =
                            strtoupper(
                                substr($parts[0] ?? '', 0, 1) .
                                substr($parts[1] ?? '', 0, 1)
                            );

                        if ($initials == '') {
                            $initials = '?';
                        }


                        

                        $date_expiration = new DateTime(
                            $h['date_expiration']
                        );

                        $aujourd_hui = new DateTime();

                        $difference =
                            $aujourd_hui->diff($date_expiration);

                        $jours =
                            (int) $difference->format('%r%a');


                       

                        $espace =
                            (float) ($h['espace'] ?? 0);

                        $espace_percent =
                            min(
                                ($espace / 10000) * 100,
                                100
                            );


                        

                        $bande_passante =
                            (float) ($h['bande_passante'] ?? 0);

                        $bande_percent =
                            min(
                                ($bande_passante / 100000) * 100,
                                100
                            );

                        ?>


                        <tr>



                            <td>

                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                    "
                                >

                                    <div class="plan-icon">

                                        <i class="fa-solid fa-server"></i>

                                    </div>


                                    <div>

                                        <div class="plan-name">

                                            <?= htmlspecialchars(
                                                $h['plan'] ?? ''
                                            ) ?>

                                        </div>

                                    </div>

                                </div>

                            </td>



            

                            <td>

                                <div class="client-cell">


                                    <div class="avatar">

                                        <?= htmlspecialchars(
                                            $initials
                                        ) ?>

                                    </div>


                                    <div>


                                        <div class="client-name">

                                            <?= htmlspecialchars(
                                                $nom
                                            ) ?>

                                        </div>


                                        <div class="client-email">

                                            <?= htmlspecialchars(
                                                $h['email'] ?? ''
                                            ) ?>

                                        </div>


                                    </div>


                                </div>

                            </td>




                            <td>

                                <div class="usage">


                                    <div class="usage-text">

                                        <span>

                                            <?= number_format(
                                                $espace,
                                                0
                                            ) ?> MB

                                        </span>

                                        <span>

                                            <?= number_format(
                                                $espace_percent,
                                                0
                                            ) ?>%

                                        </span>

                                    </div>


                                    <div class="progress">

                                        <div
                                            class="progress-bar"
                                            style="
                                                width:
                                                <?= $espace_percent ?>%;
                                            "
                                        ></div>

                                    </div>


                                </div>

                            </td>





                            <td>

                                <div class="usage">


                                    <div class="usage-text">

                                        <span>

                                            <?= number_format(
                                                $bande_passante,
                                                0
                                            ) ?> MB

                                        </span>

                                        <span>

                                            <?= number_format(
                                                $bande_percent,
                                                0
                                            ) ?>%

                                        </span>

                                    </div>


                                    <div class="progress">

                                        <div
                                            class="progress-bar"
                                            style="
                                                width:
                                                <?= $bande_percent ?>%;
                                            "
                                        ></div>

                                    </div>


                                </div>

                            </td>



              

                            <td>

                                <div class="date-expiration">

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $h['date_expiration']
                                        )
                                    ) ?>


                                    <?php if($jours < 0): ?>

                                        <small>

                                            Expiré depuis
                                            <?= abs($jours) ?> jour(s)

                                        </small>


                                    <?php elseif($jours == 0): ?>

                                        <small>

                                            Expire aujourd'hui

                                        </small>


                                    <?php else: ?>

                                        <small>

                                            Dans <?= $jours ?> jour(s)

                                        </small>

                                    <?php endif; ?>

                                </div>

                            </td>



                         

                            <td>


                                <?php if(
                                    $h['statut_auto'] === 'actif'
                                ): ?>


                                    <span
                                        class="badge badge-actif"
                                    >

                                        <i
                                            class="fa-solid fa-circle-check"
                                        ></i>

                                        Actif

                                    </span>


                                <?php elseif(
                                    $h['statut_auto'] === 'expire_bientot'
                                ): ?>


                                    <span
                                        class="badge badge-expire_bientot"
                                    >

                                        <i
                                            class="fa-solid fa-clock"
                                        ></i>

                                        Expire bientôt

                                    </span>


                                <?php else: ?>


                                    <span
                                        class="badge badge-expire"
                                    >

                                        <i
                                            class="fa-solid fa-circle-xmark"
                                        ></i>

                                        Expiré

                                    </span>


                                <?php endif; ?>


                            </td>



                  

                            <td>


                                <div class="actions">


                          

                                    <a
                                        href="modifier.php?id=<?= $h['id'] ?>"
                                        class="btn-action btn-edit"
                                        title="Modifier"
                                    >

                                        <i
                                            class="fa-solid fa-pen"
                                        ></i>

                                    </a>




                                    <a
                                        href="liste.php?delete=<?= $h['id'] ?>"
                                        class="btn-action btn-delete"
                                        title="Supprimer"

                                        onclick="
                                            return confirm(
                                                'Êtes-vous sûr de vouloir supprimer cet hébergement ?'
                                            )
                                        "
                                    >

                                        <i
                                            class="fa-solid fa-trash"
                                        ></i>

                                    </a>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>


            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>