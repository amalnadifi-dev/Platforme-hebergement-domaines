<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: liste.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("
    SELECT c.*,
           COUNT(DISTINCT d.id) AS total_domaines,
           COUNT(DISTINCT h.id) AS total_hebergements
    FROM clients c
    LEFT JOIN domaines d ON c.id = d.id_client
    LEFT JOIN hebergements h ON c.id = h.id_client
    GROUP BY c.id
    ORDER BY c.id DESC
");

$clients = $stmt->fetchAll();

$total_clients = count($clients);

$total_domaines_clients = array_sum(array_column($clients, 'total_domaines'));

$total_hebergements_clients = array_sum(array_column($clients, 'total_hebergements'));

$clients_actifs = 0;
$clients_inactifs = 0;

foreach ($clients as $client) {
    if (($client['statut'] ?? 'actif') === 'actif') {
        $clients_actifs++;
    } else {
        $clients_inactifs++;
    }
}

$aujourdhui = date('d/m/Y');
$heure = date('H');

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>HostManager - Clients</title>

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
        linear-gradient(rgba(15,23,42,0.90), rgba(15,23,42,0.94)),
        url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.card,
.box,
.topbar {
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
    box-shadow: 2px 0 10px rgba(0,0,0,0.05);
}

.content {
    background:
        linear-gradient(rgba(248,250,252,0.93), rgba(241,245,249,0.96)),
        url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.card,
.box,
.topbar {
    background: rgba(255,255,255,0.90);
    border: 1px solid #e2e8f0;
    box-shadow: 0 5px 20px rgba(15,23,42,0.05);
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
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
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
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    transform: translateX(3px);
}

.sidebar a.active {
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    color: white;
    box-shadow: 0 7px 18px rgba(99,102,241,0.25);
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
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    text-decoration: none;
}


.stats-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
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

.stat-icon.blue {
    background: linear-gradient(135deg,#3b82f6,#2563eb);
}

.stat-icon.orange {
    background: linear-gradient(135deg,#f59e0b,#d97706);
}

.stat-icon.purple {
    background: linear-gradient(135deg,#a855f7,#9333ea);
}

.stat-icon.green {
    background: linear-gradient(135deg,#10b981,#059669);
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
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    color: white;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: 0.25s;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 18px rgba(99,102,241,0.25);
}


.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
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
    background: rgba(99,102,241,0.07);
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
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: 700;
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


.count-badge {
    min-width: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 9px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 700;
}

.count-domaines {
    background: rgba(245,158,11,0.12);
    color: #f59e0b;
}

.count-hebergements {
    background: rgba(168,85,247,0.12);
    color: #a855f7;
}


.status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
}

.status.active {
    background: rgba(16,185,129,0.12);
    color: #10b981;
}

.status.inactive {
    background: rgba(239,68,68,0.12);
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
    background: rgba(245,158,11,0.12);
    color: #f59e0b;
}

.btn-edit:hover {
    background: #f59e0b;
    color: white;
}

.btn-delete {
    background: rgba(239,68,68,0.12);
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
    background: rgba(16,185,129,0.10);
    border: 1px solid rgba(16,185,129,0.25);
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
        grid-template-columns: repeat(2,1fr);
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
        width: calc(100% - 70px);
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


    <a href="liste.php" class="active">
        <i class="fa-solid fa-users"></i>
        <span>Clients</span>
    </a>


    <a href="../domaines/liste.php">
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

        <a href="../administration/deconnexion.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>

    </div>

</aside>


<main class="content">


    <div class="topbar">

        <div class="welcome">

            <h1><?= $bonjour ?> 👋</h1>

            <p>
                Gérez vos clients et leurs services depuis cet espace.
            </p>

        </div>


        <div class="top-actions">

            <div class="date-box">

                <i class="fa-regular fa-calendar"></i>

                <?= $aujourdhui ?>

            </div>


            <a href="../notifications/alertes.php" class="notification">

                <i class="fa-regular fa-bell"></i>

            </a>

        </div>

    </div>


    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>

        <div class="alert">

            <i class="fa-solid fa-circle-check"></i>

            Client supprimé avec succès

        </div>

    <?php endif; ?>


    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon blue">
                    <i class="fa-solid fa-users"></i>
                </div>

            </div>

            <div class="stat-number">
                <?= $total_clients ?>
            </div>

            <div class="stat-title">
                Total clients
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-user-check"></i>
                <?= $clients_actifs ?> clients actifs
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon orange">
                    <i class="fa-solid fa-globe"></i>
                </div>

            </div>

            <div class="stat-number">
                <?= $total_domaines_clients ?>
            </div>

            <div class="stat-title">
                Domaines
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-arrow-up"></i>
                Services associés
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon purple">
                    <i class="fa-solid fa-server"></i>
                </div>

            </div>

            <div class="stat-number">
                <?= $total_hebergements_clients ?>
            </div>

            <div class="stat-title">
                Hébergements
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-server"></i>
                Services actifs
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>

            </div>

            <div class="stat-number">
                <?= $clients_actifs ?>
            </div>

            <div class="stat-title">
                Clients actifs
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-circle-check"></i>
                <?= $clients_inactifs ?> inactifs
            </div>

        </div>


    </div>


    <div class="box">


        <div class="box-header">


            <div class="box-title">

                <i class="fa-solid fa-users"></i>

                Liste des clients

            </div>


            <a href="ajouter.php" class="btn-add">

                <i class="fa-solid fa-plus"></i>

                Nouveau client

            </a>


        </div>


        <?php if(empty($clients)): ?>


            <div class="empty">

                <i class="fa-solid fa-user-slash"></i>

                <p>Aucun client pour le moment</p>

                <small>
                    Commencez par ajouter votre premier client.
                </small>

            </div>


        <?php else: ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>Client</th>

                            <th>Téléphone</th>

                            <th>Domaines</th>

                            <th>Hébergements</th>

                            <th>Statut</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach($clients as $c): ?>


                        <?php

                        $telephone = $c['telephone'] ?? $c['phone'] ?? 'N/A';

                        $statut = $c['statut'] ?? 'actif';

                        $is_active = $statut === 'actif';

                        $nom_parts = preg_split('/\s+/', trim($c['full_name']));

                        $initials = strtoupper(
                            substr($nom_parts[0] ?? '',0,1) .
                            substr($nom_parts[1] ?? '',0,1)
                        );

                        if($initials == '') {
                            $initials = '?';
                        }

                        ?>


                        <tr>


                            <td>

                                <div class="client-cell">


                                    <div class="avatar">

                                        <?= htmlspecialchars($initials) ?>

                                    </div>


                                    <div>

                                        <div class="client-name">

                                            <?= htmlspecialchars($c['full_name']) ?>

                                        </div>


                                        <div class="client-email">

                                            <?= htmlspecialchars($c['email']) ?>

                                        </div>

                                    </div>


                                </div>

                            </td>


                            <td>

                                <?= htmlspecialchars($telephone) ?>

                            </td>


                            <td>

                                <span class="count-badge count-domaines">

                                    <?= $c['total_domaines'] ?>

                                </span>

                            </td>


                            <td>

                                <span class="count-badge count-hebergements">

                                    <?= $c['total_hebergements'] ?>

                                </span>

                            </td>


                            <td>

                                <?php if($is_active): ?>

                                    <span class="status active">

                                        <i class="fa-solid fa-circle"></i>

                                        Actif

                                    </span>

                                <?php else: ?>

                                    <span class="status inactive">

                                        <i class="fa-solid fa-circle"></i>

                                        Inactif

                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>


                                <div class="actions">


                                    <a
                                        href="modifier.php?id=<?= $c['id'] ?>"
                                        class="btn-action btn-edit"
                                        title="Modifier"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <a
                                        href="liste.php?delete=<?= $c['id'] ?>"
                                        class="btn-action btn-delete"
                                        title="Supprimer"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')"
                                    >

                                        <i class="fa-solid fa-trash"></i>

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