<?php
require_once("auth.php");
require_once("../configuration/base_donnees.php");

if (isset($_GET['theme'])) {
    if ($_GET['theme'] === 'light') {
        $_SESSION['theme'] = 'light';
    } elseif ($_GET['theme'] === 'dark') {
        $_SESSION['theme'] = 'dark';
    }

    header("Location: tableau_bord.php");
    exit;
}

$theme = $_SESSION['theme'] ?? 'dark';

$total_clients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();

$total_domaines = $pdo->query("SELECT COUNT(*) FROM domaines")->fetchColumn();

$total_hebergements = $pdo->query("SELECT COUNT(*) FROM hebergements")->fetchColumn();

$domaines_actifs = $pdo->query("
    SELECT COUNT(*) 
    FROM domaines 
    WHERE date_expiration > NOW()
")->fetchColumn();

$domaines_expires = $pdo->query("
    SELECT COUNT(*) 
    FROM domaines 
    WHERE date_expiration < NOW()
")->fetchColumn();

$domaines_bientot = $pdo->query("
    SELECT COUNT(*) 
    FROM domaines 
    WHERE date_expiration BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
")->fetchColumn();

$hebergements_actifs = $pdo->query("
    SELECT COUNT(*) 
    FROM hebergements 
    WHERE date_expiration > NOW()
")->fetchColumn();

$hebergements_expires = $pdo->query("
    SELECT COUNT(*) 
    FROM hebergements 
    WHERE date_expiration < NOW()
")->fetchColumn();

$hebergements_bientot = $pdo->query("
    SELECT COUNT(*) 
    FROM hebergements 
    WHERE date_expiration BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
")->fetchColumn();

$total_paiements = 0;

try {
    $total_paiements = $pdo->query("SELECT COUNT(*) FROM paiements")->fetchColumn();
} catch (Exception $e) {
    $total_paiements = 0;
}

$total_alertes = $domaines_bientot + $domaines_expires + $hebergements_bientot + $hebergements_expires;

$derniers_clients = $pdo->query("
    SELECT * 
    FROM clients 
    ORDER BY id DESC 
    LIMIT 5
")->fetchAll();

$activites_clients = $pdo->query("
    SELECT id, full_name, email
    FROM clients
    ORDER BY id DESC
    LIMIT 3
")->fetchAll();

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
<title>HostManager - Tableau de bord</title>

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
    transition: 0.3s;
}

<?php if ($theme == 'dark'): ?>

body {
    color: #e2e8f0;
    background: #0f172a;
}

.sidebar {
    background: rgba(15, 23, 42, 0.96);
    border-right: 1px solid #334155;
}

.content {
    background:
        linear-gradient(rgba(15,23,42,0.90), rgba(15,23,42,0.94)),
        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.card,
.box,
.topbar {
    background: rgba(30,41,59,0.82);
    border: 1px solid #334155;
}

.stat-title,
.muted {
    color: #94a3b8;
}

.activity-text {
    color: #cbd5e1;
}

.quick-action {
    color: #e2e8f0;
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
        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop')
        center/cover fixed no-repeat;
}

.card,
.box,
.topbar {
    background: rgba(255,255,255,0.90);
    border: 1px solid #e2e8f0;
    box-shadow: 0 5px 20px rgba(15,23,42,0.05);
}

.stat-title,
.muted {
    color: #64748b;
}

.activity-text {
    color: #475569;
}

.quick-action {
    color: #0f172a;
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
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 19px;
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
    font-size: 15px;
}

.sidebar a:hover {
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    transform: translateX(3px);
}

.sidebar a.active {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
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
    gap: 12px;
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
    position: relative;
    text-decoration: none;
}

.notification span {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: var(--red);
    color: white;
    font-size: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.theme-toggle {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    text-decoration: none;
    transition: 0.25s;
}

.theme-toggle:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    margin-bottom: 22px;
}

.stat-card {
    padding: 20px;
    border-radius: 16px;
    position: relative;
    overflow: hidden;
    transition: 0.25s;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
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

.stat-icon.red {
    background: linear-gradient(135deg,#ef4444,#dc2626);
}

.stat-icon.indigo {
    background: linear-gradient(135deg,#6366f1,#4f46e5);
}

.stat-number {
    font-size: 27px;
    font-weight: 800;
    margin-bottom: 5px;
}

.stat-title {
    font-size: 11px;
    font-weight: 600;
}

.stat-link {
    margin-top: 13px;
    font-size: 10px;
    color: var(--primary);
}

.main-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
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

.view-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
}

.chart-area {
    display: flex;
    align-items: center;
    gap: 35px;
    min-height: 210px;
}

.donut {
    width: 175px;
    height: 175px;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}

.donut::after {
    content: "";
    position: absolute;
    inset: 30px;
    border-radius: 50%;
}

<?php if ($theme == 'dark'): ?>

.donut::after {
    background: #1e293b;
}

<?php else: ?>

.donut::after {
    background: #ffffff;
}

<?php endif; ?>

.donut-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.donut-center strong {
    font-size: 27px;
}

.donut-center span {
    color: #64748b;
    font-size: 10px;
}

.legend {
    display: flex;
    flex-direction: column;
    gap: 17px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 12px;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.legend-dot.green {
    background: #10b981;
}

.legend-dot.orange {
    background: #f59e0b;
}

.legend-dot.red {
    background: #ef4444;
}

.legend-number {
    margin-left: auto;
    font-weight: 700;
}

.alert-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px;
    border-radius: 11px;
}

.alert-item.red {
    background: rgba(239,68,68,0.10);
}

.alert-item.orange {
    background: rgba(245,158,11,0.10);
}

.alert-item.green {
    background: rgba(16,185,129,0.10);
}

.alert-icon {
    width: 37px;
    height: 37px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.alert-item.red .alert-icon {
    color: #ef4444;
    background: rgba(239,68,68,0.15);
}

.alert-item.orange .alert-icon {
    color: #f59e0b;
    background: rgba(245,158,11,0.15);
}

.alert-item.green .alert-icon {
    color: #10b981;
    background: rgba(16,185,129,0.15);
}

.alert-content strong {
    display: block;
    font-size: 12px;
    margin-bottom: 3px;
}

.alert-content span {
    font-size: 10px;
    color: #64748b;
}

.clients-box {
    min-height: 290px;
}

.client-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px;
    border-radius: 10px;
    transition: 0.2s;
}

.client-item:hover {
    background: rgba(99,102,241,0.08);
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
    font-weight: 700;
    font-size: 13px;
}

.client-info {
    flex: 1;
}

.client-info strong {
    display: block;
    font-size: 12px;
    margin-bottom: 3px;
}

.client-info span {
    font-size: 10px;
    color: #64748b;
}

.client-status {
    font-size: 9px;
    padding: 5px 8px;
    border-radius: 20px;
    background: rgba(16,185,129,0.12);
    color: #10b981;
    font-weight: 700;
}

.activity-list {
    display: flex;
    flex-direction: column;
}

.activity {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(148,163,184,0.12);
}

.activity:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 35px;
    height: 35px;
    border-radius: 9px;
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-text strong {
    font-size: 11px;
    display: block;
    margin-bottom: 3px;
}

.activity-text span {
    font-size: 10px;
    color: #64748b;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 12px;
}

.quick-action {
    padding: 16px;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: 0.25s;
    border: 1px solid rgba(148,163,184,0.15);
}

.quick-action:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
}

.quick-action-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: rgba(99,102,241,0.10);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-action strong {
    font-size: 11px;
    display: block;
}

.quick-action span {
    font-size: 9px;
    color: #64748b;
}

.section-space {
    margin-bottom: 20px;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #64748b;
}

.empty i {
    font-size: 30px;
    margin-bottom: 10px;
}

@media(max-width:1250px) {
    .stats-grid {
        grid-template-columns: repeat(3,1fr);
    }

    .main-grid {
        grid-template-columns: 1fr;
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
        padding-bottom: 25px;
    }

    .sidebar a {
        justify-content: center;
    }

    .sidebar a i {
        margin: 0;
    }

    .content {
        margin-left: 70px;
        width: calc(100% - 70px);
        padding: 18px;
    }

    .topbar {
        align-items: flex-start;
    }

    .date-box {
        display: none;
    }

    .quick-actions {
        grid-template-columns: repeat(2,1fr);
    }
}

@media(max-width:600px) {
    .stats-grid {
        grid-template-columns: repeat(2,1fr);
    }

    .chart-area {
        flex-direction: column;
        justify-content: center;
    }

    .quick-actions {
        grid-template-columns: 1fr;
    }

    .welcome h1 {
        font-size: 18px;
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

    <div class="nav-title">MENU PRINCIPAL</div>

    <a href="tableau_bord.php" class="active">
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

        <a href="deconnexion.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>

    </div>

</aside>


<main class="content">

    <div class="topbar">

        <div class="welcome">
            <h1><?= $bonjour ?> 👋</h1>
            <p>Voici l'état actuel de votre infrastructure HostManager.</p>
        </div>

        <div class="top-actions">

            <div class="date-box">
                <i class="fa-regular fa-calendar"></i>
                <?= $aujourdhui ?>
            </div>

            <?php if ($theme == 'dark'): ?>

                <a
                    href="tableau_bord.php?theme=light"
                    class="theme-toggle"
                    title="Mode clair"
                >
                <i class="fa-solid fa-sun"></i>

                </a>

            <?php else: ?>

                <a
                    href="tableau_bord.php?theme=dark"
                    class="theme-toggle"
                    title="Mode sombre"
                >
                <i class="fa-solid fa-moon"></i>
                   
                </a>

            <?php endif; ?>

            <a href="../notifications/alertes.php" class="notification">
                <i class="fa-regular fa-bell"></i>

                <?php if ($total_alertes > 0): ?>
                    <span><?= $total_alertes > 99 ? '99+' : $total_alertes ?></span>
                <?php endif; ?>

            </a>

        </div>

    </div>


    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <div class="stat-number"><?= $total_clients ?></div>
            <div class="stat-title">Total clients</div>

            <div class="stat-link">
                <i class="fa-solid fa-arrow-up"></i>
                Clients enregistrés
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-globe"></i>
                </div>
            </div>

            <div class="stat-number"><?= $total_domaines ?></div>
            <div class="stat-title">Total domaines</div>

            <div class="stat-link">
                <?= $domaines_actifs ?> actifs
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>

            <div class="stat-number"><?= $total_hebergements ?></div>
            <div class="stat-title">Hébergements</div>

            <div class="stat-link">
                <?= $hebergements_actifs ?> actifs
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="stat-number"><?= $domaines_actifs ?></div>
            <div class="stat-title">Domaines actifs</div>

            <div class="stat-link">
                État normal
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <div class="stat-number"><?= $domaines_bientot ?></div>
            <div class="stat-title">Expiration 30 jours</div>

            <div class="stat-link">
                Attention requise
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon indigo">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
            </div>

            <div class="stat-number"><?= $total_paiements ?></div>
            <div class="stat-title">Paiements</div>

            <div class="stat-link">
                Transactions enregistrées
            </div>

        </div>

    </div>


    <div class="main-grid">

        <div class="box">

            <div class="box-header">

                <div class="box-title">
                    <i class="fa-solid fa-chart-pie"></i>
                    État des domaines
                </div>

                <a href="../domaines/liste.php" class="view-link">
                    Voir tout
                </a>

            </div>

            <?php

            $total_graph = max((int)$total_domaines, 1);

            $percent_actifs = round(($domaines_actifs / $total_graph) * 100);

            $percent_bientot = round(($domaines_bientot / $total_graph) * 100);

            $percent_expires = round(($domaines_expires / $total_graph) * 100);

            $deg1 = $percent_actifs * 3.6;

            $deg2 = $percent_bientot * 3.6;

            ?>


            <div class="chart-area">

                <div
                    class="donut"
                    style="
                    background:
                    conic-gradient(
                        #10b981 0deg <?= $deg1 ?>deg,
                        #f59e0b <?= $deg1 ?>deg <?= $deg1 + $deg2 ?>deg,
                        #ef4444 <?= $deg1 + $deg2 ?>deg 360deg
                    );
                    "
                >

                    <div class="donut-center">
                        <strong><?= $total_domaines ?></strong>
                        <span>Domaines</span>
                    </div>

                </div>


                <div class="legend">

                    <div class="legend-item">
                        <span class="legend-dot green"></span>
                        Actifs
                        <span class="legend-number"><?= $domaines_actifs ?></span>
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot orange"></span>
                        Expirent bientôt
                        <span class="legend-number"><?= $domaines_bientot ?></span>
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot red"></span>
                        Expirés
                        <span class="legend-number"><?= $domaines_expires ?></span>
                    </div>

                </div>

            </div>

        </div>


        <div class="box">

            <div class="box-header">

                <div class="box-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    Centre des alertes
                </div>

                <a href="../notifications/alertes.php" class="view-link">
                    Voir tout
                </a>

            </div>


            <div class="alert-list">

                <?php if ($domaines_expires > 0): ?>

                    <div class="alert-item red">

                        <div class="alert-icon">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>

                        <div class="alert-content">
                            <strong><?= $domaines_expires ?> domaine(s) expiré(s)</strong>
                            <span>Action recommandée</span>
                        </div>

                    </div>

                <?php endif; ?>


                <?php if ($domaines_bientot > 0): ?>

                    <div class="alert-item orange">

                        <div class="alert-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>

                        <div class="alert-content">
                            <strong><?= $domaines_bientot ?> domaine(s) bientôt expiré(s)</strong>
                            <span>Expiration dans moins de 30 jours</span>
                        </div>

                    </div>

                <?php endif; ?>


                <?php if ($hebergements_expires > 0): ?>

                    <div class="alert-item red">

                        <div class="alert-icon">
                            <i class="fa-solid fa-server"></i>
                        </div>

                        <div class="alert-content">
                            <strong><?= $hebergements_expires ?> hébergement(s) expiré(s)</strong>
                            <span>Vérifiez les renouvellements</span>
                        </div>

                    </div>

                <?php endif; ?>


                <?php if ($hebergements_bientot > 0): ?>

                    <div class="alert-item orange">

                        <div class="alert-icon">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>

                        <div class="alert-content">
                            <strong><?= $hebergements_bientot ?> hébergement(s) bientôt expiré(s)</strong>
                            <span>Moins de 30 jours restants</span>
                        </div>

                    </div>

                <?php endif; ?>


                <?php if ($total_alertes == 0): ?>

                    <div class="alert-item green">

                        <div class="alert-icon">
                            <i class="fa-solid fa-check"></i>
                        </div>

                        <div class="alert-content">
                            <strong>Tout est en ordre</strong>
                            <span>Aucune expiration urgente détectée</span>
                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <div class="main-grid section-space">

        <div class="box clients-box">

            <div class="box-header">

                <div class="box-title">
                    <i class="fa-solid fa-user-plus"></i>
                    Derniers clients
                </div>

                <a href="../clients/liste.php" class="view-link">
                    Voir tous
                </a>

            </div>


            <?php if (empty($derniers_clients)): ?>

                <div class="empty">
                    <i class="fa-solid fa-user-slash"></i>
                    <p>Aucun client enregistré</p>
                </div>

            <?php else: ?>

                <?php foreach ($derniers_clients as $client): ?>

                    <?php

                    $nom = trim($client['full_name']);

                    $parts = preg_split('/\s+/', $nom);

                    $initials = strtoupper(
                        substr($parts[0] ?? '', 0, 1) .
                        substr($parts[1] ?? '', 0, 1)
                    );

                    if ($initials == '') {
                        $initials = '?';
                    }

                    ?>

                    <div class="client-item">

                        <div class="avatar">
                            <?= htmlspecialchars($initials) ?>
                        </div>

                        <div class="client-info">

                            <strong>
                                <?= htmlspecialchars($client['full_name']) ?>
                            </strong>

                            <span>
                                <?= htmlspecialchars($client['email']) ?>
                            </span>

                        </div>

                        <div class="client-status">
                            Client
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


        <div class="box">

            <div class="box-header">

                <div class="box-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Activité récente
                </div>

            </div>


            <div class="activity-list">

                <?php if (!empty($activites_clients)): ?>

                    <?php foreach ($activites_clients as $client): ?>

                        <div class="activity">

                            <div class="activity-icon">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>

                            <div class="activity-text">

                                <strong>
                                    Nouveau client : <?= htmlspecialchars($client['full_name']) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars($client['email']) ?>
                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty">
                        <i class="fa-solid fa-clock"></i>
                        <p>Aucune activité récente</p>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>

</body>
</html>