# Platforme-hebergement-domaines
Platforme web de gestion des clients, hébergements, noms de domaines et suivi des renouvellements: projet réalisé dans le cadre de stage d'inititiation chez l'entreprise VALA.
# Fonctionnalités prévues
-gestion des clients
-gestion des noms de domaines
-gestion des hébergements 
-suivi des renouvellements
-gestion des paiements
Tableau de bord administrateur
# Technologies 
- HTML
- CSS
- JavaScript
- PHP
- MySQL

#  Rapport Semaine 1 - 
1. Travaux réalisés cette semaine
# 1.1 Base de données
Création de la base de données hosting_manager avec les tables suivantes :
admins : gestion des comptes administrateurs
clients : informations des clients domaines : liste des noms de domaines
hebergements : plans d'hébergement liés aux clients
paiements : historique des transactions
# 1.2 Développement Backend - PHP
Configuration: fichier base_donnees.php pour la connexion sécurisée à MySQL
Système d'authentification :
auth.php : vérification de session
connexion.php : page de login
deconnexion.php : destruction de session
# 1.3 Interface Frontend
Page de connexion moderne : design CSS responsive avec formulaire stylisé
Tableau de bord simple : tableau_bord.php avec première version du design
Assets : intégration d'une image de fond bg.jpeg et base CSS
# 1.4 Gestion de version
Initialisation du dépôt GitHub : Plateforme-hebergement-domaines
Structure du projet organisée en dossiers : administration/, configuration/, assets/, database/
# 2. Technologies utilisées
Backend : PHP 8, PDO MySQL
Base de données : MySQL - hosting_manager
Frontend : HTML5, CSS3 moderne
Outils : VS Code, Git/GitHub, XAMPP



#  Rapport Semaine 2 - 
## Module Clients - CRUD Complet
- Fichiers créés : clients/ajouter.php, clients/liste.php, clients/modifier.php, clients/supprimer.php
Fonctionnalités :
- Ajout nouveau client avec validation des champs
- Liste des clients avec informations
- Modification des informations client
- Suppression avec confirmation 
## Module Domaines - CRUD Complet
- Fichiers créés : domaines/ajouter.php, domaines/liste.php, domaines/modifier.php, domaines/supprimer.php
Fonctionnalités :
- Liaison avec table clients via clé étrangère client_id
- Gestion des dates : date_achat et date_expiration
- Liste des domaines avec statut : Actif / Expire bientôt / Expiré
- Formulaire d'ajout avec menu déroulant des clients existants
- Validation des dates d'expiration
- Ajout nouveau domaine avec validation des champs
- Modification 
- Suppression avec confirmation 
## Module Hébergements - Démarrage
- Fichier créé : hebergements/ajouter.php
- Fonctionnalités : Formulaire d'ajout avec liaison client + domaine
- État : En cours de développement - CRUD à compléter semaine 3
## Difficultés rencontrées
- Gestion des clés étrangères entre domaines et clients lors de la suppression
- Validation des dates d'expiration en PHP vs MySQL



# rapport semaine 3-
# Objectif de la semaine
Finaliser modules Hébergements et Paiements.
## Réalisations
# Module Hébergements - CRUD Complet
- Création fichiers : ajouter.php, liste.php, modifier.php, supprimer.php
- Ajout hébergement avec choix client, plan, espace disque, bande passante
- Gestion dates début et expiration
- Liste des hébergements avec nom du client
- Modification et suppression hébergement
- Sécurisation avec PDO et authentification
# Module Paiements - CRUD Complet
- Création fichiers : ajouter.php, liste.php, modifier.php, facture.php, supprimer.php
- Ajout paiement lié au client et hébergement
- Champs : montant, date paiement, méthode, statut
- Liste des factures avec statut payé/en attente..
- Modification et suppression paiement
# Difficultés rencontrées
- Gestion des liaisons entre tables
- Calcul des dates d'expiration
- Tests complets de la plateforme



  # rapport Semaine 4  - Semaine Finale

### Objectif de la semaine
Finaliser la plateforme .

### Réalisations

#### 1. Module Alertes - Terminé
- Création fichier `alertes.php`
- Système d'alertes automatiques pour domaines expirant dans 30 jours
- Alertes pour hébergements expirant bientôt
- Affichage notifications dans tableau de bord

#### 2. Tests et Débogage Plateforme
- Test complet des 4 modules : Clients, Domaines, Hébergements, Paiements
- Correction bug suppression paiement
- Correction bug affichage facture
- Vérification sécurité PDO sur tous les formulaires
- Test authentification et sessions
- Vérification responsive design

#### 3. Optimisations
- Ajout messages de confirmation suppression
- Optimisation temps de chargement listes

#### 4. Préparation
- Code complet pushé sur GitHub
- Captures d'écran de toutes les fonctionnalités
- README.md finalisé avec documentation
- Préparation rapport de stage final

### Bilan Global du Stage
-Modules développés :5 modules complets + Dashboard
- Fichiers créés : 20+ fichiers PHP
- Technologies : PHP 8, MySQL, PDO, HTML/CSS
- Fonctionnalités : CRUD complet, authentification, alertes, factures


### État Final
Plateforme HostManager 95ù% fonctionnelle et prête pour production, et les erreurs seront corigées si ells sont détectées.

### Lien
GitHub : https://github.com/amalnadifi-dev/Plateforme-hebergement-domaines/tree/main/host_manager
fin de stage...
