<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$theme = $_SESSION['theme'] ?? 'dark';

$id = $_GET['id'] ?? 0;

if($_POST){
    $stmt = $pdo->prepare("
        UPDATE paiements 
        SET montant=?, methode=?, statut=?, description=? 
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['montant'],
        $_POST['methode'],
        $_POST['statut'],
        $_POST['description'],
        $id
    ]);

    header("Location: liste.php");
    exit();
}

$p = $pdo->query("
    SELECT p.*, c.full_name 
    FROM paiements p 
    JOIN clients c ON p.id_client = c.id 
    WHERE p.id = $id
")->fetch();

if(!$p){
    die("Paiement introuvable");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modifier Paiement #<?= $p['id'] ?></title>

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

            color: #e2e8f0;

            background:
                linear-gradient(
                    rgba(15, 23, 42, .78),
                    rgba(15, 23, 42, .84)
                ),
                url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop');

            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .sidebar {
            width: 250px;
            height: 100vh;

            position: fixed;
            left: 0;
            top: 0;

            padding: 22px 16px;

            background: rgba(15, 23, 42, .97);

            border-right: 1px solid #334155;

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

            border-radius: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;
            font-size: 18px;

            background: linear-gradient(
                135deg,
                #6366f1,
                #8b5cf6
            );
        }

        .logo span {
            font-size: 20px;
            font-weight: 800;
            color: #f8fafc;
        }

        .nav-title {
            font-size: 10px;
            font-weight: 700;

            letter-spacing: 1px;
            text-transform: uppercase;

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

            color: #64748b;

            text-decoration: none;

            font-size: 14px;
            font-weight: 500;

            transition: .2s;
        }

        .sidebar a i {
            width: 18px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar a:hover {
            background: rgba(99, 102, 241, .12);
            color: #a5b4fc;

            transform: translateX(3px);
        }

        .sidebar a.active {
            background: linear-gradient(
                135deg,
                #6366f1,
                #4f46e5
            );

            color: white;

            box-shadow:
                0 8px 20px rgba(79, 70, 229, .25);
        }

        .sidebar-bottom {
            position: absolute;
            bottom: 20px;
            left: 16px;
            right: 16px;
        }

        .logout {
            color: #f87171 !important;
        }

        .logout:hover {
            background: rgba(239, 68, 68, .10) !important;
            color: #fca5a5 !important;
        }

        .content {
            margin-left: 250px;

            width: calc(100% - 250px);

            min-height: 100vh;

            padding: 28px;
        }

        .topbar {
            min-height: 78px;

            padding: 16px 20px;

            margin-bottom: 26px;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background: rgba(30, 41, 59, .82);

            border: 1px solid #334155;

            backdrop-filter: blur(15px);
        }

        .welcome h1 {
            font-size: 23px;
            font-weight: 800;

            color: #f8fafc;

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
            font-size: 13px;
            color: #64748b;
        }

        .date-box i {
            color: var(--primary);
            margin-right: 6px;
        }

        .notification {
            width: 40px;
            height: 40px;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #a5b4fc;

            background: rgba(99, 102, 241, .12);
        }

        .box {
            width: 100%;
            max-width: 850px;

            padding: 28px;

            border-radius: 17px;

            background: rgba(30, 41, 59, .82);

            border: 1px solid #334155;

            backdrop-filter: blur(15px);

            margin: 0 auto;
        }

        .box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 25px;
        }

        .box-title {
            display: flex;
            align-items: center;
            gap: 10px;

            font-size: 17px;
            font-weight: 700;

            color: #f8fafc;
        }

        .box-title i {
            color: var(--primary);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 9px 13px;

            border-radius: 10px;

            text-decoration: none;

            color: #64748b;

            font-size: 12px;
            font-weight: 600;

            background: rgba(100, 116, 139, .08);

            transition: .2s;
        }

        .back-btn:hover {
            color: #a5b4fc;
            background: rgba(99, 102, 241, .12);
        }

        form {
            width: 100%;
        }

        .form-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: span 2;
        }

        .form-group label {
            margin-bottom: 8px;

            font-size: 12px;
            font-weight: 700;

            color: #64748b;
        }

        .form-group label i {
            margin-right: 6px;
            color: var(--primary);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            padding: 12px 14px;

            border-radius: 10px;

            border: 1px solid #334155;

            outline: none;

            background: rgba(15, 23, 42, .75);

            color: #e2e8f0;

            font-family: 'Inter', sans-serif;

            font-size: 13px;

            transition: .2s;
        }

        .form-group input,
        .form-group select {
            height: 46px;
        }

        .form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: var(--primary);

            box-shadow:
                0 0 0 3px rgba(99, 102, 241, .10);
        }

        .form-group input:disabled {

            opacity: .65;

            cursor: not-allowed;

            background: rgba(15, 23, 42, .45);
        }

        select option {
            background: #1e293b;
            color: #e2e8f0;
        }

        .form-actions {

            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 28px;

            padding-top: 20px;

            border-top: 1px solid rgba(51, 65, 85, .8);
        }

        .btn {

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 11px 18px;

            border-radius: 10px;

            border: none;

            text-decoration: none;

            cursor: pointer;

            font-family: 'Inter', sans-serif;

            font-size: 12px;

            font-weight: 600;

            transition: .2s;
        }

        .btn-primary {

            color: white;

            background: linear-gradient(
                135deg,
                #6366f1,
                #4f46e5
            );

            box-shadow:
                0 6px 15px rgba(79, 70, 229, .22);
        }

        .btn-primary:hover {

            transform: translateY(-1px);

            box-shadow:
                0 9px 20px rgba(79, 70, 229, .32);
        }

        .btn-secondary {

            color: #94a3b8;

            background: rgba(100, 116, 139, .10);

            border: 1px solid #334155;
        }

        .btn-secondary:hover {

            color: #f8fafc;

            background: rgba(100, 116, 139, .18);
        }

        @media(max-width: 850px) {

            .sidebar {
                width: 70px;
                padding: 22px 10px;
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
            }

            .date-box {
                display: none;
            }
        }

        @media(max-width: 650px) {

            .content {
                padding: 18px;
            }

            .topbar {
                padding: 15px;
                border-radius: 14px;
            }

            .welcome h1 {
                font-size: 18px;
            }

            .box {
                padding: 20px;
            }

            .box-header {
                align-items: flex-start;
                gap: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: span 1;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
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

    <a href="../domaines/liste.php">
        <i class="fa-solid fa-globe"></i>
        <span>Domaines</span>
    </a>

    <a href="../hebergements/liste.php">
        <i class="fa-solid fa-server"></i>
        <span>Hébergements</span>
    </a>

    <a href="liste.php" class="active">
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

            <h1>
                Modifier le paiement
            </h1>

            <p>
                Modification du paiement #<?= $p['id'] ?>
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

                <i class="fa-solid fa-pen-to-square"></i>

                Modifier Paiement #<?= $p['id'] ?>

            </div>

            <a href="liste.php" class="back-btn">

                <i class="fa-solid fa-arrow-left"></i>

                Retour

            </a>

        </div>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group full">

                    <label>

                        <i class="fa-solid fa-user"></i>

                        Client

                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars($p['full_name']) ?>"
                        disabled
                    >

                </div>

                <div class="form-group">

                    <label>

                        <i class="fa-solid fa-euro-sign"></i>

                        Montant

                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="montant"
                        value="<?= htmlspecialchars($p['montant']) ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>

                        <i class="fa-solid fa-credit-card"></i>

                        Méthode de paiement

                    </label>

                    <select name="methode" required>

                        <option
                            value="Carte"
                            <?= $p['methode']=='Carte' ? 'selected' : '' ?>
                        >
                            Carte Bancaire
                        </option>

                        <option
                            value="Virement"
                            <?= $p['methode']=='Virement' ? 'selected' : '' ?>
                        >
                            Virement
                        </option>

                        <option
                            value="PayPal"
                            <?= $p['methode']=='PayPal' ? 'selected' : '' ?>
                        >
                            PayPal
                        </option>

                        <option
                            value="Espèces"
                            <?= $p['methode']=='Espèces' ? 'selected' : '' ?>
                        >
                            Espèces
                        </option>

                    </select>

                </div>

                <div class="form-group full">

                    <label>

                        <i class="fa-solid fa-circle-check"></i>

                        Statut

                    </label>

                    <select name="statut" required>

                        <option
                            value="payé"
                            <?= $p['statut']=='payé' ? 'selected' : '' ?>
                        >
                            Payé
                        </option>

                        <option
                            value="en attente"
                            <?= $p['statut']=='en attente' ? 'selected' : '' ?>
                        >
                            En attente
                        </option>

                    </select>

                </div>

                <div class="form-group full">

                    <label>

                        <i class="fa-solid fa-comment"></i>

                        Description

                    </label>

                    <textarea
                        name="description"
                        rows="3"
                    ><?= htmlspecialchars($p['description'] ?? '') ?></textarea>

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

</div>

</body>
</html>