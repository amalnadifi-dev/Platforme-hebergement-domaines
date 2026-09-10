<?php

require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';

$stmt_domaines = $pdo->query("
    SELECT 
        d.*,
        c.full_name,
        'domaine' AS type,
        DATEDIFF(d.date_expiration, CURDATE()) AS jours_restants
    FROM domaines d
    JOIN clients c ON d.id_client = c.id
    WHERE DATEDIFF(d.date_expiration, CURDATE()) <= 30
    ORDER BY d.date_expiration ASC
");

$domaines_alertes = $stmt_domaines->fetchAll();

$stmt_hebergements = $pdo->query("
    SELECT 
        h.*,
        c.full_name,
        'hebergement' AS type,
        DATEDIFF(h.date_expiration, CURDATE()) AS jours_restants
    FROM hebergements h
    JOIN clients c ON h.id_client = c.id
    WHERE DATEDIFF(h.date_expiration, CURDATE()) <= 30
    ORDER BY h.date_expiration ASC
");

$hebergements_alertes = $stmt_hebergements->fetchAll();

$alertes = array_merge(
    $domaines_alertes,
    $hebergements_alertes
);

usort($alertes, function ($a, $b) {
    return $a['jours_restants'] - $b['jours_restants'];
});

$total_critique = 0;
$total_warning = 0;

foreach ($alertes as $a) {

    if ($a['jours_restants'] < 0) {
        $total_critique++;
    } elseif ($a['jours_restants'] <= 7) {
        $total_critique++;
    } else {
        $total_warning++;
    }
}

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>HostManager - Alertes</title>

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
        url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2070&auto=format&fit=crop')
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
        url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2070&auto=format&fit=crop')
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

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
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

.type-cell {
    display: flex;
    align-items: center;
    gap: 11px;
}

.type-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.type-domaine {
    background:
        rgba(59,130,246,0.12);
    color: #3b82f6;
}

.type-hebergement {
    background:
        rgba(168,85,247,0.12);
    color: #a855f7;
}

.type-name {
    font-size: 12px;
    font-weight: 700;
}

.type-subtitle {
    font-size: 10px;
    color: #64748b;
    margin-top: 3px;
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

.date-expiration {
    font-size: 12px;
    font-weight: 700;
}

.jours {
    font-size: 13px;
    font-weight: 800;
}

.jours.expire {
    color: #ef4444;
}

.jours.urgent {
    color: #f59e0b;
}

.jours.warning {
    color: #ca8a04;
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

.badge-expire {
    background:
        rgba(239,68,68,0.12);
    color: #ef4444;
}

.badge-critique {
    background:
        rgba(245,158,11,0.12);
    color: #f59e0b;
}

.badge-warning {
    background:
        rgba(234,179,8,0.12);
    color: #ca8a04;
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

    <a href="../hebergements/liste.php">
        <i class="fa-solid fa-server"></i>
        <span>Hébergements</span>
    </a>

    <a href="../paiements/liste.php">
        <i class="fa-solid fa-credit-card"></i>
        <span>Paiements</span>
    </a>

    <a href="alertes.php" class="active">
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
                Surveillez vos domaines et hébergements depuis cet espace.
            </p>

        </div>

        <div class="top-actions">

            <div class="date-box">

                <i class="fa-regular fa-calendar"></i>

                <?= $aujourdhui ?>

            </div>

            <a
                href="alertes.php"
                class="notification"
                title="Alertes"
            >

                <i class="fa-regular fa-bell"></i>

            </a>

        </div>

    </div>

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-top">

                <div
                    class="stat-icon red"
                    style="
                    background:linear-gradient(
                        135deg,
                        #ef4444,
                        #dc2626
                    );
                    "
                >

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

            </div>

            <div class="stat-number">
                <?= $total_critique ?>
            </div>

            <div class="stat-title">
                Critiques / Expirés
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-circle-exclamation"></i>
                Action requise
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon orange">

                    <i class="fa-solid fa-clock"></i>

                </div>

            </div>

            <div class="stat-number">
                <?= $total_warning ?>
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

                <div class="stat-icon blue">

                    <i class="fa-solid fa-bell"></i>

                </div>

            </div>

            <div class="stat-number">
                <?= count($alertes) ?>
            </div>

            <div class="stat-title">
                Total alertes
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-list-check"></i>
                Services à surveiller
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon purple">

                    <i class="fa-solid fa-server"></i>

                </div>

            </div>

            <div class="stat-number">
                <?= count($domaines_alertes) + count($hebergements_alertes) ?>
            </div>

            <div class="stat-title">
                Services concernés
            </div>

            <div class="stat-link">
                <i class="fa-solid fa-globe"></i>
                Domaines & hébergements
            </div>

        </div>

    </div>

    <div class="box">

        <div class="box-header">

            <div class="box-title">

                <i class="fa-solid fa-bell"></i>

                Centre d'alertes

            </div>

        </div>

        <?php if (empty($alertes)): ?>

            <div class="empty">

                <i class="fa-solid fa-circle-check"></i>

                <p>
                    Aucune alerte pour le moment
                </p>

                <small>
                    Tous vos services sont à jour.
                </small>

            </div>

        <?php else: ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Type</th>
                            <th>Nom</th>
                            <th>Client</th>
                            <th>Date expiration</th>
                            <th>Jours restants</th>
                            <th>Statut</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($alertes as $a): ?>

                        <?php

                        $jours = (int) $a['jours_restants'];

                        if ($jours < 0) {

                            $statut_class = 'badge-expire';
                            $statut_text = 'Expiré';
                            $jours_class = 'expire';

                        } elseif ($jours <= 7) {

                            $statut_class = 'badge-critique';
                            $statut_text = 'Urgent';
                            $jours_class = 'urgent';

                        } else {

                            $statut_class = 'badge-warning';
                            $statut_text = 'Bientôt';
                            $jours_class = 'warning';

                        }

                        if ($a['type'] === 'domaine') {

                            $nom = $a['nom_domaine'];

                        } else {

                            $nom = $a['plan'];

                        }

                        $client_nom = $a['full_name'] ?? 'Client';

                        $parts = preg_split(
                            '/\s+/',
                            trim($client_nom)
                        );

                        $initials =
                            strtoupper(
                                substr(
                                    $parts[0] ?? '',
                                    0,
                                    1
                                ) .
                                substr(
                                    $parts[1] ?? '',
                                    0,
                                    1
                                )
                            );

                        if ($initials === '') {

                            $initials = '?';

                        }

                        ?>

                        <tr>

                            <td>

                                <div class="type-cell">

                                    <div
                                        class="
                                            type-icon
                                            <?= $a['type'] === 'domaine'
                                                ? 'type-domaine'
                                                : 'type-hebergement'
                                            ?>
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                <?= $a['type'] === 'domaine'
                                                    ? 'fa-globe'
                                                    : 'fa-server'
                                                ?>
                                            "
                                        ></i>

                                    </div>

                                    <div>

                                        <div class="type-name">

                                            <?= $a['type'] === 'domaine'
                                                ? 'Domaine'
                                                : 'Hébergement'
                                            ?>

                                        </div>

                                        <div class="type-subtitle">
                                            Service
                                        </div>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars($nom) ?>
                                </strong>

                            </td>

                            <td>

                                <div class="client-cell">

                                    <div class="avatar">

                                        <?= htmlspecialchars($initials) ?>

                                    </div>

                                    <div>

                                        <div class="client-name">

                                            <?= htmlspecialchars(
                                                $client_nom
                                            ) ?>

                                        </div>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <div class="date-expiration">

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $a['date_expiration']
                                        )
                                    ) ?>

                                </div>

                            </td>

                            <td>

                                <div
                                    class="
                                        jours
                                        <?= $jours_class ?>
                                    "
                                >

                                    <?php if ($jours < 0): ?>

                                        <?= abs($jours) ?>
                                        jour(s) de retard

                                    <?php elseif ($jours == 0): ?>

                                        Aujourd'hui

                                    <?php else: ?>

                                        <?= $jours ?>
                                        jour(s)

                                    <?php endif; ?>

                                </div>

                            </td>

                            <td>

                                <?php if ($jours < 0): ?>

                                    <span
                                        class="badge badge-expire"
                                    >

                                        <i class="fa-solid fa-circle-xmark"></i>

                                        Expiré

                                    </span>

                                <?php elseif ($jours <= 7): ?>

                                    <span
                                        class="badge badge-critique"
                                    >

                                        <i class="fa-solid fa-triangle-exclamation"></i>

                                        Urgent

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="badge badge-warning"
                                    >

                                        <i class="fa-solid fa-clock"></i>

                                        Bientôt

                                    </span>

                                <?php endif; ?>

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