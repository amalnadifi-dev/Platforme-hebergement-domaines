<?php
require_once("../administration/auth.php");
require_once("../configuration/base_donnees.php");

$id = $_GET['id'] ?? 0;
$id = (int)$id;

if($id == 0){
    header("Location: liste.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT p.*, c.full_name, c.email 
    FROM paiements p 
    LEFT JOIN clients c ON p.id_client = c.id 
    WHERE p.id = ?
");

$stmt->execute([$id]);
$p = $stmt->fetch();

if(!$p){
    die("Paiement introuvable");
}

$numero_facture = $p['numero_facture']
    ?? 'FAC-' . str_pad($p['id'], 6, '0', STR_PAD_LEFT);

$date_paiement = !empty($p['date_paiement'])
    ? date('d/m/Y à H:i', strtotime($p['date_paiement']))
    : date('d/m/Y à H:i');

$statut = strtolower(trim($p['statut'] ?? ''));

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Facture <?= htmlspecialchars($numero_facture) ?></title>

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{
    --primary:#6366f1;
    --primary-dark:#4f46e5;
    --green:#10b981;
    --orange:#f59e0b;
    --red:#ef4444;
}

body{

    font-family:'Inter',sans-serif;

    min-height:100vh;

    color:#e2e8f0;

    display:flex;

    background:
        linear-gradient(
            rgba(15,23,42,.80),
            rgba(15,23,42,.88)
        ),
        url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop');

    background-position:center;
    background-size:cover;
    background-attachment:fixed;
    background-repeat:no-repeat;
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

    font-size:18px;

    background:
        linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
        );
}

.logo span{

    font-size:20px;

    font-weight:800;

    color:#f8fafc;
}

.nav-title{

    margin:10px 12px;

    font-size:10px;

    font-weight:700;

    letter-spacing:1px;

    text-transform:uppercase;

    color:#64748b;
}

.sidebar a{

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

    transition:.2s;
}

.sidebar a i{

    width:18px;

    text-align:center;
}

.sidebar a:hover{

    background:rgba(99,102,241,.12);

    color:#a5b4fc;

    transform:translateX(3px);
}

.sidebar a.active{

    background:
        linear-gradient(
            135deg,
            #6366f1,
            #4f46e5
        );

    color:white;

    box-shadow:
        0 8px 20px rgba(79,70,229,.25);
}

.sidebar-bottom{

    position:absolute;

    bottom:20px;

    left:16px;
    right:16px;
}

.logout{

    color:#f87171 !important;
}

.logout:hover{

    background:rgba(239,68,68,.10) !important;

    color:#fca5a5 !important;
}

.content{

    margin-left:250px;

    width:calc(100% - 250px);

    min-height:100vh;

    padding:28px;
}

.topbar{

    min-height:78px;

    padding:16px 20px;

    margin-bottom:26px;

    border-radius:18px;

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

    font-size:13px;

    color:#64748b;
}

.date-box i{

    color:var(--primary);

    margin-right:6px;
}

.notification{

    width:40px;
    height:40px;

    border-radius:10px;

    display:flex;

    align-items:center;
    justify-content:center;

    color:#a5b4fc;

    background:rgba(99,102,241,.12);
}

.actions{

    display:flex;

    justify-content:flex-end;

    gap:10px;

    margin-bottom:18px;
}

.btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    padding:11px 18px;

    border-radius:10px;

    border:none;

    text-decoration:none;

    cursor:pointer;

    font-family:'Inter',sans-serif;

    font-size:12px;

    font-weight:600;

    transition:.2s;
}

.btn-primary{

    color:white;

    background:
        linear-gradient(
            135deg,
            #6366f1,
            #4f46e5
        );

    box-shadow:
        0 6px 15px rgba(79,70,229,.22);
}

.btn-primary:hover{

    transform:translateY(-1px);
}

.btn-secondary{

    color:#94a3b8;

    background:rgba(100,116,139,.10);

    border:1px solid #334155;
}

.btn-secondary:hover{

    color:white;

    background:rgba(100,116,139,.18);
}

.invoice{

    max-width:950px;

    margin:0 auto;

    padding:45px;

    border-radius:4px;

    background:#ffffff;

    border:1px solid #e5e7eb;

    box-shadow:
        0 15px 45px rgba(0,0,0,.22);

    color:#111827;
}

.invoice-header{

    display:flex;

    align-items:flex-start;

    justify-content:space-between;

    padding-bottom:30px;

    border-bottom:1px solid #e5e7eb;
}

.brand{

    display:flex;

    align-items:center;

    gap:12px;
}

.brand-icon{

    width:48px;
    height:48px;

    border-radius:8px;

    display:flex;

    align-items:center;
    justify-content:center;

    color:white;

    font-size:21px;

    background:
        linear-gradient(
            135deg,
            #6366f1,
            #4f46e5
        );
}

.brand h2{

    font-size:21px;

    font-weight:800;

    color:#111827;
}

.brand p{

    margin-top:3px;

    font-size:11px;

    color:#6b7280;
}

.invoice-title{

    text-align:right;
}

.invoice-title h2{

    font-size:30px;

    font-weight:800;

    color:#111827;

    letter-spacing:1px;
}

.invoice-title p{

    margin-top:7px;

    font-size:12px;

    color:#6b7280;
}

.invoice-number{

    color:#4f46e5 !important;

    font-weight:700;
}

.info-section{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

    margin-top:30px;
}

.info-card{

    padding:20px;

    border-radius:5px;

    background:#fafafa;

    border:1px solid #e5e7eb;
}

.info-card h3{

    display:flex;

    align-items:center;

    gap:8px;

    margin-bottom:14px;

    font-size:11px;

    font-weight:700;

    text-transform:uppercase;

    letter-spacing:.6px;

    color:#6b7280;
}

.info-card h3 i{

    color:#6366f1;
}

.client-name{

    font-size:17px;

    font-weight:700;

    color:#111827;

    margin-bottom:5px;
}

.client-email{

    font-size:13px;

    color:#6b7280;
}

.payment-section{

    margin-top:30px;
}

.section-title{

    font-size:15px;

    font-weight:700;

    color:#111827;

    margin-bottom:14px;
}

.payment-table{

    width:100%;

    border-collapse:collapse;

    border:1px solid #e5e7eb;

    border-radius:5px;

    overflow:hidden;
}

.payment-table th{

    padding:14px 16px;

    text-align:left;

    font-size:10px;

    text-transform:uppercase;

    letter-spacing:.6px;

    color:#6b7280;

    background:#f5f5f5;

    border-bottom:1px solid #e5e7eb;
}

.payment-table td{

    padding:18px 16px;

    font-size:13px;

    color:#374151;

    background:#ffffff;

    border-bottom:1px solid #eeeeee;
}

.payment-table tr:last-child td{

    border-bottom:none;
}

.payment-description{

    font-weight:600;

    color:#111827;
}

.payment-method{

    display:inline-flex;

    align-items:center;

    gap:7px;
}

.payment-method i{

    color:#6366f1;
}

.status{

    display:inline-flex;

    align-items:center;

    gap:7px;

    padding:6px 11px;

    border-radius:20px;

    font-size:10px;

    font-weight:700;
}

.status i{

    font-size:7px;
}

.status-paid{

    color:#047857;

    background:#d1fae5;
}

.status-waiting{

    color:#b45309;

    background:#fef3c7;
}

.status-cancelled{

    color:#b91c1c;

    background:#fee2e2;
}

.total-section{

    display:flex;

    justify-content:flex-end;

    margin-top:25px;
}

.total-box{

    min-width:300px;

    padding:20px;

    border-radius:5px;

    background:#f7f7f7;

    border:1px solid #e5e7eb;
}

.total-row{

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:30px;
}

.total-label{

    font-size:12px;

    font-weight:600;

    color:#6b7280;
}

.total-value{

    font-size:27px;

    font-weight:800;

    color:#059669;
}

.invoice-footer{

    display:flex;

    align-items:center;

    justify-content:space-between;

    margin-top:35px;

    padding-top:22px;

    border-top:1px solid #e5e7eb;
}

.footer-message{

    font-size:11px;

    color:#6b7280;
}

.footer-message i{

    color:#10b981;

    margin-right:5px;
}

.footer-date{

    font-size:11px;

    color:#9ca3af;
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

        padding:13px;
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

    .invoice{

        padding:30px;
    }
}

@media(max-width:650px){

    .content{

        padding:18px;
    }

    .topbar{

        padding:15px;
    }

    .welcome h1{

        font-size:18px;
    }

    .invoice{

        padding:20px;

        border-radius:3px;
    }

    .invoice-header{

        flex-direction:column;

        gap:20px;
    }

    .invoice-title{

        text-align:left;
    }

    .invoice-title h2{

        font-size:25px;
    }

    .info-section{

        grid-template-columns:1fr;
    }

    .payment-table{

        display:block;

        overflow-x:auto;
    }

    .total-section{

        justify-content:stretch;
    }

    .total-box{

        width:100%;

        min-width:auto;
    }

    .invoice-footer{

        flex-direction:column;

        align-items:flex-start;

        gap:8px;
    }

    .actions{

        flex-direction:column;
    }

    .actions .btn{

        width:100%;
    }
}

@media print{

    @page{

        size:A4;

        margin:12mm;
    }

    body{

        display:block;

        background:white !important;

        color:#111827 !important;
    }

    .sidebar,
    .no-print{

        display:none !important;
    }

    .content{

        margin:0;

        width:100%;

        padding:0;
    }

    .invoice{

        max-width:100%;

        margin:0;

        padding:25px;

        background:#ffffff !important;

        color:#111827 !important;

        border:none;

        box-shadow:none;
    }

    .brand h2,
    .invoice-title h2,
    .client-name,
    .section-title,
    .payment-description{

        color:#111827 !important;
    }

    .info-card{

        background:#fafafa !important;

        border-color:#e5e7eb !important;
    }

    .payment-table{

        border-color:#e5e7eb !important;
    }

    .payment-table th{

        background:#f5f5f5 !important;

        color:#4b5563 !important;
    }

    .payment-table td{

        background:#ffffff !important;

        color:#374151 !important;

        border-color:#e5e7eb !important;
    }

    .total-box{

        background:#f7f7f7 !important;

        border-color:#e5e7eb !important;
    }

    .total-value{

        color:#059669 !important;
    }

    .invoice-header,
    .invoice-footer{

        border-color:#e5e7eb !important;
    }

}

</style>

</head>

<body>

<div class="sidebar no-print">

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

        <a
            href="../administration/deconnexion.php"
            class="logout"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>Déconnexion</span>

        </a>

    </div>

</div>

<div class="content">

    <div class="topbar no-print">

        <div class="welcome">

            <h1>
                Facture de paiement
            </h1>

            <p>
                Document de confirmation de paiement
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

    <div class="actions no-print">

        <button
            onclick="window.print()"
            class="btn btn-primary"
        >

            <i class="fa-solid fa-print"></i>

            Imprimer la facture

        </button>

        <a
            href="liste.php"
            class="btn btn-secondary"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Retour aux paiements

        </a>

    </div>

    <div class="invoice">

        <div class="invoice-header">

            <div class="brand">

                <div class="brand-icon">

                    <i class="fa-solid fa-cloud"></i>

                </div>

                <div>

                    <h2>
                        HostManager
                    </h2>

                    <p>
                        Gestion des services d'hébergement
                    </p>

                </div>

            </div>

            <div class="invoice-title">

                <h2>
                    FACTURE
                </h2>

                <p>

                    Référence :

                    <span class="invoice-number">

                        <?= htmlspecialchars($numero_facture) ?>

                    </span>

                </p>

            </div>

        </div>

        <div class="info-section">

            <div class="info-card">

                <h3>

                    <i class="fa-solid fa-user"></i>

                    Informations client

                </h3>

                <div class="client-name">

                    <?= htmlspecialchars(
                        $p['full_name'] ?? 'Client supprimé'
                    ) ?>

                </div>

                <div class="client-email">

                    <i class="fa-regular fa-envelope"></i>

                    <?= htmlspecialchars(
                        $p['email'] ?? 'N/A'
                    ) ?>

                </div>

            </div>

            <div class="info-card">

                <h3>

                    <i class="fa-solid fa-calendar-check"></i>

                    Informations de paiement

                </h3>

                <div class="client-name">

                    <?= $date_paiement ?>

                </div>

                <div class="client-email">

                    <i class="fa-solid fa-receipt"></i>

                    Paiement enregistré

                </div>

            </div>

        </div>

        <div class="payment-section">

            <div class="section-title">

                Détails du paiement

            </div>

            <table class="payment-table">

                <thead>

                    <tr>

                        <th>
                            Description
                        </th>

                        <th>
                            Méthode
                        </th>

                        <th>
                            Statut
                        </th>

                        <th style="text-align:right;">
                            Montant
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>

                            <div class="payment-description">

                                <?= !empty($p['description'])
                                    ? htmlspecialchars($p['description'])
                                    : 'Service HostManager'
                                ?>

                            </div>

                        </td>

                        <td>

                            <span class="payment-method">

                                <i class="fa-solid fa-credit-card"></i>

                                <?= ucfirst(
                                    htmlspecialchars(
                                        $p['methode']
                                    )
                                ) ?>

                            </span>

                        </td>

                        <td>

                            <?php if(
                                $statut == 'payé' ||
                                $statut == 'paye'
                            ): ?>

                                <span class="status status-paid">

                                    <i class="fa-solid fa-circle"></i>

                                    Payé

                                </span>

                            <?php elseif(
                                $statut == 'en_attente' ||
                                $statut == 'en attente'
                            ): ?>

                                <span class="status status-waiting">

                                    <i class="fa-solid fa-circle"></i>

                                    En attente

                                </span>

                            <?php else: ?>

                                <span class="status status-cancelled">

                                    <i class="fa-solid fa-circle"></i>

                                    Annulé

                                </span>

                            <?php endif; ?>

                        </td>

                        <td style="text-align:right;">

                            <strong
                                style="
                                    font-size:16px;
                                    color:#111827;
                                "
                            >

                                <?= number_format(
                                    $p['montant'],
                                    2
                                ) ?> €

                            </strong>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <div class="total-section">

            <div class="total-box">

                <div class="total-row">

                    <span class="total-label">

                        Total payé

                    </span>

                    <span class="total-value">

                        <?= number_format(
                            $p['montant'],
                            2
                        ) ?> €

                    </span>

                </div>

            </div>

        </div>

        <div class="invoice-footer">

            <div class="footer-message">

                <i class="fa-solid fa-circle-check"></i>

                Merci pour votre confiance.

            </div>

            <div class="footer-date">

                Facture générée le
                <?= date('d/m/Y') ?>

            </div>

        </div>

    </div>

</div>

</body>

</html>