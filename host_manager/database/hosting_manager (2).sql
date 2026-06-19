-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 19 juin 2026 à 23:24
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hosting_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$rLqLT.YwgL4d9Y6FeS9c0u43mnFigDR4q5MexrItGOnut/MQ25hba', '2026-06-15 12:33:56');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `full_name`, `email`, `phone`, `company`, `created_at`) VALUES
(1, 'Nora Nadifi', 'norabac3iwad@gmail.com', '0698412203', 'Maroc Zip', '2026-06-19 14:56:13'),
(2, 'Amal Nadifi', 'amalnadifi2022@gmail.com', '0698712203', 'Maroc Tel', '2026-06-19 14:56:13'),
(3, 'Ahmed Benali', 'ahmed.benali@gmail.com', '0623456789', 'Benali Store', '2026-06-19 14:56:13'),
(4, 'Fatima Zahra', 'fatima.zahra@outlook.com', '0634567890', 'Fatima Blog', '2026-06-19 14:56:13'),
(5, 'Youssef Amrani', 'youssef.amrani@yahoo.fr', '0645678901', 'Amrani Shop', '2026-06-19 14:56:13'),
(6, 'Sara Alaoui', 'sara.alaoui@gmail.com', '0656789012', 'Sara Design', '2026-06-19 14:56:13'),
(7, 'Omar Idrissi', 'omar.idrissi@hotmail.com', '0667890123', 'Idrissi Consulting', '2026-06-19 14:56:13'),
(8, 'Khadija Berrada', 'khadija.berrada@gmail.com', '0678901234', 'Berrada Immo', '2026-06-19 14:56:13'),
(9, 'Hassan Tazi', 'hassan.tazi@gmail.com', '0689012345', 'Tazi Agadir', '2026-06-19 14:56:13'),
(10, 'Amina Chaoui', 'amina.chaoui@outlook.com', '0690123456', 'Chaoui Events', '2026-06-19 14:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `domaines`
--

CREATE TABLE `domaines` (
  `id` int(11) NOT NULL,
  `nom_domaine` varchar(255) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date_enregistrement` date NOT NULL,
  `date_expiration` date NOT NULL,
  `statut` enum('actif','expiré','suspendu') NOT NULL DEFAULT 'actif',
  `registrar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `domaines`
--

INSERT INTO `domaines` (`id`, `nom_domaine`, `id_client`, `date_enregistrement`, `date_expiration`, `statut`, `registrar`) VALUES
(1, 'nora-tech.ma', 1, '2024-12-15', '2026-12-15', 'actif', NULL),
(2, 'amal-tel.ma', 2, '2024-11-20', '2026-11-20', 'actif', NULL),
(3, 'benali-store.com', 3, '2024-11-20', '2026-11-20', 'actif', NULL),
(4, 'fatima-blog.net', 4, '2024-08-10', '2025-08-10', 'expiré', NULL),
(5, 'amrani-shop.ma', 5, '2025-03-05', '2027-03-05', 'actif', NULL),
(6, 'sara-design.com', 6, '2024-09-30', '2026-09-30', 'actif', NULL),
(7, 'idrissi-consulting.ma', 7, '2024-07-12', '2026-07-12', 'actif', NULL),
(8, 'berrada-immo.com', 8, '2024-06-18', '2025-06-18', 'expiré', NULL),
(9, 'tazi-agadir.ma', 9, '2025-01-25', '2027-01-25', 'actif', NULL),
(10, 'chaoui-events.net', 10, '2024-10-08', '2026-10-08', 'actif', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `hebergements`
--

CREATE TABLE `hebergements` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `plan` varchar(100) NOT NULL,
  `espace` int(11) NOT NULL,
  `bande_passante` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_expiration` date NOT NULL,
  `statut` enum('actif','expire','suspendu') DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `hebergements`
--

INSERT INTO `hebergements` (`id`, `id_client`, `plan`, `espace`, `bande_passante`, `date_debut`, `date_expiration`, `statut`, `date_creation`) VALUES
(1, 1, 'Pack Pro', 50, 0, '2024-01-15', '2026-01-15', 'actif', '2026-06-19 14:56:14'),
(2, 2, 'Pack Starter', 10, 100, '2024-03-20', '2026-03-20', 'actif', '2026-06-19 14:56:14'),
(3, 3, 'Pack Business', 100, 0, '2024-04-10', '2026-04-10', 'actif', '2026-06-19 14:56:14'),
(4, 5, 'Pack Pro', 50, 0, '2024-05-05', '2026-05-05', 'actif', '2026-06-19 14:56:14'),
(5, 6, 'Pack Premium', 200, 0, '2024-07-30', '2026-07-30', 'actif', '2026-06-19 14:56:14'),
(6, 8, 'Pack Business', 100, 0, '2023-12-18', '2025-12-18', 'actif', '2026-06-19 14:56:14');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `methode` enum('carte','virement','paypal','espèces') DEFAULT 'carte',
  `statut` enum('payé','en_attente','annulé') DEFAULT 'payé',
  `date_paiement` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `id_client`, `montant`, `methode`, `statut`, `date_paiement`, `description`) VALUES
(1, 1, 1200.00, 'carte', 'payé', '2026-06-10 00:00:00', 'Hébergement Pro - 1 an'),
(2, 2, 600.00, 'virement', 'payé', '2026-06-08 00:00:00', 'Domaine amal-tel.ma'),
(4, 5, 1200.00, 'paypal', 'payé', '2026-05-28 00:00:00', 'Pack Pro amrani-shop.ma'),
(5, 6, 2500.00, 'carte', 'payé', '2026-05-20 00:00:00', 'Pack Premium sara-design'),
(6, 7, 600.00, 'carte', 'en_attente', '2026-05-15 00:00:00', 'Hébergement Starter'),
(7, 8, 1800.00, 'virement', 'payé', '2026-05-10 00:00:00', 'Pack Business berrada-immo'),
(8, 9, 1200.00, 'carte', 'payé', '2026-04-22 00:00:00', 'Renouvellement tazi-agadir.ma');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `domaines`
--
ALTER TABLE `domaines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `hebergements`
--
ALTER TABLE `hebergements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `domaines`
--
ALTER TABLE `domaines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `hebergements`
--
ALTER TABLE `hebergements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `domaines`
--
ALTER TABLE `domaines`
  ADD CONSTRAINT `domaines_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `hebergements`
--
ALTER TABLE `hebergements`
  ADD CONSTRAINT `hebergements_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
