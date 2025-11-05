-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 oct. 2025 à 11:08
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
-- Base de données : `govathon_bd`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `created_at`, `updated_at`) VALUES
(1, 'Étudiant', NULL, NULL),
(2, 'Startup', NULL, NULL),
(3, 'Autre', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `configurations`
--

CREATE TABLE `configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cle` varchar(255) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `criteres`
--

CREATE TABLE `criteres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `poids` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `criteres`
--

INSERT INTO `criteres` (`id`, `nom`, `description`, `poids`, `created_at`, `updated_at`) VALUES
(18, 'Innovation', NULL, 1, '2025-10-13 16:57:48', '2025-10-13 16:57:48'),
(19, 'impact', 'impact', 1, '2025-10-14 07:50:47', '2025-10-14 07:50:47');

-- --------------------------------------------------------

--
-- Structure de la table `critere_categorie`
--

CREATE TABLE `critere_categorie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `critere_id` bigint(20) UNSIGNED NOT NULL,
  `categorie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `critere_categorie`
--

INSERT INTO `critere_categorie` (`id`, `critere_id`, `categorie_id`, `created_at`, `updated_at`) VALUES
(23, 18, 1, NULL, NULL),
(24, 18, 2, NULL, NULL),
(25, 18, 3, NULL, NULL),
(26, 19, 1, NULL, NULL),
(27, 19, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `chemin` varchar(255) NOT NULL,
  `date_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `documents`
--

INSERT INTO `documents` (`id`, `projet_id`, `type`, `chemin`, `date_upload`) VALUES
(1, 9, 'ninea', 'submissions/ninea/IqEXyA4Yn0pQnVFhb4usD98xlUixp6ryTH0xJsiF.png', '2025-10-02 10:17:34'),
(2, 9, 'rccm', 'submissions/rccm/ZaTpuwsorWvkYxboLYYEm9iPlaBeKFHqP0trezA4.png', '2025-10-02 10:17:34');

-- --------------------------------------------------------

--
-- Structure de la table `equipe_membres`
--

CREATE TABLE `equipe_membres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `chemin_carte_etudiant` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipe_membres`
--

INSERT INTO `equipe_membres` (`id`, `projet_id`, `prenom`, `nom`, `email`, `telephone`, `role`, `chemin_carte_etudiant`, `created_at`, `updated_at`) VALUES
(1, 1, 'fzde', '', 'mbaye26.dieng@gmail.com', '772687346', 'Chef d\'équipe', NULL, '2025-10-02 09:13:29', '2025-10-02 09:13:29'),
(2, 1, 'daedza', '', 'mbaye2eae6.dieng@gmail.com', '1322454', 'Membre', NULL, '2025-10-02 09:13:29', '2025-10-02 09:13:29'),
(3, 2, 'dezde', '', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-02 09:29:10', '2025-10-02 09:29:10'),
(4, 2, 'daeda', '', 'mbayediengdeadae@fpublique.gouv.sn', '21324', 'Membre', NULL, '2025-10-02 09:29:10', '2025-10-02 09:29:10'),
(5, 3, 'cezd', '', 'babacar12018@gmail.com', '32454', 'Chef d\'équipe', NULL, '2025-10-02 09:33:40', '2025-10-02 09:33:40'),
(6, 3, 'déedeéd', '', 'mbayedieng@fpublique.gouv.sn', '134', 'Membre', NULL, '2025-10-02 09:33:40', '2025-10-02 09:33:40'),
(7, 4, 'ddadae', '', 'babacar12018@gmail.com', '0938280000', 'Chef d\'équipe', NULL, '2025-10-02 09:35:33', '2025-10-02 09:35:33'),
(8, 4, 'ezdfrzerzf', '', 'mbayedieng@fpublique.gouv.sn', '21345', 'Membre', NULL, '2025-10-02 09:35:33', '2025-10-02 09:35:33'),
(9, 5, 'daed', '', 'mbaye26.dieng@gmail.com', '772687346', 'Chef d\'équipe', NULL, '2025-10-02 10:02:28', '2025-10-02 10:02:28'),
(10, 5, 'dazdaz', '', 'mbayeadeazd26.dieng@gmail.com', '2312435', 'Membre', NULL, '2025-10-02 10:02:28', '2025-10-02 10:02:28'),
(13, 7, 'deadez', '', 'mbaye26.dieng@gmail.com', '772687346', 'Chef d\'équipe', NULL, '2025-10-02 10:04:46', '2025-10-02 10:04:46'),
(14, 7, 'dezdez', '', 'mbayegtefze26.dieng@gmail.com', '2132435465', 'Membre', NULL, '2025-10-02 10:04:46', '2025-10-02 10:04:46'),
(15, 8, 'szae', '', 'mbaye26.dieng@gmail.com', '772687346', 'Chef d\'équipe', NULL, '2025-10-02 10:16:36', '2025-10-02 10:16:36'),
(16, 8, 'zedz', '', 'mbayeezeze26.dieng@gmail.com', '234', 'Membre', NULL, '2025-10-02 10:16:36', '2025-10-02 10:16:36'),
(17, 9, 'eee', '', 'mbaye26.dieng@gmail.com', '4', 'Chef d\'équipe', NULL, '2025-10-02 10:17:34', '2025-10-02 10:17:34'),
(18, 9, 'dae', '', 'mbaydedee26.dieng@gmail.com', '213243', 'Membre', NULL, '2025-10-02 10:17:34', '2025-10-02 10:17:34'),
(19, 10, 'dzae', '', 'mbaye26.dieng@gmail.com', '772687346', 'Chef d\'équipe', NULL, '2025-10-02 10:18:17', '2025-10-02 10:18:17'),
(20, 10, 'dae', '', 'mbaydezde26.dieng@gmail.com', '3124', 'Membre', NULL, '2025-10-02 10:18:17', '2025-10-02 10:18:17'),
(21, 11, 'eze', '', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-03 08:30:27', '2025-10-03 08:30:27'),
(22, 11, 'ez&e', '', 'fezre@gmail.com', '21345', 'Membre', NULL, '2025-10-03 08:30:27', '2025-10-03 08:30:27'),
(23, 12, 'dezdez', '', 'mbayedisdsdeng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-03 09:17:45', '2025-10-03 09:17:45'),
(24, 12, 'dezdze', '', 'mbayedieng@fpublique.gouv.sn', '0938221380000', 'Membre', NULL, '2025-10-03 09:17:45', '2025-10-03 09:17:45'),
(25, 13, 'test', '', 'test@gmail.com', '1234', 'Chef d\'équipe', NULL, '2025-10-03 10:10:35', '2025-10-03 10:10:35'),
(26, 13, 'dazd', '', 'mbayetestdieng@fpublique.gouv.sn', '21324', 'Membre', NULL, '2025-10-03 10:10:35', '2025-10-03 10:10:35'),
(27, 14, 'deaea', '', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-03 12:13:57', '2025-10-03 12:13:57'),
(28, 14, 'dadea', '', 'mbayedededieng@fpublique.gouv.sn', '32313313', 'Membre', NULL, '2025-10-03 12:13:57', '2025-10-03 12:13:57'),
(29, 15, 'dezde', '', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-03 12:15:54', '2025-10-03 12:15:54'),
(30, 15, 'dazd', '', 'mbayediededng@fpublique.gouv.sn', '0938280023200', 'Membre', NULL, '2025-10-03 12:15:54', '2025-10-03 12:15:54'),
(31, 16, 'test', '', 'tetestst@gmail.com', '312431', 'Chef d\'équipe', NULL, '2025-10-03 12:21:33', '2025-10-03 12:21:33'),
(32, 16, 'test', '', 'testtesttesttest@gmail.com', '31331', 'Membre', NULL, '2025-10-03 12:21:33', '2025-10-03 12:21:33'),
(33, 17, '123', '', 'mbadzeyedieng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-10 12:50:15', '2025-10-10 12:50:15'),
(34, 17, 'dazd', '', 'mbayedfeieng@fpublique.gouv.sn', '3432', 'Membre', NULL, '2025-10-10 12:50:15', '2025-10-10 12:50:15'),
(35, 18, 'Moustapha', 'Diakhate', 'moustaphadiakhate1212@gmail.com', '774572648', 'Chef d\'équipe', NULL, '2025-10-10 16:41:27', '2025-10-10 16:41:27'),
(36, 18, 'magath', 'nael', 'naelmansa@gmail.com', '781680443', 'Membre', NULL, '2025-10-10 16:41:27', '2025-10-10 16:41:27'),
(37, 19, 'dzedez', '', 'mbayediddzeeng@fpublique.gouv.sn', '0938280000', 'Chef d\'équipe', NULL, '2025-10-10 16:42:49', '2025-10-10 16:42:49'),
(38, 19, 'dezdze', '', 'mbayedieng@fpublique.gouv.sn', '21324', 'Membre', NULL, '2025-10-10 16:42:49', '2025-10-10 16:42:49'),
(39, 20, 'zdezf', '', 'mbayedieng@fpublique.gouv.sn', '2132435', 'Chef d\'équipe', NULL, '2025-10-10 17:31:31', '2025-10-10 17:31:31'),
(40, 20, 'ezdazf', '', 'mbayediengdez@fpublique.gouv.sn', '31243', 'Membre', NULL, '2025-10-10 17:31:31', '2025-10-10 17:31:31');

-- --------------------------------------------------------

--
-- Structure de la table `etat`
--

CREATE TABLE `etat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_etat` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jury_id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `phase_id` bigint(20) UNSIGNED NOT NULL,
  `critere_id` bigint(20) UNSIGNED NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupe_categorie`
--

CREATE TABLE `groupe_categorie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jury_id` bigint(20) UNSIGNED NOT NULL,
  `categorie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupe_categorie`
--

INSERT INTO `groupe_categorie` (`id`, `jury_id`, `categorie_id`, `created_at`, `updated_at`) VALUES
(36, 13, 1, '2025-10-13 16:57:33', '2025-10-13 16:57:33'),
(37, 13, 2, '2025-10-13 16:57:33', '2025-10-13 16:57:33'),
(38, 13, 3, '2025-10-13 16:57:33', '2025-10-13 16:57:33'),
(39, 14, 1, '2025-10-13 20:51:27', '2025-10-13 20:51:27'),
(40, 14, 2, '2025-10-13 20:51:27', '2025-10-13 20:51:27'),
(41, 14, 3, '2025-10-13 20:51:27', '2025-10-13 20:51:27'),
(42, 15, 3, '2025-10-13 20:51:33', '2025-10-13 20:51:33');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jury`
--

CREATE TABLE `jury` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_jury` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `categorie_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jury`
--

INSERT INTO `jury` (`id`, `nom_jury`, `description`, `categorie_id`, `created_at`, `updated_at`) VALUES
(13, 'groupe1', NULL, NULL, '2025-10-13 16:57:33', '2025-10-13 16:57:33'),
(14, 'groupe3', NULL, NULL, '2025-10-13 20:51:27', '2025-10-13 20:51:27'),
(15, 'groupe2', NULL, NULL, '2025-10-13 20:51:33', '2025-10-13 20:51:33');

-- --------------------------------------------------------

--
-- Structure de la table `jury_critere_phase`
--

CREATE TABLE `jury_critere_phase` (
  `jury_id` bigint(20) UNSIGNED NOT NULL,
  `phase_id` bigint(20) UNSIGNED NOT NULL,
  `critere_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jury_phase`
--

CREATE TABLE `jury_phase` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jury_id` bigint(20) UNSIGNED NOT NULL,
  `phase_id` bigint(20) UNSIGNED NOT NULL,
  `poids_vote` decimal(8,2) NOT NULL DEFAULT 1.00,
  `date_assignation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jury_to _del`
--

CREATE TABLE `jury_to _del` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nom_jury` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `categorie_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liste_preselectionnes`
--

CREATE TABLE `liste_preselectionnes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`snapshot`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mail_batches`
--

CREATE TABLE `mail_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `mail_batches`
--

INSERT INTO `mail_batches` (`id`, `name`, `subject`, `from_email`, `from_name`, `body`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 'Test – Projets sélectionnés', 'Information importante – Govathon', 'no-reply@govathon.local', 'Govathon', '<p>Bonjour, votre projet est sélectionné. Merci de confirmer.</p>', NULL, '2025-10-11 14:50:58', '2025-10-11 14:50:58');

-- --------------------------------------------------------

--
-- Structure de la table `mail_recipients`
--

CREATE TABLE `mail_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `submission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `error` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mail_recipients`
--

INSERT INTO `mail_recipients` (`id`, `batch_id`, `projet_id`, `submission_id`, `email`, `status`, `error`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 7, 'babacar12018@gmail.com', 'pending', NULL, NULL, '2025-10-11 14:50:58', '2025-10-11 14:50:58'),
(5, 1, 2, 23, 'mbayedieng@fpublique.gouv.sn', 'pending', NULL, NULL, '2025-10-11 14:50:58', '2025-10-11 14:50:58');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_20_161528_create_submissions_table', 1),
(5, '2025_09_23_095504_add_new_fields_to_submissions_table', 1),
(6, '2025_09_23_111131_make_ninea_file_path_nullable_in_submissions_table', 1),
(7, '2025_09_23_125332_remove_legal_status_from_submissions_table', 1),
(8, '2025_09_28_205704_add_domain_to_submissions_table', 1),
(9, '2025_09_25_120256_create_secteurs_table', 2),
(10, '2025_09_25_120256_create_themes_table', 2),
(11, '2025_09_25_120257_create_criteres_table', 2),
(12, '2025_09_25_120257_create_phases_table', 2),
(13, '2025_09_25_120257_create_projets_table', 2),
(14, '2025_09_25_120325_create_equipe_membres_table', 2),
(15, '2025_09_25_120325_create_profil_champ_valeurs_table', 2),
(16, '2025_09_25_120325_create_profil_champs_table', 2),
(17, '2025_09_25_120325_create_projet_profils_table', 2),
(18, '2025_09_25_120326_create_documents_table', 2),
(19, '2025_09_25_120336_create_configurations_table', 2),
(20, '2025_09_25_120336_create_notifications_table', 2),
(21, '2025_09_25_120336_create_vote_publics_table', 2),
(22, '2025_09_25_120336_create_votes_table', 2),
(23, '2025_09_25_120350_create_jury_critere_phase_table', 2),
(24, '2025_09_25_120350_create_jury_phase_table', 2),
(25, '2025_09_25_120350_update_users_table', 2),
(26, '2025_09_25_130000_add_foreign_to_profil_champ_valeurs_table', 2),
(27, '2025_09_26_170000_create_profils_table', 2),
(28, '2025_09_30_100000_update_submissions_table_for_secteur_and_theme', 2),
(29, '2025_09_30_140000_create_schools_table', 2),
(30, '2025_09_30_140500_add_profile_type_to_themes_table', 2),
(31, '2025_09_30_160000_create_terms_table', 2),
(32, '2025_09_28_170000_add_domain_to_submissions_table', 3),
(33, '2025_09_28_170001_add_domain_to_submissions_table', 4),
(34, '2025_10_03_085859_add_etat_to_projets_table', 4),
(35, '2025_10_03_091017_add_etat_to_submissions_table', 5),
(36, '2025_10_03_092651_rename_votes_to_evaluations', 6),
(37, '2025_10_03_093848_create_jury_table', 7),
(38, '2025_10_03_094628_add_jury_and_profil_to_users_table', 7),
(39, '2025_10_03_095732_rename_juries_to_jury_table', 8),
(40, '2025_10_03_153844_create_etat_table', 9),
(41, '2025_10_03_153844_create_notation_table', 9),
(42, '2025_10_10_090654_create_categories_table', 10),
(43, '2025_10_10_091358_create_groupes_table', 11),
(44, '2025_10_06_154224_add_projets_and_jury_tables', 12),
(45, '2025_09_25_120256_create_categories_table', 13),
(46, '2025_10_03_104521_add_domain_to_submissions_table', 13),
(47, '2025_10_10_001226_add_categorie_id_to_jury_table', 14),
(48, '2025_10_10_203623_create_groupe_critere_table', 15),
(49, '2025_10_11_113728_create_groupe_categorie_table', 16),
(50, '2025_10_11_114659_rename_groupe_critere_to_groupe_categorie', 17),
(51, '2025_10_11_123804_create_project_rejections_table', 18),
(52, '2025_10_11_143225_create_mail_batches_tables', 19),
(53, '2025_10_12_002023_create_parametrage__selection_table', 20),
(54, '2025_10_06_154224_add_details_to_notation_table', 21),
(55, '2025_10_13_112946_2025_01_01_000000_add_commentaire_to_notations_table', 22),
(56, '2025_10_16_101655_create_liste_preselectionnes_table', 23);

-- --------------------------------------------------------

--
-- Structure de la table `notation`
--

CREATE TABLE `notation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notation`
--

INSERT INTO `notation` (`id`, `user_id`, `projet_id`, `note`, `details`, `commentaire`, `created_at`, `updated_at`) VALUES
(29, 19, 11, 10.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":10,\"poids\":1}]', NULL, '2025-10-13 21:00:10', '2025-10-14 07:49:49'),
(30, 19, 10, 4.20, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":4,\"poids\":1}]', NULL, '2025-10-13 21:00:16', '2025-10-14 07:49:58'),
(31, 18, 11, 5.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":5,\"poids\":1}]', NULL, '2025-10-13 21:02:01', '2025-10-13 21:02:01'),
(32, 18, 10, 6.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":6,\"poids\":1}]', NULL, '2025-10-13 21:02:12', '2025-10-13 21:02:12'),
(33, 18, 9, 10.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":10,\"poids\":1}]', NULL, '2025-10-13 21:26:12', '2025-10-13 21:26:12'),
(34, 19, 7, 7.05, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":10,\"poids\":1},{\"critere_id\":19,\"critere\":\"impact\",\"note\":4.1,\"poids\":1}]', NULL, '2025-10-14 07:51:15', '2025-10-14 07:51:32'),
(35, 19, 9, 10.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":10,\"poids\":1},{\"critere_id\":19,\"critere\":\"impact\",\"note\":10,\"poids\":1}]', NULL, '2025-10-14 07:55:15', '2025-10-14 07:59:37'),
(36, 19, 3, 5.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":4,\"poids\":1},{\"critere_id\":19,\"critere\":\"impact\",\"note\":6,\"poids\":1}]', NULL, '2025-10-14 07:55:25', '2025-10-14 07:55:25'),
(37, 18, 3, 10.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":10,\"poids\":1},{\"critere_id\":19,\"critere\":\"impact\",\"note\":10,\"poids\":1}]', NULL, '2025-10-14 08:02:14', '2025-10-14 08:02:14'),
(38, 19, 2, 4.50, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":4,\"poids\":1},{\"critere_id\":19,\"critere\":\"impact\",\"note\":5,\"poids\":1}]', NULL, '2025-10-18 09:49:21', '2025-10-18 09:49:21'),
(39, 18, 8, 2.00, '[{\"critere_id\":18,\"critere\":\"Innovation\",\"note\":2,\"poids\":1}]', NULL, '2025-10-19 12:36:40', '2025-10-19 12:36:40');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utilisateur_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `est_lu` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametrage__selection`
--

CREATE TABLE `parametrage__selection` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_a_selectionner` int(11) NOT NULL DEFAULT 0,
  `quota_student` int(11) NOT NULL DEFAULT 0,
  `quota_startup` int(11) NOT NULL DEFAULT 0,
  `quota_others` int(11) NOT NULL DEFAULT 0,
  `note_selection` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `parametrage__selection`
--

INSERT INTO `parametrage__selection` (`id`, `nombre_a_selectionner`, `quota_student`, `quota_startup`, `quota_others`, `note_selection`, `created_at`, `updated_at`) VALUES
(2, 11, 5, 3, 3, 1.00, '2025-10-12 16:05:41', '2025-10-14 07:56:34');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `phases`
--

CREATE TABLE `phases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `numero_ordre` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profil_champs`
--

CREATE TABLE `profil_champs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_profil` varchar(255) NOT NULL,
  `nom_champ` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `type_champ` enum('text','number','boolean','select','file','url') NOT NULL,
  `requis` tinyint(1) NOT NULL DEFAULT 0,
  `numero_ordre` int(11) NOT NULL DEFAULT 0,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profil_champ_valeurs`
--

CREATE TABLE `profil_champ_valeurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_profil_id` bigint(20) UNSIGNED NOT NULL,
  `profil_champ_id` bigint(20) UNSIGNED NOT NULL,
  `valeur` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `project_rejections`
--

CREATE TABLE `project_rejections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `motif` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_equipe` varchar(255) NOT NULL,
  `nombre_membres` int(11) DEFAULT NULL,
  `secteur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `theme_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nom_projet` varchar(255) NOT NULL,
  `resume` text NOT NULL,
  `description` text NOT NULL,
  `a_prototype` tinyint(1) NOT NULL DEFAULT 0,
  `lien_prototype` varchar(255) DEFAULT NULL,
  `validation_admin` tinyint(1) NOT NULL DEFAULT 0,
  `etat_id` tinyint(1) NOT NULL DEFAULT 0,
  `champs_personnalises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`champs_personnalises`)),
  `submission_token` varchar(64) DEFAULT NULL,
  `adresse_ip` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `nom_equipe`, `nombre_membres`, `secteur_id`, `theme_id`, `nom_projet`, `resume`, `description`, `a_prototype`, `lien_prototype`, `validation_admin`, `etat_id`, `champs_personnalises`, `submission_token`, `adresse_ip`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 'Mbaye Dieng', 2, 1, 1, 'eae', 'eeaae', 'eaeaea', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '58ce37c4-a83e-4e8e-ae88-f044e5b67d6e', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 09:13:29', '2025-10-12 10:37:51'),
(2, 'Mbaye Dieng', 2, 2, 2, 'deadaze', 'dadza', 'dazdz', 0, NULL, 0, 1, '{\"profile_type\":\"student\",\"student_school\":\"UAM\",\"student_school_other\":null,\"student_designated\":false,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '94bbd791-0768-4c29-be35-df9e54095d50', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-02 09:29:10', '2025-10-12 10:38:28'),
(3, 'jpioghfc', 2, 3, 3, 'fé\'efé', 'r\"éré\"r\"ér\"', 'ré\"r\"ré\"r\"ér\"r\"r\"\"', 0, NULL, 0, 4, '{\"profile_type\":\"student\",\"student_school\":\"EPT\",\"student_school_other\":null,\"student_designated\":false,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'ef0f5073-1560-427c-a2fb-e5daebfdc3ca', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-02 09:33:40', '2025-10-14 08:03:00'),
(4, 'Mbaye Dieng', 2, 3, 3, 'zdcfvde', 'dzfcs', 'zsadezfr', 0, NULL, 0, 1, '{\"profile_type\":\"student\",\"student_school\":\"OTHER\",\"student_school_other\":\"szdaerfetgry\",\"student_designated\":false,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '447a6c8e-97d3-41c4-9a72-5467508a5b0c', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-02 09:35:33', '2025-10-12 10:38:12'),
(5, 'Mbaye Dieng', 2, 4, 4, 'adazddzdz', 'aadsadeade', 'edeadae', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '4c91059b-e6eb-47ae-80e3-94493b872d28', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 10:02:28', '2025-10-12 10:37:46'),
(7, 'Mbaye Dieng', 2, 5, 6, 'fezef', 'ezfezfzef', 'fzefefezfzr', 0, NULL, 0, 1, '{\"profile_type\":\"student\",\"student_school\":\"UAM\",\"student_school_other\":null,\"student_designated\":false,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'a40bff55-0e0c-47c0-b88b-5587dc1d3c5b', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 10:04:46', '2025-10-12 19:34:17'),
(8, 'Mbaye Dieng', 2, 6, 7, 'da', 'dzad', 'dazdza', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'de228569-a59e-4908-8ae0-1f0e1985740c', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 10:16:36', '2025-10-12 10:37:38'),
(9, 'Mbaye Dieng', 2, 4, 8, 'dede', 'e&éd', 'e&\"édf', 1, 'https://mbaye.com', 0, 4, '{\"profile_type\":\"startup\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":\"zzedz\",\"creation_year\":\"2020\",\"is_senegalese_company\":true,\"capital_majority_sn\":true,\"has_rccm\":true}', '4354bd06-fdd2-4c34-9316-1dabddec0fa6', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 10:17:34', '2025-10-14 07:56:36'),
(10, 'zsdez', 2, 7, 2, 'szade', 'szadez', 'zdaezf', 0, NULL, 0, 4, '{\"profile_type\":\"student\",\"student_school\":\"EPT\",\"student_school_other\":null,\"student_designated\":true,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '9f52e7bc-0c3e-4045-b109-d15bf916ee30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-10-02 10:18:17', '2025-10-14 07:56:36'),
(11, 'Mbaye Dieng', 2, 8, 9, 'd', 'ezade', 'dadeza', 0, NULL, 0, 4, '{\"profile_type\":\"student\",\"student_school\":\"UAM\",\"student_school_other\":null,\"student_designated\":false,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'f572130c-562e-4444-b9a7-48641cfccdf0', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 08:30:27', '2025-10-14 07:56:36'),
(12, '\'validation_admin\'     => false,', 2, 9, 10, 'zdefr', 'eaz', 'adefzr', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '4f146e10-b75a-48ae-80e7-207c3c94c768', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 09:17:45', '2025-10-13 10:54:24'),
(13, 'test', 2, 10, 1, 'test', 'test', 'test', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '2dec1aab-e0ac-44a3-843b-198fc57eabdc', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 10:10:35', '2025-10-03 10:10:35'),
(14, 'Mbaye Dieng', 2, 4, 4, 'déed', 'déedée', 'dé\"d\"é', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'a976ad3d-d402-45d0-a351-db6f10433e37', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 12:13:57', '2025-10-13 12:18:01'),
(15, 'Mbaye Dieng', 2, 4, 4, 'zed', 'dezdez', 'dzdze', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '92c5db27-d46c-41b0-b479-7c77ac267894', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 12:15:54', '2025-10-03 12:15:54'),
(16, 'test', 2, 10, 1, 'test', 'test', 'test', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '62854e03-4ad1-4b51-ab84-5b9336ff112f', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-03 12:21:33', '2025-10-03 12:21:33'),
(17, 'edéz', 2, 7, 11, 'fe', 'ef', 'frzfzr', 1, 'https://fzefzfezf/', 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'a9b766bd-2ea0-4562-84e9-e82077bc20c3', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-10 12:50:15', '2025-10-10 12:50:15'),
(18, 'digipols system', 2, 5, 12, 'digital system  la premiere plateforme plainte et de solution KYC au', '7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.', '7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.\r\n11) Services consulaires & diaspora\r\nProblème. RDV difficiles, manque d’info fiable. Défis.\r\n* Agenda en ligne + file virtuelle + checklist documents multilingue.\r\n* Suivi de demande (statut + délais types) par email/SMS. Livrables. Microsite + webhook SMS + back-office simple. Succès. –50% no-show, satisfaction >80%.\r\n12) Administration verte « sans CAPEX »\r\nProblème. Coûts énergie/papier, pas de suivi. Défis.\r\n* Compteur « papier/énergie »: objectifs hebdo + classement par service.\r\n* Modèles d’achats publics « verts » (gabarits, check-list). Livrables. Dashboard + kits de gabarits. Succès. –30% impressions, –10% conso élec. dans 3 mois.\r\n\r\nFormats attendus (pour cadrer les équipes)\r\n* Canaux: Web léger, WhatsApp, USSD/SMS, IVR; offline-first si possible.\r\n* Data minimale: CSV/Excel, Google Sheets, petites DB; API REST simple.\r\n* Accessibilité: multilingue, lisible, pictos; mobile-first.\r\n* Interop: 1 webhook entrant + 1 export CSV/JSON suffisent en POC.\r\n* Sécurité & éthique: consentement, minimisation des données, journal des accès.\r\nCritères d’évaluation\r\n1. Impact direct usager (temps gagné, résolution, inclusion).\r\n2. Faisabilité 90 jours (coût, dépendances, maintenance).\r\n3. Simplicité & UX (clarté, langues locales, faible bande passante).\r\n4. Mesurabilité (KPI clairs, tableau de bord prêt).\r\n5. Interopérabilité (imports/exports simples, API minimale).7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.\r\n11) Services consulaires & diaspora\r\nProblème. RDV difficiles, manque d’info fiable. Défis.\r\n* Agenda en ligne + file virtuelle + checklist documents multilingue.\r\n* Suivi de demande (statut + délais types) par email/SMS. Livrables. Microsite + webhook SMS + back-office simple. Succès. –50% no-show, satisfaction >80%.\r\n12) Administration verte « sans CAPEX »\r\nProblème. Coûts énergie/papier, pas de suivi. Défis.\r\n* Compteur « papier/énergie »: objectifs hebdo + classement par service.\r\n* Modèles d’achats publics « verts » (gabarits, check-list). Livrables. Dashboard + kits de gabarits. Succès. –30% impressions, –10% conso élec. dans 3 mois.\r\n\r\nFormats attendus (pour cadrer les équipes)\r\n* Canaux: Web léger, WhatsApp, USSD/SMS, IVR; offline-first si possible.\r\n* Data minimale: CSV/Excel, Google Sheets, petites DB; API REST simple.\r\n* Accessibilité: multilingue, lisible, pictos; mobile-first.\r\n* Interop: 1 webhook entrant + 1 export CSV/JSON suffisent en POC.\r\n* Sécurité & éthique: consentement, minimisation des données, journal des accès.\r\nCritères d’évaluation\r\n1. Impact direct usager (temps gagné, résolution, inclusion).\r\n2. Faisabilité 90 jours (coût, dépendances, maintenance).\r\n3. Simplicité & UX (clarté, langues locales, faible bande passante).\r\n4. Mesurabilité (KPI clairs, tableau de bord prêt).\r\n5. Interopérabilité (imports/exports simples, API minimale).7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.', 1, 'https://digipol/africabytes.com/', 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', 'b91cbf1d-e93c-450a-98a9-02ddd64e15e7', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-10 16:41:27', '2025-10-11 13:19:00'),
(19, 'Mbaye Dieng', 2, 6, 13, 'eadazed', '// 5) Avant envoi : pousse les marqueurs anti-bot\r\n    form.addEventListener(\'submit\', function(){\r\n      t.value = Date.now() - startedAt;              // durée en ms\r\n      // ajoute un champ \"gestures\" à ton FormData (il sera envoyé car c\'est un input hidden)\r\n      let g = document.getElementById(\'__gestures\');\r\n      if(!g){ g = document.createElement(\'input\'); g.type=\'hidden\'; g.name=\'gestures\'; g.id=\'__gestures\'; form.appendChild(g); }\r\n      g.value = String(gestures);', 'descr', 1, 'https://deza', 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '017ee2fd-24ce-44d4-b825-a6224f4abfef', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-10 16:42:49', '2025-10-13 10:17:13'),
(20, 'szadz', 2, 4, 4, 'dez', 'defz', 'edfze', 0, NULL, 0, 1, '{\"profile_type\":\"other\",\"student_school\":null,\"student_school_other\":null,\"student_designated\":null,\"startup_name\":null,\"creation_year\":null,\"is_senegalese_company\":null,\"capital_majority_sn\":null,\"has_rccm\":null}', '2d22b4b2-20ed-4413-88c9-2129bd7be397', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-10-10 17:31:31', '2025-10-13 10:54:15');

-- --------------------------------------------------------

--
-- Structure de la table `projets_jury`
--

CREATE TABLE `projets_jury` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `jury_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projets_jury`
--

INSERT INTO `projets_jury` (`id`, `projet_id`, `jury_id`, `created_at`, `updated_at`) VALUES
(32, 1, 13, NULL, NULL),
(33, 2, 13, NULL, NULL),
(34, 3, 13, NULL, NULL),
(35, 4, 13, NULL, NULL),
(36, 5, 13, NULL, NULL),
(37, 7, 13, NULL, NULL),
(38, 8, 13, NULL, NULL),
(39, 9, 13, NULL, NULL),
(40, 10, 13, NULL, NULL),
(41, 11, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `projet_profils`
--

CREATE TABLE `projet_profils` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `type_profil` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `secteurs`
--

CREATE TABLE `secteurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteurs`
--

INSERT INTO `secteurs` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'digitalisation pratique (plateformes de demande en ligne, archivage numérique, signalement participatif)', NULL, '2025-10-02 09:13:29', '2025-10-02 09:13:29'),
(2, 'plateformes de volontariat, applications de leadership, engagement civique gamifié.', NULL, '2025-10-02 09:29:10', '2025-10-02 09:29:10'),
(3, 'applis pour fluidifier le transport urbain (info trafic en temps réel, partage de trajets).', NULL, '2025-10-02 09:33:40', '2025-10-02 09:33:40'),
(4, 'Infrastructures & Mobilité', NULL, '2025-10-02 10:02:28', '2025-10-02 10:02:28'),
(5, 'Gouvernance, Sécurité & Transparence', NULL, '2025-10-02 10:04:46', '2025-10-02 10:04:46'),
(6, 'Hygiène publique & Prévention sanitaire', NULL, '2025-10-02 10:16:36', '2025-10-02 10:16:36'),
(7, 'Jeunesse, Inclusion & Citoyenneté', NULL, '2025-10-02 10:18:17', '2025-10-02 10:18:17'),
(8, 'Civic Tech', NULL, '2025-10-03 08:30:27', '2025-10-03 08:30:27'),
(9, 'Culture, Artisanat & Patrimoine', NULL, '2025-10-03 09:17:45', '2025-10-03 09:17:45'),
(10, 'Transformation Digitale & Services Publics', NULL, '2025-10-03 10:10:35', '2025-10-03 10:10:35');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('k9I0LAIDGpJ5QqcoSpHasId40OfmSr0VFVzgS38C', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGp2MVlscHYzcnN1R1U0VDAzZnd2WFBTUW5zVzBwdkV3MG1IU1Z6QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1760983112);

-- --------------------------------------------------------

--
-- Structure de la table `submissions`
--

CREATE TABLE `submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `profile_type` enum('student','startup','other') NOT NULL,
  `student_school` varchar(255) DEFAULT NULL,
  `student_designated` tinyint(1) DEFAULT NULL,
  `startup_name` varchar(255) DEFAULT NULL,
  `creation_year` smallint(5) UNSIGNED DEFAULT NULL,
  `not_state_agent` tinyint(1) DEFAULT NULL,
  `team_name` varchar(255) NOT NULL,
  `team_count` tinyint(3) UNSIGNED NOT NULL,
  `team_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`team_members`)),
  `domain` varchar(150) DEFAULT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `description` longtext NOT NULL,
  `has_prototype` tinyint(1) NOT NULL,
  `prototype_link` varchar(255) DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL,
  `submission_token` varchar(64) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `etat_id` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_senegalese_company` tinyint(1) NOT NULL DEFAULT 0,
  `capital_majority_sn` tinyint(1) NOT NULL DEFAULT 0,
  `has_rccm` tinyint(1) DEFAULT NULL,
  `rccm_file_path` varchar(255) DEFAULT NULL,
  `ninea_file_path` varchar(255) DEFAULT NULL,
  `student_school_other` varchar(255) DEFAULT NULL,
  `secteur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `theme_id` bigint(20) UNSIGNED DEFAULT NULL,
  `theme` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `submissions`
--

INSERT INTO `submissions` (`id`, `profile_type`, `student_school`, `student_designated`, `startup_name`, `creation_year`, `not_state_agent`, `team_name`, `team_count`, `team_members`, `domain`, `contact_email`, `contact_phone`, `project_name`, `summary`, `description`, `has_prototype`, `prototype_link`, `terms_accepted`, `submission_token`, `ip_address`, `user_agent`, `etat_id`, `created_at`, `updated_at`, `is_senegalese_company`, `capital_majority_sn`, `has_rccm`, `rccm_file_path`, `ninea_file_path`, `student_school_other`, `secteur_id`, `theme_id`, `theme`) VALUES
(1, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"dezde\",\"phone\":\"0938280000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dzdez\",\"phone\":\"3243\",\"email\":\"daed@gmail.com\",\"role\":\"Membre\"}]', NULL, 'mbayedieng@fpublique.gouv.sn', '0938280000', 'Mbaye Dieng', 'deadz', 'dazdaz', 0, NULL, 1, '7802aaa0-ee41-4c5f-a3da-d83fbc683048', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-01 13:09:45', '2025-10-01 13:09:45', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(2, 'other', NULL, NULL, NULL, NULL, NULL, 'zdae', 2, '[{\"name\":\"dezd\",\"phone\":\"123435\",\"email\":\"mbayediendazdg@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dazd\",\"phone\":\"21324\",\"email\":\"dzedze@gmail.com\",\"role\":\"Membre\"}]', NULL, 'mbayediendazdg@fpublique.gouv.sn', '123435', 'dead', 'dade', 'dazdaz', 0, NULL, 1, '2fcf6fcb-6e10-4833-8de1-a0da133e38b6', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-01 13:39:05', '2025-10-01 13:39:05', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(3, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"dezdez\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dae\",\"phone\":\"772333687346\",\"email\":\"mbaezezye26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'projets d’organisation locale (coopératives de transport, plateformes d’alerte routière).', 'mbaye26.dieng@gmail.com', '772687346', 'dadd', 'azdza', 'dzadz', 0, NULL, 1, '59c534f2-6852-4651-bf36-11964df21af3', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 09:10:24', '2025-10-02 09:10:24', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(4, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"fzde\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"daedza\",\"phone\":\"1322454\",\"email\":\"mbaye2eae6.dieng@gmail.com\",\"role\":\"Membre\"}]', 'digitalisation pratique (plateformes de demande en ligne, archivage numérique, signalement participatif)', 'mbaye26.dieng@gmail.com', '772687346', 'eae', 'eeaae', 'eaeaea', 0, NULL, 1, '58ce37c4-a83e-4e8e-ae88-f044e5b67d6e', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 09:13:29', '2025-10-02 09:13:29', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(5, 'student', 'UAM', 0, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"dezde\",\"phone\":\"0938280000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"daeda\",\"phone\":\"21324\",\"email\":\"mbayediengdeadae@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'plateformes de volontariat, applications de leadership, engagement civique gamifié.', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'deadaze', 'dadza', 'dazdz', 0, NULL, 1, '94bbd791-0768-4c29-be35-df9e54095d50', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-02 09:29:10', '2025-10-02 09:29:10', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(6, 'student', 'EPT', 0, NULL, NULL, NULL, 'jpioghfc', 2, '[{\"name\":\"cezd\",\"phone\":\"32454\",\"email\":\"babacar12018@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"d\\u00e9ede\\u00e9d\",\"phone\":\"134\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'applis pour fluidifier le transport urbain (info trafic en temps réel, partage de trajets).', 'babacar12018@gmail.com', '32454', 'fé\'efé', 'r\"éré\"r\"ér\"', 'ré\"r\"ré\"r\"ér\"r\"r\"\"', 0, NULL, 1, 'ef0f5073-1560-427c-a2fb-e5daebfdc3ca', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-02 09:33:40', '2025-10-02 09:33:40', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(7, 'student', 'OTHER', 0, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"ddadae\",\"phone\":\"0938280000\",\"email\":\"babacar12018@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"ezdfrzerzf\",\"phone\":\"21345\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'applis pour fluidifier le transport urbain (info trafic en temps réel, partage de trajets).', 'babacar12018@gmail.com', '0938280000', 'zdcfvde', 'dzfcs', 'zsadezfr', 0, NULL, 1, '447a6c8e-97d3-41c4-9a72-5467508a5b0c', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-02 09:35:33', '2025-10-02 09:35:33', 0, 0, NULL, NULL, NULL, 'szdaerfetgry', NULL, NULL, ''),
(8, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"daed\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dazdaz\",\"phone\":\"2312435\",\"email\":\"mbayeadeazd26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbaye26.dieng@gmail.com', '772687346', 'adazddzdz', 'aadsadeade', 'edeadae', 0, NULL, 1, '4c91059b-e6eb-47ae-80e3-94493b872d28', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:02:28', '2025-10-02 10:02:28', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(9, 'startup', NULL, NULL, 'Mbaye Dieng', 2020, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"de\\u00e9ded\\u00e9e\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"de\\u00e9dee\\u00e9\",\"phone\":\"32432142\",\"email\":\"mbayedede26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbaye26.dieng@gmail.com', '772687346', 'dede', 'dedede', 'déedéee', 1, 'https://mbaye.com', 1, 'd6bab9f6-c4b2-4bf3-8c88-886f11b71dc8', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:03:32', '2025-10-02 10:03:32', 0, 0, 1, 'submissions/rccm/OJswp8P0lT6YDWtJ6r6eGRHvJMWpRyXx9CVIOx60.png', 'submissions/ninea/guKumy10ASevteZsLACfAPygo7yZappoPs3gURUE.png', NULL, NULL, NULL, ''),
(10, 'student', 'UAM', 0, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"deadez\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dezdez\",\"phone\":\"2132435465\",\"email\":\"mbayegtefze26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Gouvernance, Sécurité & Transparence', 'mbaye26.dieng@gmail.com', '772687346', 'fezef', 'ezfezfzef', 'fzefefezfzr', 0, NULL, 1, 'a40bff55-0e0c-47c0-b88b-5587dc1d3c5b', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:04:46', '2025-10-02 10:04:46', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(11, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"szae\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"zedz\",\"phone\":\"234\",\"email\":\"mbayeezeze26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Hygiène publique & Prévention sanitaire', 'mbaye26.dieng@gmail.com', '772687346', 'da', 'dzad', 'dazdza', 0, NULL, 1, 'de228569-a59e-4908-8ae0-1f0e1985740c', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:16:36', '2025-10-02 10:16:36', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(12, 'startup', NULL, NULL, 'zzedz', 2020, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"eee\",\"phone\":\"4\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dae\",\"phone\":\"213243\",\"email\":\"mbaydedee26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbaye26.dieng@gmail.com', '4', 'dede', 'e&éd', 'e&\"édf', 1, 'https://mbaye.com', 1, '4354bd06-fdd2-4c34-9316-1dabddec0fa6', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:17:34', '2025-10-02 10:17:34', 1, 1, 1, 'submissions/rccm/ZaTpuwsorWvkYxboLYYEm9iPlaBeKFHqP0trezA4.png', 'submissions/ninea/IqEXyA4Yn0pQnVFhb4usD98xlUixp6ryTH0xJsiF.png', NULL, NULL, NULL, ''),
(13, 'student', 'EPT', 1, NULL, NULL, NULL, 'zsdez', 2, '[{\"name\":\"dzae\",\"phone\":\"772687346\",\"email\":\"mbaye26.dieng@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dae\",\"phone\":\"3124\",\"email\":\"mbaydezde26.dieng@gmail.com\",\"role\":\"Membre\"}]', 'Jeunesse, Inclusion & Citoyenneté', 'mbaye26.dieng@gmail.com', '772687346', 'szade', 'szadez', 'zdaezf', 0, NULL, 1, '9f52e7bc-0c3e-4045-b109-d15bf916ee30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 1, '2025-10-02 10:18:17', '2025-10-02 10:18:17', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(14, 'student', 'UAM', 0, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"eze\",\"phone\":\"0938280000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"ez&e\",\"phone\":\"21345\",\"email\":\"fezre@gmail.com\",\"role\":\"Membre\"}]', 'Civic Tech', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'd', 'ezade', 'dadeza', 0, NULL, 1, 'f572130c-562e-4444-b9a7-48641cfccdf0', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 08:30:27', '2025-10-03 08:30:27', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(15, 'other', NULL, NULL, NULL, NULL, NULL, '\'validation_admin\'     => false,', 2, '[{\"name\":\"dezdez\",\"phone\":\"0938280000\",\"email\":\"mbayedisdsdeng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dezdze\",\"phone\":\"0938221380000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Culture, Artisanat & Patrimoine', 'mbayedisdsdeng@fpublique.gouv.sn', '0938280000', 'zdefr', 'eaz', 'adefzr', 0, NULL, 1, '4f146e10-b75a-48ae-80e7-207c3c94c768', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 09:17:45', '2025-10-03 09:17:45', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(16, 'other', NULL, NULL, NULL, NULL, NULL, 'test', 2, '[{\"name\":\"test\",\"phone\":\"1234\",\"email\":\"test@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dazd\",\"phone\":\"21324\",\"email\":\"mbayetestdieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Transformation Digitale & Services Publics', 'test@gmail.com', '1234', 'test', 'test', 'test', 0, NULL, 1, '2dec1aab-e0ac-44a3-843b-198fc57eabdc', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 10:10:35', '2025-10-03 10:10:35', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(17, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"deaea\",\"phone\":\"0938280000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dadea\",\"phone\":\"32313313\",\"email\":\"mbayedededieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'déed', 'déedée', 'dé\"d\"é', 0, NULL, 1, 'a976ad3d-d402-45d0-a351-db6f10433e37', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 12:13:57', '2025-10-03 12:13:57', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(18, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"dezde\",\"phone\":\"0938280000\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dazd\",\"phone\":\"0938280023200\",\"email\":\"mbayediededng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbayedieng@fpublique.gouv.sn', '0938280000', 'zed', 'dezdez', 'dzdze', 0, NULL, 1, '92c5db27-d46c-41b0-b479-7c77ac267894', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 12:15:54', '2025-10-03 12:15:54', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(19, 'other', NULL, NULL, NULL, NULL, NULL, 'test', 2, '[{\"name\":\"test\",\"phone\":\"312431\",\"email\":\"tetestst@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"test\",\"phone\":\"31331\",\"email\":\"testtesttesttest@gmail.com\",\"role\":\"Membre\"}]', 'Transformation Digitale & Services Publics', 'tetestst@gmail.com', '312431', 'test', 'test', 'test', 0, NULL, 1, '62854e03-4ad1-4b51-ab84-5b9336ff112f', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-03 12:21:33', '2025-10-03 12:21:33', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(20, 'other', NULL, NULL, NULL, NULL, NULL, 'edéz', 2, '[{\"name\":\"123\",\"phone\":\"0938280000\",\"email\":\"mbadzeyedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dazd\",\"phone\":\"3432\",\"email\":\"mbayedfeieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Jeunesse, Inclusion & Citoyenneté', 'mbadzeyedieng@fpublique.gouv.sn', '0938280000', 'fe', 'ef', 'frzfzr', 1, 'https://fzefzfezf/', 1, 'a9b766bd-2ea0-4562-84e9-e82077bc20c3', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-10 12:50:15', '2025-10-10 12:50:15', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(21, 'other', NULL, NULL, NULL, NULL, NULL, 'digipols system', 2, '[{\"name\":\"Moustapha Diakhate\",\"phone\":\"774572648\",\"email\":\"moustaphadiakhate1212@gmail.com\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"magath nael\",\"phone\":\"781680443\",\"email\":\"naelmansa@gmail.com\",\"role\":\"Membre\"}]', 'Gouvernance, Sécurité & Transparence', 'moustaphadiakhate1212@gmail.com', '774572648', 'digital system  la premiere plateforme plainte et de solution KYC au', '7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.', '7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.\r\n11) Services consulaires & diaspora\r\nProblème. RDV difficiles, manque d’info fiable. Défis.\r\n* Agenda en ligne + file virtuelle + checklist documents multilingue.\r\n* Suivi de demande (statut + délais types) par email/SMS. Livrables. Microsite + webhook SMS + back-office simple. Succès. –50% no-show, satisfaction >80%.\r\n12) Administration verte « sans CAPEX »\r\nProblème. Coûts énergie/papier, pas de suivi. Défis.\r\n* Compteur « papier/énergie »: objectifs hebdo + classement par service.\r\n* Modèles d’achats publics « verts » (gabarits, check-list). Livrables. Dashboard + kits de gabarits. Succès. –30% impressions, –10% conso élec. dans 3 mois.\r\n\r\nFormats attendus (pour cadrer les équipes)\r\n* Canaux: Web léger, WhatsApp, USSD/SMS, IVR; offline-first si possible.\r\n* Data minimale: CSV/Excel, Google Sheets, petites DB; API REST simple.\r\n* Accessibilité: multilingue, lisible, pictos; mobile-first.\r\n* Interop: 1 webhook entrant + 1 export CSV/JSON suffisent en POC.\r\n* Sécurité & éthique: consentement, minimisation des données, journal des accès.\r\nCritères d’évaluation\r\n1. Impact direct usager (temps gagné, résolution, inclusion).\r\n2. Faisabilité 90 jours (coût, dépendances, maintenance).\r\n3. Simplicité & UX (clarté, langues locales, faible bande passante).\r\n4. Mesurabilité (KPI clairs, tableau de bord prêt).\r\n5. Interopérabilité (imports/exports simples, API minimale).7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.\r\n11) Services consulaires & diaspora\r\nProblème. RDV difficiles, manque d’info fiable. Défis.\r\n* Agenda en ligne + file virtuelle + checklist documents multilingue.\r\n* Suivi de demande (statut + délais types) par email/SMS. Livrables. Microsite + webhook SMS + back-office simple. Succès. –50% no-show, satisfaction >80%.\r\n12) Administration verte « sans CAPEX »\r\nProblème. Coûts énergie/papier, pas de suivi. Défis.\r\n* Compteur « papier/énergie »: objectifs hebdo + classement par service.\r\n* Modèles d’achats publics « verts » (gabarits, check-list). Livrables. Dashboard + kits de gabarits. Succès. –30% impressions, –10% conso élec. dans 3 mois.\r\n\r\nFormats attendus (pour cadrer les équipes)\r\n* Canaux: Web léger, WhatsApp, USSD/SMS, IVR; offline-first si possible.\r\n* Data minimale: CSV/Excel, Google Sheets, petites DB; API REST simple.\r\n* Accessibilité: multilingue, lisible, pictos; mobile-first.\r\n* Interop: 1 webhook entrant + 1 export CSV/JSON suffisent en POC.\r\n* Sécurité & éthique: consentement, minimisation des données, journal des accès.\r\nCritères d’évaluation\r\n1. Impact direct usager (temps gagné, résolution, inclusion).\r\n2. Faisabilité 90 jours (coût, dépendances, maintenance).\r\n3. Simplicité & UX (clarté, langues locales, faible bande passante).\r\n4. Mesurabilité (KPI clairs, tableau de bord prêt).\r\n5. Interopérabilité (imports/exports simples, API minimale).7) Droit d’accès à l’info « 10 jours »\r\nProblème. Demandes sans suite, opacité. Défis.\r\n* Guichet e-demande (accusé auto, délai, responsable désigné).\r\n* Registre public des réponses (données ouvertes + anonymisation). Livrables. Formulaire + pipeline publication. Succès. 90% réponses <10j, 100% demandes tracées.\r\n8) Traçabilité des dépenses locales\r\nProblème. Méfiance sur les petits budgets (routes, écoles). Défis.\r\n* « Facturier public »: engagements, paiements, pièces jointes.\r\n* Carte des projets avec % avancement et photos « avant/après ». Livrables. Tableur normalisé + site vitrine + API CSV/JSON. Succès. 100% des marchés>10 M CFA publiés, visites x3.\r\n9) Hygiène & risques sanitaires communautaires\r\nProblème. Dengue/choléra liés à eau stagnante & déchets. Défis.\r\n* Alerte « eau stagnante » (photo, GPS approximatif) + réponse municipale.\r\n* Rappels SMS lavage mains/traitement eau selon météo locale. Livrables. App/USSD + webhook météo + registre interventions. Succès. –40% gîtes signalés actifs en 30 jours.\r\n10) Identité & e-signature « niveau 1 »\r\nProblème. Dossiers papier, falsifications, lenteur. Défis.\r\n* POC d’Identifiant Usager simplifié (numéro + OTP SMS).\r\n* E-signature simple (hash + horodatage) pour 2 démarches. Livrables. Service OTP, journal de preuves, bouton « signer ». Succès. 80% dossiers signés sans déplacement, 0 litige pilote.', 1, 'https://digipol/africabytes.com/', 1, 'b91cbf1d-e93c-450a-98a9-02ddd64e15e7', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-10 16:41:27', '2025-10-10 16:41:27', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(22, 'other', NULL, NULL, NULL, NULL, NULL, 'Mbaye Dieng', 2, '[{\"name\":\"dzedez\",\"phone\":\"0938280000\",\"email\":\"mbayediddzeeng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"dezdze\",\"phone\":\"21324\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Hygiène publique & Prévention sanitaire', 'mbayediddzeeng@fpublique.gouv.sn', '0938280000', 'eadazed', '// 5) Avant envoi : pousse les marqueurs anti-bot\r\n    form.addEventListener(\'submit\', function(){\r\n      t.value = Date.now() - startedAt;              // durée en ms\r\n      // ajoute un champ \"gestures\" à ton FormData (il sera envoyé car c\'est un input hidden)\r\n      let g = document.getElementById(\'__gestures\');\r\n      if(!g){ g = document.createElement(\'input\'); g.type=\'hidden\'; g.name=\'gestures\'; g.id=\'__gestures\'; form.appendChild(g); }\r\n      g.value = String(gestures);', 'descr', 1, 'https://deza', 1, '017ee2fd-24ce-44d4-b825-a6224f4abfef', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-10 16:42:49', '2025-10-10 16:42:49', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(23, 'other', NULL, NULL, NULL, NULL, NULL, 'szadz', 2, '[{\"name\":\"zdezf\",\"phone\":\"2132435\",\"email\":\"mbayedieng@fpublique.gouv.sn\",\"role\":\"Chef d\'\\u00e9quipe\"},{\"name\":\"ezdazf\",\"phone\":\"31243\",\"email\":\"mbayediengdez@fpublique.gouv.sn\",\"role\":\"Membre\"}]', 'Infrastructures & Mobilité', 'mbayedieng@fpublique.gouv.sn', '2132435', 'dez', 'defz', 'edfze', 0, NULL, 1, '2d22b4b2-20ed-4413-88c9-2129bd7be397', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1, '2025-10-10 17:31:31', '2025-10-10 17:31:31', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Structure de la table `terms`
--

CREATE TABLE `terms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `profile_type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `html` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `secteur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `themes`
--

INSERT INTO `themes` (`id`, `nom`, `description`, `secteur_id`, `profile_type`, `created_at`, `updated_at`) VALUES
(1, 'digitalisation pratique (plateformes de demande en ligne, archivage numérique, signalement participatif)', NULL, 1, 'other', '2025-10-02 09:13:29', '2025-10-02 09:13:29'),
(2, 'plateformes de volontariat, applications de leadership, engagement civique gamifié.', NULL, 2, 'student', '2025-10-02 09:29:10', '2025-10-02 09:29:10'),
(3, 'applis pour fluidifier le transport urbain (info trafic en temps réel, partage de trajets).', NULL, 3, 'student', '2025-10-02 09:33:40', '2025-10-02 09:33:40'),
(4, 'projets d’organisation locale (coopératives de transport, plateformes d’alerte routière).', NULL, 4, 'other', '2025-10-02 10:02:28', '2025-10-02 10:02:28'),
(6, 'applis simples de consultation citoyenne, jeux de sensibilisation à la transparence.', NULL, 5, 'student', '2025-10-02 10:04:46', '2025-10-02 10:04:46'),
(7, 'Initiatives citoyennes numériques pour des quartiers propres et sains (signalement participatif, cartographie).', NULL, 6, NULL, '2025-10-02 10:16:36', '2025-10-02 10:16:36'),
(8, 'Smart airports, logistique intelligente, plateformes multimodales de mobilité.', NULL, 4, NULL, '2025-10-02 10:17:34', '2025-10-02 10:17:34'),
(9, 'Jeux éducatifs Civic Tech : sensibilisation à la lutte contre la corruption, aux valeurs civiques.', NULL, 8, NULL, '2025-10-03 08:30:27', '2025-10-03 08:30:27'),
(10, 'Valorisation du made in Sénégal', NULL, 9, NULL, '2025-10-03 09:17:45', '2025-10-03 09:17:45'),
(11, 'initiatives d’insertion économique, coopératives artisanales, réseaux communautaires', NULL, 7, NULL, '2025-10-10 12:50:15', '2025-10-10 12:50:15'),
(12, 'Plateforme de remontée d\'alerte', NULL, 5, NULL, '2025-10-10 16:41:27', '2025-10-10 16:41:27'),
(13, 'Plateformes collaboratives pour promouvoir l\'hygiène domestique et collective.', NULL, 6, NULL, '2025-10-10 16:42:49', '2025-10-10 16:42:49');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','jury','super_admin','president_jury','equipe_inscription') NOT NULL DEFAULT 'jury',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jury_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profil_id` bigint(20) UNSIGNED DEFAULT NULL,
  `criteres_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criteres_ids`)),
  `secteurs_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`secteurs_ids`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `telephone`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `jury_id`, `profil_id`, `criteres_ids`, `secteurs_ids`) VALUES
(1, 'Arona Dia', 'rone', 'fmamad12345@gmail.com', NULL, NULL, '$2y$12$wFnwSvZhMfkqhxIwtcC7p.iQp3yh5hGpIvDi06jga3ovtdMxjg4H2', 'admin', NULL, '2025-10-03 09:16:32', '2025-10-03 09:16:32', NULL, NULL, NULL, NULL),
(2, '', 'govathon', 'contact@govathon.sn', NULL, '0000-00-00 00:00:00', '$2y$12$wFnwSvZhMfkqhxIwtcC7p.iQp3yh5hGpIvDi06jga3ovtdMxjg4H2', 'super_admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '', 'govathon', 'projets@govathon.sn', NULL, '0000-00-00 00:00:00', '$2y$12$wFnwSvZhMfkqhxIwtcC7p.iQp3yh5hGpIvDi06jga3ovtdMxjg4H2', 'super_admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '', 'govathon', 'inscription@govathon.sn', NULL, '0000-00-00 00:00:00', '$2y$12$wFnwSvZhMfkqhxIwtcC7p.iQp3yh5hGpIvDi06jga3ovtdMxjg4H2', 'equipe_inscription', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Aly', 'Dieng', 'mbayedieng@gmail.com', '0938280000', NULL, '$2y$12$wFnwSvZhMfkqhxIwtcC7p.iQp3yh5hGpIvDi06jga3ovtdMxjg4H2', 'jury', NULL, '2025-10-13 16:58:40', '2025-10-13 21:01:32', 13, NULL, NULL, NULL),
(19, 'Mbaye', 'Dieng', 'mbaye26.dieng@gmail.com', '772687346', NULL, '$2y$12$4cw6KWu0U4hiKb11EcFi5.Pi83p0UiSxJlWeA1ykZdgdQ11rISDK.', 'jury', NULL, '2025-10-13 20:59:02', '2025-10-13 20:59:02', 13, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vote_publics`
--

CREATE TABLE `vote_publics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `projet_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `est_verifie` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `configurations_cle_unique` (`cle`);

--
-- Index pour la table `criteres`
--
ALTER TABLE `criteres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `critere_categorie`
--
ALTER TABLE `critere_categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `critere_categorie_critere_id_foreign` (`critere_id`),
  ADD KEY `critere_categorie_categorie_id_foreign` (`categorie_id`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_projet_id_foreign` (`projet_id`);

--
-- Index pour la table `equipe_membres`
--
ALTER TABLE `equipe_membres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipe_membres_projet_id_foreign` (`projet_id`);

--
-- Index pour la table `etat`
--
ALTER TABLE `etat`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `votes_jury_id_foreign` (`jury_id`),
  ADD KEY `votes_projet_id_foreign` (`projet_id`),
  ADD KEY `votes_phase_id_foreign` (`phase_id`),
  ADD KEY `votes_critere_id_foreign` (`critere_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `groupe_categorie`
--
ALTER TABLE `groupe_categorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groupe_critere_jury_id_categorie_id_unique` (`jury_id`,`categorie_id`),
  ADD KEY `groupe_critere_categorie_id_foreign` (`categorie_id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jury`
--
ALTER TABLE `jury`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jury_categorie_id` (`categorie_id`);

--
-- Index pour la table `jury_critere_phase`
--
ALTER TABLE `jury_critere_phase`
  ADD PRIMARY KEY (`jury_id`,`phase_id`,`critere_id`),
  ADD KEY `jury_critere_phase_phase_id_foreign` (`phase_id`),
  ADD KEY `jury_critere_phase_critere_id_foreign` (`critere_id`);

--
-- Index pour la table `jury_phase`
--
ALTER TABLE `jury_phase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jury_phase_jury_id_phase_id_unique` (`jury_id`,`phase_id`),
  ADD KEY `jury_phase_phase_id_foreign` (`phase_id`);

--
-- Index pour la table `jury_to _del`
--
ALTER TABLE `jury_to _del`
  ADD PRIMARY KEY (`id`),
  ADD KEY `juries_user_id_foreign` (`user_id`),
  ADD KEY `jury_categorie_id_foreign` (`categorie_id`);

--
-- Index pour la table `liste_preselectionnes`
--
ALTER TABLE `liste_preselectionnes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `liste_preselectionnes_projet_id_unique` (`projet_id`);

--
-- Index pour la table `mail_batches`
--
ALTER TABLE `mail_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mail_recipients`
--
ALTER TABLE `mail_recipients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notation`
--
ALTER TABLE `notation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notation_user_id_foreign` (`user_id`),
  ADD KEY `notation_projet_id_foreign` (`projet_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_utilisateur_id_foreign` (`utilisateur_id`);

--
-- Index pour la table `parametrage__selection`
--
ALTER TABLE `parametrage__selection`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `phases`
--
ALTER TABLE `phases`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profils`
--
ALTER TABLE `profils`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `profils_code_unique` (`code`);

--
-- Index pour la table `profil_champs`
--
ALTER TABLE `profil_champs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profil_champ_valeurs`
--
ALTER TABLE `profil_champ_valeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profil_champ_valeurs_projet_profil_id_foreign` (`projet_profil_id`);

--
-- Index pour la table `project_rejections`
--
ALTER TABLE `project_rejections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_rejections_submission_id_foreign` (`submission_id`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projets_submission_token_unique` (`submission_token`),
  ADD KEY `projets_secteur_id_foreign` (`secteur_id`),
  ADD KEY `projets_theme_id_foreign` (`theme_id`);

--
-- Index pour la table `projets_jury`
--
ALTER TABLE `projets_jury`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projets_jury_projet_id_foreign` (`projet_id`),
  ADD KEY `projets_jury_jury_id_foreign` (`jury_id`);

--
-- Index pour la table `projet_profils`
--
ALTER TABLE `projet_profils`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_profils_projet_id_foreign` (`projet_id`);

--
-- Index pour la table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_value_unique` (`value`);

--
-- Index pour la table `secteurs`
--
ALTER TABLE `secteurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submissions_submission_token_unique` (`submission_token`),
  ADD KEY `submissions_secteur_id_foreign` (`secteur_id`),
  ADD KEY `submissions_theme_id_foreign` (`theme_id`);

--
-- Index pour la table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `terms_profile_type_unique` (`profile_type`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `themes_secteur_id_foreign` (`secteur_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_profil_id_foreign` (`profil_id`),
  ADD KEY `users_jury_id_foreign` (`jury_id`);

--
-- Index pour la table `vote_publics`
--
ALTER TABLE `vote_publics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vote_publics_token_unique` (`token`),
  ADD KEY `vote_publics_projet_id_foreign` (`projet_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `configurations`
--
ALTER TABLE `configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `criteres`
--
ALTER TABLE `criteres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `critere_categorie`
--
ALTER TABLE `critere_categorie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `equipe_membres`
--
ALTER TABLE `equipe_membres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `etat`
--
ALTER TABLE `etat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groupe_categorie`
--
ALTER TABLE `groupe_categorie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jury`
--
ALTER TABLE `jury`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `jury_phase`
--
ALTER TABLE `jury_phase`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jury_to _del`
--
ALTER TABLE `jury_to _del`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `liste_preselectionnes`
--
ALTER TABLE `liste_preselectionnes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `mail_batches`
--
ALTER TABLE `mail_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `mail_recipients`
--
ALTER TABLE `mail_recipients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pour la table `notation`
--
ALTER TABLE `notation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `parametrage__selection`
--
ALTER TABLE `parametrage__selection`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `phases`
--
ALTER TABLE `phases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profils`
--
ALTER TABLE `profils`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profil_champs`
--
ALTER TABLE `profil_champs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profil_champ_valeurs`
--
ALTER TABLE `profil_champ_valeurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `project_rejections`
--
ALTER TABLE `project_rejections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `projets_jury`
--
ALTER TABLE `projets_jury`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `projet_profils`
--
ALTER TABLE `projet_profils`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `secteurs`
--
ALTER TABLE `secteurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `vote_publics`
--
ALTER TABLE `vote_publics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `equipe_membres`
--
ALTER TABLE `equipe_membres`
  ADD CONSTRAINT `equipe_membres_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `votes_critere_id_foreign` FOREIGN KEY (`critere_id`) REFERENCES `criteres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_jury_id_foreign` FOREIGN KEY (`jury_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_phase_id_foreign` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `groupe_categorie`
--
ALTER TABLE `groupe_categorie`
  ADD CONSTRAINT `fk_groupe_jury_id` FOREIGN KEY (`jury_id`) REFERENCES `jury` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groupe_critere_categorie_id_foreign` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jury`
--
ALTER TABLE `jury`
  ADD CONSTRAINT `fk_jury_categorie_id` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `jury_critere_phase`
--
ALTER TABLE `jury_critere_phase`
  ADD CONSTRAINT `jury_critere_phase_critere_id_foreign` FOREIGN KEY (`critere_id`) REFERENCES `criteres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_critere_phase_jury_id_foreign` FOREIGN KEY (`jury_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_critere_phase_phase_id_foreign` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jury_phase`
--
ALTER TABLE `jury_phase`
  ADD CONSTRAINT `jury_phase_jury_id_foreign` FOREIGN KEY (`jury_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_phase_phase_id_foreign` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jury_to _del`
--
ALTER TABLE `jury_to _del`
  ADD CONSTRAINT `juries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_categorie_id_foreign` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `liste_preselectionnes`
--
ALTER TABLE `liste_preselectionnes`
  ADD CONSTRAINT `liste_preselectionnes_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notation`
--
ALTER TABLE `notation`
  ADD CONSTRAINT `notation_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notation_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profil_champ_valeurs`
--
ALTER TABLE `profil_champ_valeurs`
  ADD CONSTRAINT `profil_champ_valeurs_projet_profil_id_foreign` FOREIGN KEY (`projet_profil_id`) REFERENCES `projet_profils` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `project_rejections`
--
ALTER TABLE `project_rejections`
  ADD CONSTRAINT `project_rejections_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `projets`
--
ALTER TABLE `projets`
  ADD CONSTRAINT `projets_secteur_id_foreign` FOREIGN KEY (`secteur_id`) REFERENCES `secteurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projets_theme_id_foreign` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `projets_jury`
--
ALTER TABLE `projets_jury`
  ADD CONSTRAINT `projets_jury_jury_id_foreign` FOREIGN KEY (`jury_id`) REFERENCES `jury` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projets_jury_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `projet_profils`
--
ALTER TABLE `projet_profils`
  ADD CONSTRAINT `projet_profils_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_secteur_id_foreign` FOREIGN KEY (`secteur_id`) REFERENCES `secteurs` (`id`),
  ADD CONSTRAINT `submissions_theme_id_foreign` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`);

--
-- Contraintes pour la table `themes`
--
ALTER TABLE `themes`
  ADD CONSTRAINT `themes_secteur_id_foreign` FOREIGN KEY (`secteur_id`) REFERENCES `secteurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_jury_id_foreign` FOREIGN KEY (`jury_id`) REFERENCES `jury` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_profil_id_foreign` FOREIGN KEY (`profil_id`) REFERENCES `profils` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vote_publics`
--
ALTER TABLE `vote_publics`
  ADD CONSTRAINT `vote_publics_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
