-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Lun 08 Octobre 2018 à 15:52
-- Version du serveur :  5.7.23-0ubuntu0.18.04.1
-- Version de PHP :  7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `tamas_prod_base_test`
--

-- --------------------------------------------------------

--
-- Structure de la table `alfa_author`
--

CREATE TABLE `alfa_author` (
  `id` int(11) NOT NULL,
  `canonical_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_author`
--

INSERT INTO `alfa_author` (`id`, `canonical_name`) VALUES
(29, 'Andreas de Buk'),
(15, 'Belford'),
(16, 'Farseby'),
(3, 'Firmin de Beauval'),
(1, 'Geoffrey de Meaux'),
(23, 'Giovanni Bianchini'),
(9, 'Heinrich Selder'),
(21, 'Jacopo Dondi'),
(8, 'Jean de Gène'),
(5, 'Jean de Lignères'),
(7, 'Jean de Monfort'),
(6, 'Jean de Saxe'),
(4, 'Jean des Murs'),
(32, 'Jean Simon de Zelande'),
(2, 'Jean Vimond'),
(24, 'John of Gmunden'),
(10, 'John Somer'),
(17, 'Killingworth'),
(18, 'Laurentinus de Laurentiis'),
(31, 'Martinus de Ziebice'),
(11, 'Nicholas of Lynn'),
(28, 'Nicholaus de Heybech'),
(30, 'Petrus Gaszowiec'),
(25, 'Peurbach'),
(22, 'Prosdocimo de Beldomandi'),
(14, 'Randolphus'),
(26, 'Regiomontanus'),
(13, 'Simon Tunsted'),
(20, 'Tadeus de Parma'),
(19, 'Ugonis de Castella'),
(12, 'William Reed'),
(27, 'Zacut');

-- --------------------------------------------------------

--
-- Structure de la table `alfa_authority`
--

CREATE TABLE `alfa_authority` (
  `id` int(11) NOT NULL,
  `last_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_authority`
--

INSERT INTO `alfa_authority` (`id`, `last_name`, `first_name`) VALUES
(1, 'Van Brummelen', 'Glen'),
(2, 'Leitão', 'Henrique'),
(3, 'Chabás', 'José'),
(4, 'Miolo', 'Laure'),
(5, 'Saby', 'Marie-Madeleine'),
(6, 'Husson', 'Matthieu'),
(7, 'Malpangotto', 'Michela'),
(8, 'Hadravová', 'Alena'),
(9, 'Hadrava', 'Petr'),
(10, 'Nothaft', 'Philipp'),
(11, 'Kremer', 'Richard'),
(12, 'Falk', 'Seb'),
(13, 'Pietroni', 'Silvia');

-- --------------------------------------------------------

--
-- Structure de la table `alfa_library`
--

CREATE TABLE `alfa_library` (
  `id` int(11) NOT NULL,
  `name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_library`
--

INSERT INTO `alfa_library` (`id`, `name`, `city`) VALUES
(1, 'Bibliothèque nationale de France', 'Paris'),
(2, 'Univ. Lib.', 'Aberden'),
(3, 'Bib. des Benedidtinerstifts', 'Admont'),
(4, 'Univ. Bib.', 'Amsterdam'),
(5, 'Univ. Bib.', 'Basel'),
(6, 'Staatsbib.', 'Berlin'),
(7, 'Staatsbib.', 'Bern'),
(8, 'Cusanusstift', 'Bernkastel'),
(9, 'Bib. Com.', 'Bologna'),
(10, 'Univ. Bib.', 'Bonn'),
(11, 'Stedelijke Openbare Bib.', 'Bruges'),
(12, 'Bib. Roy.', 'Brussels'),
(13, 'NSL', 'budapest'),
(14, 'Gonville & Caius Coll', 'Cambridge'),
(15, 'Magdalene college', 'Cambridge'),
(16, 'Peterhouse', 'Cambridge'),
(17, 'St John\'s college', 'Cambridge'),
(18, 'Trinity college', 'Cambridge'),
(19, 'Univ. Lib.', 'Cambridge'),
(20, 'Houghton Lib.', 'Cambridge (USA)'),
(21, 'Bib. Univ.', 'Catania'),
(22, 'BJ', 'Cracow'),
(23, 'Bib. Mub.', 'Dijon'),
(24, 'Sächsische Landesbibl', 'Dresden'),
(25, 'Nat. Lib. of Scotland', 'Edinburgh'),
(26, 'Royal Observatory', 'Edinburgh'),
(27, 'Univ. Bib.', 'Erfurt'),
(28, 'Bib. Com.', 'Ferrara'),
(29, 'Bib. Naz.', 'Florence'),
(30, 'Bib. Med. Laur', 'Florence'),
(31, 'Bib. Naz.', 'Florence'),
(32, 'Univ. Lib.', 'Frankfurt a M.'),
(33, 'Univ. Lib.', 'Freiburg'),
(34, 'BPU', 'Geneva'),
(35, 'Univ. Bib.', 'Göttingen'),
(36, 'Univ. Bib.', 'Heidelberg'),
(37, 'Zisterzienserstift Bibliothek', 'Heiligenkreuz'),
(38, 'Bib.', 'Helmstadt'),
(39, 'Servitenkloster', 'Innsbruck'),
(40, 'Univ. Bib.', 'Innsbruck'),
(41, 'Univ. Lib.', 'Kalinengrad'),
(42, 'Fószékesegyházi Könyvtár', 'Kalocsa'),
(43, ' Bischöffliche Bib.', 'Klagenfurt'),
(44, 'Badlische lB', 'Karlsruhe'),
(45, 'Stiftsbib.', 'Klostenburg'),
(46, 'Univ. Bib.', 'Königsberg'),
(47, 'Univ. Lib.', 'Leipzig'),
(48, 'Oberösterr. Landesmuseum', 'Linz'),
(49, 'Univ. Bib.', 'Lipsko'),
(50, 'Ajuda', 'Lisbon'),
(51, 'British Library', 'London'),
(52, 'Univ. College', 'London'),
(53, 'Tatsbücherei', 'Lüneburg'),
(54, 'Stadtbib.', 'Mainz'),
(55, 'Biblioteca Nacional', 'Madrid'),
(56, 'Escorial Bib. del Monasterio', 'Madrid'),
(57, 'Ottingische bib.', 'Maihingen'),
(58, 'Benediktinerstift', 'Melk'),
(59, 'Stifbib.', 'Melk'),
(60, 'Bib. Mun.', 'Metz'),
(61, 'Biblioteca Ambrosiana', 'Milano'),
(62, 'SB', 'Munich'),
(63, 'Bibliotheca Nazionale', 'Naples'),
(64, 'Yale Univ.', 'New Haven'),
(65, 'Pierpont Morgan', 'New York'),
(66, 'Columbia Univ', 'New York'),
(67, 'Public Library', 'Norwhich'),
(68, 'SB', 'Nürnbarg'),
(69, 'SVK', 'Olomouc'),
(70, 'Bodleian', 'Oxford'),
(71, 'Corpus Christi College', 'Oxford'),
(72, 'Herford College', 'Oxford'),
(73, 'Bi. Univ.', 'Padua'),
(74, 'Bib. Cap.', 'Padua'),
(75, 'Free Lib.', 'Philadelphia'),
(76, 'Penn. Univ. Lib.', 'Philadelphia'),
(77, 'Clementinum', 'Prague'),
(78, 'Narodni Knihovna', 'Prague'),
(79, 'Univ. Lib.', 'Prague'),
(80, 'Univ. Lib', 'Princeton'),
(81, 'Casanatense', 'Rome'),
(82, 'Univ. Bib.', 'Rostock'),
(83, 'Kantonsbibliothek', 'Saint Gallen'),
(84, 'Bib. Univ.', 'Salamaca'),
(85, 'Öffentliche Bib.', 'Stuttgart'),
(86, 'GRäffliche Bib.', 'Tambach'),
(87, 'St. Peter\'s Church', 'Tiverton (Devon)'),
(88, 'Univ. Bib.', 'Torun'),
(89, 'Staadtbib', 'Ulm'),
(90, 'Univ. Bib.', 'Utrecht'),
(91, 'Bib. Ap.', 'Vatican'),
(92, 'Museo Civico Correr', 'Vanice'),
(93, 'ÖNB', 'Vienna'),
(94, 'Herzog August Bib.', 'Wolfenbuttel'),
(95, 'Univ. Bib.', 'Wroclaw'),
(96, 'ZB', 'Zurich'),
(97, 'Ratsschulbibliothek', 'Zwickau'),
(98, 'Staatsbib.', 'Bamberg'),
(99, 'Historischer Archiv des Stadt', 'Cologne'),
(100, 'Univ. of North Carolina at chapel hill', 'Durham'),
(101, 'Universitäts und Landesbibliothek', 'Düsseldorf'),
(102, 'Royal Astron. Society', 'London'),
(103, 'Stadtbib.', 'Lübeck'),
(104, 'Jesus College', 'Oxford'),
(105, 'Magdalen College', 'Oxford'),
(106, 'St. Peter-Stiftsbibl', 'Salzburg'),
(107, 'Biblioteca de la Catedral', 'Segovia'),
(108, 'Dïoz. Bib.', 'Wroclaw'),
(109, 'Landesbibl.', 'Wolfenbuttel'),
(110, 'Biblioteca nacional de portugal', 'Lisbon');

-- --------------------------------------------------------

--
-- Structure de la table `alfa_primary_source`
--

CREATE TABLE `alfa_primary_source` (
  `id` int(11) NOT NULL,
  `library_id` int(11) DEFAULT NULL,
  `collection` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shelfmark` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tpq` int(11) DEFAULT NULL,
  `taq` int(11) DEFAULT NULL,
  `place_of_production` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `historicalActor` longtext COLLATE utf8_unicode_ci,
  `title` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editor` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `early_printed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_primary_source`
--

INSERT INTO `alfa_primary_source` (`id`, `library_id`, `collection`, `shelfmark`, `tpq`, `taq`, `place_of_production`, `historicalActor`, `title`, `editor`, `early_printed`) VALUES
(1, 2, NULL, '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 3, NULL, '2° 461', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 4, NULL, '1334', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 98, NULL, '214', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 5, NULL, 'F II 7', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 6, NULL, 'Lat. F. 246', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 6, NULL, 'Lat. 4° 175', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 6, NULL, 'Lat. 8° 438', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 6, NULL, 'Lat. 4° 23', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 7, NULL, '545', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 8, NULL, '211', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 8, NULL, '212', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 8, NULL, '213', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 9, NULL, '1601', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 9, NULL, '2284', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 10, NULL, '2° 497', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 10, NULL, '2° 498', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 11, NULL, '466', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 12, NULL, '0926-40', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 12, NULL, '1022-47', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 12, NULL, '1086-1115', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 12, NULL, '10117-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 13, NULL, 'Ms 62', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 14, NULL, '110/179', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 14, NULL, '141/179', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 14, NULL, '174', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 14, NULL, '230/116', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 15, 'Pepys', '1662', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 15, 'Pepys', '2329', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 16, NULL, '75.I', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 16, NULL, '250', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 16, NULL, '277', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 17, NULL, 'K.26', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 18, NULL, 'O.3.13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 18, NULL, 'O.8.34', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 18, NULL, 'R.15.18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 18, NULL, 'R.15.21', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 19, '', ' Add. 5943', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 19, NULL, 'Ee.3.61', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 19, NULL, 'Gg.6.3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 19, NULL, 'Li.1.1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 19, NULL, 'Li.1.27', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 19, NULL, 'Mm.3.11', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 19, NULL, 'Mm 4.41', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 20, NULL, '120', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 21, NULL, '85', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 22, NULL, '459', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 22, NULL, '546', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 22, NULL, '547', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 22, NULL, '548', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 22, NULL, '549', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 22, NULL, '550', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 22, NULL, '551', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 22, NULL, '552', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 22, NULL, '553', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 22, NULL, '555', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 22, NULL, '556', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 22, NULL, '557', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 22, NULL, '558', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 22, NULL, '560', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 22, NULL, '563', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 22, NULL, '564', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 22, NULL, '566', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 22, NULL, '568', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 22, NULL, '570', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 22, NULL, '573', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 22, NULL, '574', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 22, NULL, '575', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 22, NULL, '576', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 22, NULL, '577', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 22, NULL, '578', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 22, NULL, '579', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 22, NULL, '589', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 22, NULL, '594', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 22, NULL, '596', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 22, NULL, '597', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 22, NULL, '598', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 22, NULL, '600', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 22, NULL, '601', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 22, NULL, '602', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 22, NULL, '603', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 22, NULL, '604', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 22, NULL, '605', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 22, NULL, '606', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 22, NULL, '607', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 22, NULL, '609', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 22, NULL, '610', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 22, NULL, '611', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 22, NULL, '612', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 22, NULL, '613', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 22, NULL, '614', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 22, NULL, '615', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 22, NULL, '616', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 22, NULL, '617', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 22, NULL, '618', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 22, NULL, '715', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 22, NULL, '846', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 22, NULL, '1391', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 22, NULL, '1841', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 22, NULL, '1844', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 22, NULL, '1846', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 22, NULL, '1848', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 22, NULL, '1849', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 22, NULL, '1852', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 22, NULL, '1858', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 22, NULL, '1859', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 22, NULL, '1864', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 22, NULL, '1865', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 22, NULL, '1915', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 22, NULL, '1916', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 22, NULL, '1917', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 22, NULL, '1918', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 22, NULL, '1958', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 22, NULL, '1927', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 22, NULL, '1966', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 22, NULL, '1967', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 22, NULL, '2252', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 22, NULL, '2478', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 22, NULL, '2480', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 22, NULL, '2650', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 22, NULL, '2664', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 22, NULL, '3224', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 23, NULL, '447', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 24, NULL, 'N 100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 25, NULL, '23.7.11', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 26, 'Crawford Library', 'Cr. 3.28', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 27, NULL, '2° 37', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 27, NULL, 'F. 376', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 27, NULL, 'F. 377', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 27, NULL, 'F. 384', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 27, NULL, 'F. 386', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 27, NULL, 'F; 387', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 27, NULL, 'F. 388', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 27, NULL, 'F. 395', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 27, NULL, 'Q. 355', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 27, NULL, 'Q. 360', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 27, NULL, 'Q; 362', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 27, NULL, 'Q.364', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 27, NULL, 'Q. 366', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 27, NULL, 'Q. 371', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 27, NULL, 'Q. 376', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 27, NULL, 'Q. 382', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 28, 'Ariostea', 'I.147', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 29, 'Magl', 'XX.53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 30, '', 'F; Lat. 131', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 31, 'San Marco', '178', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 31, 'San Marco', '188', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 30, 'San Marco', '184', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 30, 'San marco', '185', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 30, 'Ash.', '134', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 30, 'Ash.', '206', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 31, 'San Marco', '192', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 30, 'San Marco', '193', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 32, 'Barth', '134', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 33, '', '28', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(157, 33, '', '537', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 34, '', '80', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 35, 'Hist nat.', '86', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 36, NULL, '1481', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 36, NULL, '2503', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 37, NULL, 'Cod. 302', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 38, NULL, '1127', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 39, NULL, 'I.b.62', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 40, NULL, '750', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 41, NULL, '2° 159', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 41, NULL, '2° 1755\r\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 42, NULL, '326', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 43, NULL, 'XXIX.c.9', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 44, NULL, 'Rast. 36', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 45, NULL, '1238', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 46, NULL, '2° 1735', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 47, NULL, '1469', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 47, NULL, '1472', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(175, 47, NULL, '1476', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 47, NULL, '1477', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 47, NULL, '1481', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 47, NULL, '1482', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 47, NULL, '1484', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 48, NULL, 'Ms. 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 49, NULL, '1473', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 50, NULL, '52-XII-35', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(183, 51, NULL, 'Add 10,628', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 51, NULL, 'Add 15,209', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 51, NULL, '17,358', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 51, NULL, 'Add 22,113', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(187, 51, NULL, '22,808', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 51, NULL, 'Add 24,070', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 51, NULL, 'Add 24,071', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 51, NULL, 'Arundel 88', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 51, 'Cotton', 'Ms. Vesp. E. vii', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 51, 'Cotton', 'Vitellius A.i', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(193, 51, 'Egerton', '847', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 51, 'Egerton', '889', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 51, 'Harley', '321', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 51, 'Harley', '973', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 51, 'Harley', '1009', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(198, 51, 'Harley', '1785', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 51, 'Harley', '5311', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 51, 'Bib. Reg.', '2 B. viii', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 51, 'Royal', '12 C.xvii', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 51, 'Royal', '12 D.vi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 51, 'Royal', '12 E.xvi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(204, 51, 'Royal', '2 B.viii', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 51, 'Sloane', '282', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 51, 'Sloane', '407', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 51, 'Sloane', '807', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 51, 'Sloane', '2250', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 51, 'Sloane', '2397', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 51, 'Sloane', '2465', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 52, '', 'Lat. 16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 53, '', 'Misc. D. 2°11', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 53, '', 'Misc. D. 2°13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 54, '', '530a', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 55, '', '3306', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(216, 55, '', '4238', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(217, 55, '', '9288', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 55, '', '10,0002', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(219, 56, '', 'I-II-7', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 56, '', 'E-III-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 57, NULL, 'II-f-1.2° 110', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(222, 57, NULL, 'II-1.4° 73', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(223, 59, NULL, '51', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(224, 59, NULL, '367', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 59, NULL, '711', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(226, 60, NULL, '287', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 61, NULL, 'C. 139 Inf.', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 61, NULL, 'C. 207 Inf.', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 61, NULL, 'C. 221 Inf.', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 61, NULL, 'MI. D. 28 Inf.', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(231, 62, 'Cgm', '595', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 62, 'Cgm', '737', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 62, 'Cgm', '739', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 62, 'Clm', '27', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(235, 62, 'Clm', '83', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 62, 'Clm', '51', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 62, 'Clm', '214', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 62, 'Clm', '5460', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 62, 'Clm', '8950', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 62, 'Clm', '10661', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 62, 'Clm', '10691', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(242, 62, 'Clm', '14111', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(243, 62, 'Clm', '14783', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 62, 'Clm', '19501', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 62, 'Clm', '19550', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 62, 'Clm', '19960', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(247, 62, 'Clm', '25004', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(248, 62, 'Clm', '25011', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(249, 62, 'Clm', '26666', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 62, 'Clm', '26667', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(251, 62, 'Clm', '28229', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 62, 'Clm', '28992', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 63, NULL, 'VIII.C.34', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 63, NULL, 'VIII.C.36', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(255, 63, NULL, 'VIII.D.31', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(256, 64, NULL, '836', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 64, NULL, '794', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(258, 65, 'Büthler', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(259, 66, 'Plimpton', '162', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(260, 66, 'Plimpton', '175', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 67, NULL, 'TC 28/1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(262, 68, 'Cent.', 'V.36', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 68, 'Cent.', 'V.53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 68, 'Cent.', 'V.57', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 68, 'Cent.', 'V.58', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(266, 68, 'Cent.', 'V.59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(267, 68, 'Cent.', 'V.63', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(268, 68, 'Cent.', 'VI.16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 68, 'Cent.', 'VI.18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 68, 'Cent.', 'VI.23', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 69, '', 'M-I-90', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 70, 'Bodley', '300', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 70, 'Bodley', '432', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(274, 70, 'Bodley', '472', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(275, 70, 'Bodley', '491', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(276, 70, 'Bodley', '790', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(277, 70, 'Asmole', '191', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 70, 'Ashmole', '391', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 70, 'Ashmole', '789', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 70, 'AShmole', '1796', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 70, 'Ashmole Rolls ', '6', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(282, 70, 'Ashmole Rolls', '55', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(283, 70, 'Canon misc.', '27', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(284, 70, 'Canon misc.', '436', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(285, 70, 'Canon misc.', '436', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286, 70, 'Canon misc.', '499', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(287, 70, 'Canon misc.', '501', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(288, 70, 'Canon misc.', '554', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 70, 'Laud. misc.', '594', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 70, 'Laud. misc.', '662', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(291, 70, 'Laud. misc.', '674', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 70, 'Lyell', '37', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(293, 70, 'Rawlinson', 'D. 238', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(294, 70, 'Rawlinson', 'D.928', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 70, 'Rawlinson', 'D.1227', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(296, 70, 'Savile', '37', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(297, 70, 'Selden supra', '90', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(298, 70, 'Wood', 'D.8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(299, 70, 'Digby', '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 70, 'Digby', '48', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(301, 70, 'Digby', '57', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(302, 70, 'Digby', '72', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(303, 70, 'Digby', '92', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(304, 70, 'Digby', '97', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 70, 'Digby', '167', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(306, 70, 'Digby', '168', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(307, 70, 'Digby', '228', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(308, 71, '', '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(309, 71, '', '144', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(310, 72, '', 'E.3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 72, '', 'E.4', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(312, 73, '', 'Ms 643', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(313, 74, NULL, 'D. 39', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 1, NULL, 'Lat. 7197', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 1, NULL, 'Lat. 7269', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 1, NULL, 'Lat. 7270', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(317, 1, NULL, 'Lat. 7271', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(318, 1, NULL, 'Lat. 7277', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 1, NULL, 'Lat. 7279', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 1, NULL, 'Lat. 7280', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(321, 1, NULL, 'Lat. 7281', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 1, NULL, 'Lat. 7282', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(323, 1, NULL, 'Lat. 7283', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 1, NULL, 'Lat. 7285', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 1, NULL, 'Lat. 7286', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 1, NULL, 'Lat. 7286A', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 1, NULL, 'Lat. 7286C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(328, 1, NULL, 'Lat. 7287', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(329, 1, NULL, 'Lat. 7288', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 1, NULL, 'Lat. 7290', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(331, 1, NULL, 'Lat. 7290A', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(332, 1, NULL, 'Lat. 7292', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(333, 1, NULL, 'Lat. 7295', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(334, 1, NULL, 'Lat. 7300A', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(335, 1, NULL, 'Lat. 7316A', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(336, 1, NULL, 'Lat. 7322', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(337, 1, NULL, 'Lat. 7329', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(338, 1, NULL, 'Lat. 7350', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(339, 1, NULL, 'Lat. 7378A', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 1, NULL, 'Lat. 7384', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(341, 1, NULL, 'Lat. 7401', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(342, 1, NULL, 'Lat. 7403', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(343, 1, NULL, 'Lat. 7405', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(344, 1, NULL, 'Lat. 7407', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(345, 1, NULL, 'Lat. 7408', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(346, 1, NULL, 'Lat. 7427', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(347, 1, NULL, 'Lat. 7432', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(348, 1, NULL, 'Lat. 7482', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(349, 1, NULL, 'Lat. 10263', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 1, NULL, 'Lat. 10264', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(351, 1, NULL, 'Lat. 10265', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(352, 1, NULL, 'Lat. 10266', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(353, 1, NULL, 'Lat. 10267', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(354, 1, NULL, 'Lat. 10271', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 1, NULL, 'Lat. 11243', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(356, 1, NULL, 'Lat. 11250', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(357, 1, NULL, 'Lat. 11252', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 1, NULL, 'Lat. 13014', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(359, 1, NULL, 'Lat. 14481', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(360, 1, NULL, 'Lat. 15104', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 1, NULL, 'Lat. 16212', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(362, 1, NULL, 'Lat. 17282', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(363, 1, NULL, 'Lat. 17295', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 1, NULL, 'Lat. 18504', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(365, 1, NULL, 'NAL 398', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(366, 1, NULL, 'NAL 595', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 75, '', 'Ms Lewis E.3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(368, 76, NULL, 'LJS 174', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(369, 77, NULL, 'NL X B3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 77, NULL, 'NL VIII G24', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 77, NULL, '1G6', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(372, 77, NULL, '44e8', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(373, 77, NULL, 'III C 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(374, 77, NULL, 'III C 17', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(375, 77, NULL, 'NL IV G10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(376, 77, NULL, 'NL XIII F25', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(377, 77, NULL, 'NL IV G10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(378, 77, NULL, 'NL V G18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 77, NULL, 'NL V 4B', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(380, 77, NULL, 'NL IV C2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(381, 78, NULL, '1524', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 78, NULL, '1826', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(383, 78, NULL, 'XIV.B.3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(384, 78, NULL, 'XIV.F.10(2581)', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(385, 79, NULL, '2815', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(386, 80, 'Kane', '51', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(387, 81, NULL, 'Ms. 643', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(388, 81, NULL, 'MS 653', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(389, 81, NULL, 'MS 1673', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(390, 82, NULL, ' MS math phys 4°1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(391, 83, NULL, '426', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(392, 84, NULL, '2621', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(393, 85, NULL, 'mat 4°34', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(394, 86, NULL, 'E355', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(395, 87, NULL, 'Horae', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(396, 88, NULL, '74', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(397, 88, NULL, '41', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(398, 89, NULL, '13883', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(399, 90, NULL, '724', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(400, 91, 'Barberini', 'Lat. 156', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(401, 91, 'Barberini', 'Lat. 343', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(402, 91, 'Barberini', 'Lat. 350', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(403, 91, 'MS Ottob ', 'Lat. 1634', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(404, 91, 'Ms Ottob.', 'Lat. 1826', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(405, 91, 'Ms Ottob.', 'Lat 2252', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(406, 91, 'Pal. lat.', '446', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(407, 91, 'Pal. lat.', '1340', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(408, 91, 'Pal. lat.', '1354', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(409, 91, 'Pal. lat.', '1367', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(410, 91, 'Pal. lat.', '1368', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(411, 91, 'Pal. lat.', '1369', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(412, 91, 'Pal. lat.', '1370', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(413, 91, 'Pal. lat.', '1373', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(414, 91, 'Pal. lat.', '1374', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(415, 91, 'Pal. lat.', '1375', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(416, 91, 'Pal. lat.', '1376', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(417, 91, 'Pal. lat.', '1381', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(418, 91, 'Pal. lat.', '1384', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(419, 91, 'Pal. lat.', '1385', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(420, 91, 'Pal. lat.', '1390', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(421, 91, 'Pal. lat.', '1391', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(422, 91, 'Pal. lat.', '1392', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(423, 91, 'Pal. lat.', '1403', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(424, 91, 'Pal. lat.', '1405', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(425, 91, 'Pal. lat.', '1409', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(426, 91, 'Pal. lat.', '1411', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(427, 91, 'Pal. lat.', '1412', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(428, 91, 'Pal. lat.', '1413', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(429, 91, 'Pal. lat.', '1416', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(430, 91, 'Pal. lat.', '1420', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(431, 91, 'Pal. lat.', '1435', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(432, 91, 'Pal. lat.', '1436', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(433, 91, 'Pal. lat.', '1439', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(434, 91, 'Pal. lat.', '1445', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(435, 91, 'Pal. lat.', '1446', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(436, 91, 'Pal. lat.', '1452', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(437, 91, 'Reg. lat.', '1241', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(438, 91, 'Regina Sueviae', 'lat. 155', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(439, 91, 'Urbin lat.', '268', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(440, 91, 'Urbin lat.', '1399', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(441, 91, 'Vat. lat.', '2228', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(442, 91, 'Vat. lat.', '3099', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(443, 91, 'Vat. lat.', '3115', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(444, 91, 'Vat. lat.', '3116', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(445, 91, 'Vat. lat.', '3117', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(446, 91, 'Vat. lat.', '3126', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(447, 91, 'Vat. lat.', '3538', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(448, 91, 'Vat. lat.', '4087', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(449, 91, 'Vat. lat.', '4592', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(450, 91, 'Vat. lat.', '6001', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(451, 91, 'Vat. lat.', '8174', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(452, 91, 'Vat. lat.', '8951', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(453, 92, 'Cicogna', '3748', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(454, 92, NULL, 'Lat. 342', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(455, 92, NULL, 'Lat. 823', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(456, 93, NULL, '2282', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(457, 93, NULL, '2288', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(458, 93, NULL, '2293', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(459, 93, NULL, '2332', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(460, 93, NULL, '2352', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(461, 93, NULL, '2380', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(462, 93, NULL, '2440', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(463, 93, NULL, '2467', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(464, 93, NULL, '3872', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(465, 93, NULL, '5144', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(466, 93, NULL, '5145', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(467, 93, NULL, '5151', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(468, 93, NULL, '5184', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(469, 93, NULL, '5192', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(470, 93, NULL, '5206', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(471, 93, NULL, '5210', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(472, 93, NULL, '5226', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(473, 93, NULL, '5227', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(474, 93, NULL, '5228', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(475, 93, NULL, '5240', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(476, 93, NULL, '5245', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(477, 93, NULL, '5268', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(478, 93, NULL, '5273', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(479, 93, NULL, '5275', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(480, 93, NULL, '5291', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(481, 93, NULL, '5292', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(482, 93, NULL, '5299', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(483, 93, NULL, '5334', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(484, 93, NULL, '5337', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(485, 93, NULL, '5412', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(486, 93, NULL, '5415', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(487, 94, NULL, '42.2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(488, 94, NULL, '69.9', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(489, 94, NULL, '2401', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(490, 94, NULL, '2551', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(491, 94, NULL, '2637', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(492, 94, NULL, '2814', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(493, 94, NULL, '2816', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(494, 94, NULL, '2834', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(495, 94, NULL, '2841', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(496, 94, NULL, '2891', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(497, 94, NULL, '3749', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(498, 95, NULL, '4719', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(499, 95, NULL, '4F17', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(500, 95, NULL, '4F19', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(501, 95, NULL, 'R332', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(502, 96, NULL, 'Gal. II 88 nr 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(503, 97, NULL, 'Cod. XXVI-10, adl. No. 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(504, 97, NULL, 'Cod. XXII, VIII.10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(505, 97, NULL, '11, VII 30', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(506, 94, NULL, '3071', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(507, 107, NULL, 'Ms 84', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(508, 106, NULL, 'Inc. 699', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(509, 105, NULL, '182', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(510, 104, NULL, '46', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(511, 103, NULL, '2° 239', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(512, 102, NULL, 'QB 7/1021', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(513, 101, NULL, 'F. 13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(514, 100, NULL, '522', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(515, 99, NULL, 'W* 178', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(516, 108, NULL, '2° 105', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(517, 77, NULL, 'XA 23', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(518, 97, NULL, 'XIII C17', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(519, 97, NULL, 'NL XIII F25', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(520, 1, NULL, 'lat. 7443', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(521, 70, 'Savile', '39', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(522, 62, NULL, 'Clm 6748', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(523, 58, NULL, 'Ms. 601', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(524, 31, 'Covent. Soppr', 'J. VIII.28', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(525, 22, NULL, '1920', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(526, 109, NULL, '3071', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(535, 62, NULL, '4 Inc.s.a.380', 1483, 1483, NULL, 'John of Saxony', 'Tabule astronomice illustrissimi Alfontij regis Castelle', 'Erhard Ratdolt', 1),
(536, NULL, NULL, NULL, 1484, 1484, 'Leipzig', 'John of Murs', 'Tabulae coniunctionum et oppositionum', 'Marcus Brandis', 1),
(537, NULL, NULL, NULL, 1490, 1490, 'Augsburg', 'Johannes Regiomontanus', 'Tabuale directionum et profectionum', 'Erhard Ratdolt', 1),
(538, 62, NULL, '4 Inc.s.a.1559', 1490, 1490, 'augsburg', 'Johannes Regiomontanus', 'Tabulae quantitatis', 'Erhard Ratdolt', 1),
(539, NULL, NULL, NULL, 1492, 1492, 'Venice', NULL, 'Tabulae astronomicae Alfonsi Regis', 'Johannes Lucilius Santritter', 1),
(540, 62, NULL, '4 Inc.s.a.1185', 1495, 1495, 'Venice', 'Giovanni Bianchini', 'Tabulae celestium motuum earumque canones', 'Venice', 1),
(541, 110, NULL, 'Inc-187', 1496, 1496, 'Leiria', 'Abraham Zacut', 'Almanach perpetuum', 'Samuel Dortas', 1),
(542, 62, NULL, '4 Inc.s.a.1542', 1498, 1498, 'Venice', 'Johannes Regiomontanus', 'Ephemerides sive Almanach perpetuum', 'Johannes Lucilius Santritter', 1),
(543, NULL, NULL, NULL, 1498, 1498, 'Rome', 'Alfonso de Cordoba', 'Lumen caeli seu Expositio instrumenti astronomici a se excogitati', 'Johannes Besicken', 1);

-- --------------------------------------------------------

--
-- Structure de la table `alfa_primary_source_work`
--

CREATE TABLE `alfa_primary_source_work` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `primary_source_id` int(11) NOT NULL,
  `locus_from` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locus_to` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authority_id` int(11) NOT NULL,
  `info_source` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_primary_source_work`
--

INSERT INTO `alfa_primary_source_work` (`id`, `work_id`, `primary_source_id`, `locus_from`, `locus_to`, `authority_id`, `info_source`) VALUES
(1, 90, 400, '1r', '145v', 3, 'p'),
(2, 40, 402, '83r', '88v', 3, 'p'),
(3, 21, 403, '26v', '96v', 3, 'p'),
(4, 20, 403, '99r', '157r', 3, 'p'),
(5, 66, 403, '158r', '166v', 3, 'p'),
(6, 61, 404, '41r', '46r', 3, 'p'),
(7, 64, 404, '51r', '61v', 3, 'p'),
(8, 88, 404, '80r', '113r', 3, 'p'),
(9, 65, 404, '130r', '140r', 3, 'p'),
(10, 84, 404, '152v', '153r', 3, 'p'),
(11, 85, 404, '153v', '155v', 3, 'p'),
(12, 88, 405, '8r', '39r', 3, 'p'),
(13, 65, 405, '47r', '67r', 3, 'p'),
(14, 72, 406, '87r', '88r', 3, 'p'),
(15, 88, 406, '90r', '93r', 3, 'p'),
(16, 74, 406, '219r', '219r', 3, 'p'),
(17, 60, 406, '219v', '227v', 3, 'p'),
(18, 78, 406, '228r', '229r', 3, 'p'),
(19, 88, 406, '230r', '234v', 3, 'p'),
(20, 88, 406, '237r', '253v', 3, 'p'),
(21, 89, 407, '53v', '58v', 3, 'p'),
(22, 5, 408, '46r', '46r', 3, 'p'),
(23, 6, 408, '46v', '47v', 3, 'p'),
(24, 6, 408, '49r', '49v', 3, 'p'),
(25, 76, 408, '50v', '53r', 3, 'p'),
(26, 75, 408, '60r', '60v', 3, 'p'),
(27, 68, 408, '61v', '74v', 3, 'p'),
(28, 65, 408, '74v', '78v', 3, 'p'),
(29, 67, 408, '77v', '78v', 3, 'p'),
(30, 53, 408, '109r', '119v', 3, 'p'),
(31, 88, 409, '1v', '26v', 3, 'p'),
(32, 65, 409, '27v', '40v', 3, 'p'),
(33, 65, 409, '70v', '77r', 3, 'p'),
(34, 65, 409, '90r', '90v', 3, 'p'),
(35, 40, 409, '49r', '56r', 3, 'p'),
(36, 76, 409, '56r', '59r', 3, 'p'),
(37, 21, 409, '59v', '60v', 3, 'p'),
(38, 65, 409, '61r', '62r', 3, 'p'),
(39, 78, 409, '64r', '68v', 3, 'p'),
(40, 29, 410, '11r', '33r', 3, 'p'),
(41, 19, 411, '1r', '53v', 3, 'p'),
(42, 88, 412, '1r', '18r', 3, 'p'),
(43, 53, 413, '18r', '32v', 3, 'p'),
(44, 64, 413, '53r', '61v', 3, 'p'),
(45, 88, 413, '77v', '97r', 3, 'p'),
(46, 65, 413, '97v', '108r', 3, 'p'),
(47, 88, 414, '2r', '24v', 3, 'p'),
(48, 65, 414, '26r', '46v', 3, 'p'),
(49, 65, 414, '50v', '51r', 3, 'p'),
(50, 46, 414, '47r', '47r', 3, 'p'),
(51, 63, 414, '51v', '51v', 3, 'p'),
(52, 28, 415, '1r', '8v', 3, 'p'),
(53, 57, 415, '8v', '10v', 3, 'p'),
(54, 23, 415, '21r', '43r', 3, 'p'),
(55, 29, 415, '45r', '52v', 3, 'p'),
(56, 18, 415, '55r', '106r', 3, 'p'),
(57, 16, 415, '106v', '171v', 3, 'p'),
(58, 29, 415, '185r', '262v', 3, 'p'),
(59, 88, 416, '1r', '18v', 3, 'p'),
(60, 88, 416, '24v', '34r', 3, 'p'),
(61, 65, 416, '51v', '56r', 3, 'p'),
(62, 63, 416, '102r', '134r', 3, 'p'),
(63, 64, 416, '170v', '177v', 3, 'p'),
(64, 51, 416, '221r', '222r', 3, 'p'),
(65, 5, 416, '350r', '350r', 3, 'p'),
(66, 6, 416, '351r', '352r', 3, 'p'),
(67, 40, 416, '355r', '380v', 3, 'p'),
(68, 39, 416, '381r', '383r', 3, 'p'),
(69, 76, 416, '389r', '391v', 3, 'p'),
(70, 75, 416, '392r', '392r', 3, 'p'),
(71, 67, 416, '393v', '394r', 3, 'p'),
(72, 68, 416, '394v', '406r', 3, 'p'),
(73, 23, 417, '113r', '120v', 3, 'p'),
(74, 22, 417, '121r', '123r', 3, 'p'),
(75, 53, 420, '96r', '116v', 3, 'p'),
(76, 88, 420, '119v', '120v', 3, 'p'),
(77, 88, 420, '121v', '151r', 3, 'p'),
(78, 88, 420, '165r', '182v', 3, 'p'),
(79, 88, 420, '190r', '194v', 3, 'p'),
(80, 15, 421, '1r', '59v', 3, 'p'),
(81, 61, 423, '1v', '3v', 3, 'p'),
(82, 88, 423, '4r', '14r', 3, 'p'),
(83, 88, 423, '15v', '34v', 3, 'p'),
(84, 64, 423, '39r', '51r', 3, 'p'),
(85, 88, 424, '1r', '26v', 3, 'p'),
(86, 88, 424, '29v', '39r', 3, 'p'),
(87, 53, 424, '45r', '53r', 3, 'p'),
(88, 50, 425, '1r', '52r', 3, 'p'),
(89, 23, 425, '52v', '59v', 3, 'p'),
(90, 20, 426, '1r', '34v', 3, 'p'),
(91, 21, 426, '36r', '97v', 3, 'p'),
(92, 53, 427, '10r', '24v', 3, 'p'),
(93, 64, 427, '35v', '71v', 3, 'p'),
(94, 88, 427, '72v', '94v', 3, 'p'),
(95, 65, 427, '95r', '102r', 3, 'p'),
(96, 65, 427, '109r', '114v', 3, 'p'),
(97, 65, 427, '117r', '120r', 3, 'p'),
(98, 63, 427, '103v', '108v', 3, 'p'),
(99, 88, 428, '15r', '42v', 3, 'p'),
(100, 65, 428, '46r', '51r', 3, 'p'),
(101, 65, 428, '55r', '58r', 3, 'p'),
(102, 53, 428, '110r', '122v', 3, 'p'),
(103, 53, 428, '141r', '141v', 3, 'p'),
(104, 39, 428, '149r', '154v', 3, 'p'),
(105, 53, 432, '13r', '17v', 3, 'p'),
(106, 6, 432, '87v', '88v', 3, 'p'),
(107, 5, 432, '89r', '89r', 3, 'p'),
(108, 88, 432, '93r', '95r', 3, 'p'),
(109, 17, 433, '1r', '8r', 3, 'p'),
(110, 15, 433, '13r', '39r', 3, 'p'),
(111, 60, 435, '36r', '47v', 3, 'p'),
(112, 64, 437, '109r', '151v', 3, 'p'),
(113, 46, 437, '152v', '152v', 3, 'p'),
(114, 65, 437, '158r', '164v', 3, 'p'),
(115, 30, 439, '2r', '11r', 3, 'p'),
(116, 31, 439, '12v', '28r', 3, 'p'),
(117, 58, 440, '16r', '21r', 3, 'p'),
(118, 66, 440, '22r', '26r', 3, 'p'),
(119, 25, 441, '1r', '16r', 3, 'p'),
(120, 40, 443, '57v', '72r', 3, 'p'),
(121, 74, 444, '1r', '6v', 3, 'p'),
(122, 88, 444, '9r', '10r', 3, 'p'),
(123, 78, 444, '11r', '25v', 3, 'p'),
(124, 78, 444, '29r', '34v', 3, 'p'),
(125, 27, 447, '6r', '7r', 3, 'p'),
(126, 16, 447, '99r', '114r', 3, 'p'),
(127, 53, 448, '102r', '113r', 3, 'p'),
(128, 29, 449, '21r', '108r', 3, 'p'),
(129, 18, 449, '110v', '113v', 3, 'p'),
(130, 88, 452, '1r', '40v', 3, 'p'),
(131, 64, 387, '105r', '108v', 3, 'p'),
(132, 88, 388, '3r', '26r', 3, 'p'),
(133, 72, 388, '26r', '32r', 3, 'p'),
(134, 65, 388, '34v', '37r', 3, 'p'),
(135, 65, 388, '40r', '64r', 3, 'p'),
(136, 53, 388, '107r', '114v', 3, 'p'),
(137, 28, 389, '1r', '10v', 3, 'p'),
(138, 29, 389, '11r', '55v', 3, 'p'),
(139, 29, 389, '56v', '60r', 3, 'p'),
(140, 29, 389, '63r', '74r', 3, 'p'),
(141, 76, 389, '80v', '83r', 3, 'p'),
(142, 40, 389, '109v', '110r', 3, 'p'),
(143, 39, 389, '109v', '110r', 3, 'p'),
(144, 53, 535, '10r', '79v', 6, 'd'),
(145, 88, 535, '89r', '121v', 6, '');

-- --------------------------------------------------------

--
-- Structure de la table `alfa_work`
--

CREATE TABLE `alfa_work` (
  `id` int(11) NOT NULL,
  `work_type_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  `place` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tpq` int(11) DEFAULT NULL,
  `taq` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_work`
--

INSERT INTO `alfa_work` (`id`, `work_type_id`, `author_id`, `title`, `place`, `tpq`, `taq`) VALUES
(1, 4, 31, 'Notes on eclipse canons', NULL, NULL, NULL),
(2, 2, 30, 'Canons for the Tabulae Aureae', NULL, NULL, NULL),
(3, 1, 30, 'Tabulae Aureae', NULL, NULL, NULL),
(4, 2, 29, 'Notae de tabulis', NULL, NULL, NULL),
(5, 2, 28, 'Syzygies', NULL, NULL, NULL),
(6, 1, 28, 'Syzygies', NULL, NULL, NULL),
(7, 2, NULL, 'Tables of the Queen Isabella', NULL, NULL, NULL),
(8, 1, NULL, 'Tables of the Queen Isabella', NULL, NULL, NULL),
(9, 2, 27, 'Almanach', NULL, NULL, NULL),
(10, 1, 27, 'Almanach', NULL, NULL, NULL),
(11, 2, 27, 'Tables', NULL, NULL, NULL),
(12, 1, 27, 'Tables', NULL, NULL, NULL),
(13, 2, NULL, 'Tabule verificate for Salamanca', NULL, NULL, NULL),
(14, 1, NULL, 'Tabule verificate for Salamanca', NULL, NULL, NULL),
(15, 2, 26, 'Tabule directionum', NULL, NULL, NULL),
(16, 1, 26, 'Tabule directionum', NULL, NULL, NULL),
(17, 2, 25, 'eclipsyum', NULL, NULL, NULL),
(18, 1, 25, 'eclupsyum', NULL, NULL, NULL),
(19, 3, 24, 'On the Albion', NULL, NULL, NULL),
(20, 2, 24, 'Canons', NULL, NULL, NULL),
(21, 1, 24, 'Tables', NULL, NULL, NULL),
(22, 2, NULL, 'Tabulae Resolutae', NULL, NULL, NULL),
(23, 1, NULL, 'Tabulae Resolutae', NULL, NULL, NULL),
(24, 4, 23, 'Flores Almegesti', NULL, NULL, NULL),
(25, 2, 23, 'Eclipsium', NULL, NULL, NULL),
(26, 2, 23, 'Auxiliary tables', NULL, NULL, NULL),
(27, 1, 23, 'Auxiliary tables', NULL, NULL, NULL),
(28, 2, 23, 'Tables for the planets', NULL, NULL, NULL),
(29, 1, 23, 'Tables for the planets', NULL, NULL, NULL),
(30, 2, 22, 'Canons', NULL, NULL, NULL),
(31, 1, 22, 'Tables', NULL, NULL, NULL),
(32, 3, 21, 'Astrarium', NULL, NULL, NULL),
(33, 4, 21, 'Opus planetarum', NULL, NULL, NULL),
(34, 4, 20, 'Commentary on theorica planetarum', NULL, NULL, NULL),
(35, 4, 19, 'Eclipse treatise', NULL, NULL, NULL),
(36, 2, 18, 'Canons', NULL, NULL, NULL),
(38, 3, 13, 'Albion', NULL, NULL, NULL),
(39, 2, 12, 'Canons to the Oxford tables for 1348', NULL, NULL, NULL),
(40, 1, 12, 'Oxford tables for 1348', NULL, NULL, NULL),
(41, 2, 11, 'Kalendarium', NULL, NULL, NULL),
(42, 1, 11, 'Kalendarium', NULL, NULL, NULL),
(43, 2, 10, 'Kalendarium', NULL, NULL, NULL),
(44, 1, 10, 'Kalendarium', NULL, NULL, NULL),
(45, 2, 9, 'Canons to the Alfonsine tables', NULL, NULL, NULL),
(46, 1, 8, 'motum solis et lune in una hora', NULL, NULL, NULL),
(47, 2, 8, 'investigatio eclipsis 1337', NULL, NULL, NULL),
(48, 2, 7, 'Tabula ad sciendum motus solis et lune', NULL, NULL, NULL),
(49, 1, 7, 'Tabula ad sciendum motus solis et lune', NULL, NULL, NULL),
(50, 1, 6, 'Almanach, Cum animadvertum', NULL, 1336, 1336),
(51, 2, 6, 'Almanach Cum animadvertum', NULL, 1336, 1336),
(52, 5, 6, 'Expositiones canonum primi mobilis', NULL, 1336, 1336),
(53, 2, 6, 'Tempus est mensura motus', NULL, 1327, 1327),
(54, 4, 5, 'theorica planetarum', NULL, NULL, NULL),
(55, 3, 5, 'Directorium', NULL, NULL, NULL),
(56, 3, 5, 'Saphea', NULL, NULL, NULL),
(57, 3, 5, 'Second equatorie', NULL, NULL, NULL),
(58, 3, 5, 'First equatorie', NULL, NULL, NULL),
(59, 2, 5, 'Almanach', NULL, NULL, NULL),
(60, 1, 5, 'Almanach', NULL, NULL, NULL),
(61, 2, 5, 'Quia ad inveniendum', NULL, NULL, NULL),
(62, 2, 5, 'Tabule Magne', NULL, NULL, NULL),
(63, 1, 5, 'Tabule magne', NULL, NULL, NULL),
(64, 2, 5, 'Canons of 1322', NULL, 1322, 1322),
(65, 1, 5, 'Tables for 1321', NULL, 1320, 1321),
(66, 5, 5, 'Algorismus minutiis', NULL, NULL, NULL),
(67, 2, 4, 'Tabule principales', NULL, NULL, NULL),
(68, 1, 4, 'Tabule principales', NULL, NULL, NULL),
(69, 5, 4, 'Sinus et kardaga', NULL, NULL, NULL),
(70, 4, 4, 'Exposition intentionis', NULL, NULL, NULL),
(71, 2, 4, 'Tabula tabularum', NULL, NULL, NULL),
(72, 1, 4, 'Tabula tabularum', NULL, NULL, NULL),
(73, 2, 4, 'Kalendarium', NULL, NULL, NULL),
(74, 1, 4, 'Kalendarium', NULL, NULL, NULL),
(75, 2, 4, 'Tabule permanentes', NULL, NULL, NULL),
(76, 1, 4, 'Tabule permanentes', NULL, NULL, NULL),
(77, 2, 4, 'Patefit', NULL, NULL, NULL),
(78, 1, 4, 'Patefit', NULL, NULL, NULL),
(79, 2, 3, 'Regulae astronomice', NULL, NULL, NULL),
(80, 1, 2, 'Star list', NULL, NULL, NULL),
(81, 2, 2, 'Tabula motus diversis solis', NULL, NULL, NULL),
(82, 1, 2, 'Tabula motus diversis solis', NULL, NULL, NULL),
(83, 3, 2, 'Instrument', NULL, NULL, NULL),
(84, 2, 2, 'Tables', NULL, NULL, NULL),
(85, 1, 2, 'tables', NULL, NULL, NULL),
(86, 2, 1, 'Calendar', NULL, NULL, NULL),
(87, 1, 1, 'Calendar', NULL, NULL, NULL),
(88, 1, NULL, 'Parisian Alfonsine tables', NULL, NULL, NULL),
(89, 3, 32, 'Speculum planetarum', NULL, NULL, NULL),
(90, 4, 26, 'Epitome of the alamgest', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `alfa_work_type`
--

CREATE TABLE `alfa_work_type` (
  `id` int(11) NOT NULL,
  `work_type` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `alfa_work_type`
--

INSERT INTO `alfa_work_type` (`id`, `work_type`) VALUES
(1, 'tables'),
(2, 'canons'),
(3, 'instrument'),
(4, 'theoretical'),
(5, 'mathematical'),
(6, 'observational'),
(7, 'miscellaneous texts');

-- --------------------------------------------------------

--
-- Structure de la table `astronomical_object`
--

CREATE TABLE `astronomical_object` (
  `id` int(11) NOT NULL,
  `object_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `astronomical_object`
--

INSERT INTO `astronomical_object` (`id`, `object_name`) VALUES
(10, 'eclipse'),
(9, 'eighth sphere'),
(6, 'jupiter'),
(5, 'mars'),
(2, 'mercury'),
(4, 'moon'),
(7, 'saturn'),
(8, 'spherical astronomical'),
(1, 'sun'),
(11, 'trigonometrical'),
(3, 'venus');

-- --------------------------------------------------------

--
-- Structure de la table `definition`
--

CREATE TABLE `definition` (
  `id` int(11) NOT NULL,
  `object_database_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_user_interface_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `long_definition` longtext COLLATE utf8_unicode_ci,
  `short_definition` longtext COLLATE utf8_unicode_ci,
  `object_entity_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `database_admin_definition` longtext COLLATE utf8_unicode_ci,
  `user_interface_color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `definition`
--

INSERT INTO `definition` (`id`, `object_database_name`, `object_user_interface_name`, `long_definition`, `short_definition`, `object_entity_name`, `database_admin_definition`, `user_interface_color`) VALUES
(1, 'astronomical_object', 'astronomical object', 'An astronomical object is a celestial entity associated with some natural phenomena. In DISHAS, astronomical objects are used as a way to classify astronomical tables in broader categories. ', 'An astronomical object is a celestial entity associated with some natural phenomena. In DISHAS, astronomical objects are used as a way to classify astronomical tables in broader categories. ', 'astronomicalObject', NULL, NULL),
(2, 'astronomical_table', 'astronomical table', 'An astronomical table is a numerical table used by historical actors in astronomical computation. It is an important object of study in astral sciences. ', 'An astronomical table is a numerical table used by historical actors in astronomical computation. It is an important object of study in astral sciences. ', 'astronomicalTable', NULL, NULL),
(3, 'edited_text', 'edited text', 'An edited text is a production credited to a contemporary intellectual author or historian. It documents one specific table, text, or diagram. This document can be from a previous edited source or a new edition created using the web interface of DISHAS. An edited text can have various edition-types including an edition of a specific original text, a recomputation, a new production based on multiple sources, etc. <br/> An essential goal of the database is  to make editions of astronomical tables that are accessible and comparable. In achieving this, edited texts, as classified in various edition-types, help (i) identify mathematical and astronomical parameters, (ii) examine and develop critical and computational tools for creating new editions, and  (iii) provide a centralised resource for studying astronomical tables from different traditions. <br/>We collect general information about the edition, e.g., the title, the date of creation, etc., to record the metadata associated with it. This, along with the other information associated with the edited text, helps us unify and create standards for comparison in our database. We collect secondary source information for  bibliographic accuracy and catalogue reference.<br/>Three types of edition are defined in DISHAS<ul><li>Type-A Edition Numerically consistent table</li><li>Type-B Edition Numerical Reproduced Table</li><li>Type-C Edition Numerically Recomputed Table(s)</li></ul>', 'An edited text is a production credited to a contemporary intellectual author or historian. It documents one specific table, text, or diagram. This document can be from a previous edited source or a new edition created using the web interface of DISHAS. An edited text can have various edition-types including an edition of a specific original text, a recomputation, a new production based on multiple sources, etc. ', 'editedText', NULL, '#5286ec'),
(4, 'historian', 'intellectual author', 'An intellectual author is a contemporary historian who has produced an edition that is stored in the database. This historian could be different to the person entering the data into the database. For example, in certain instances, a scholar (user) may be entering the data from an edition created by another historian (from, perhaps, an unpublished collection). The intellectual author in this case is the creator-historian and not the scholar (user).<br/>Collecting information about intellectual authors validates the factual reliability of the database. This effort helps preserve the work of previous generation of historians as a way to foster and encourage further studies.<br/>The information collected about intellectual authors helps identify modern scholarship of the edition entered in the database.', 'An intellectual author is a contemporary historian who has produced an edition that is stored in the database. This historian could be different to the person entering the data into the database. For example, in certain instances, a scholar (user) may be entering the data from an edition created by another historian (from, perhaps, an unpublished collection). The intellectual author in this case is the creator-historian and not the scholar (user).', 'historian', NULL, '#d85040'),
(5, 'historical_actor', 'historical actor', 'Historical actors are individuals or collectives related to the items or works stored in the database. They include author, copyist, owners, dedicatee, etc. associated to the materials of the original item. <br/>Within the framework and scope of our database, we collect certain information related to the historical actors, namely, their name, their flourit, and their location, to enable us to create a standardised lists of actors. With the additional (and highly recommended) Virtual International Authority File (VIAF) number recorded, the database makes it possible to align names added at a latter stage with a larger, normalised, authority register of person names.  <br/>We collect information about the actors in order to build prosopographic data.', 'Historical actors are individuals or collectives related to the items or works stored in the database. They include author, copyist, owners, dedicatee, etc. associated to the materials of the original item.', 'historicalActor', NULL, '#F2BD42'),
(6, 'journal', 'journal', 'In academic publishing, a scientific journal is a periodical publication intended to further the progress of science, usually by reporting new research.', 'In academic publishing, a scientific journal is a periodical publication intended to further the progress of science, usually by reporting new research.', 'journal', NULL, '#58a55c'),
(7, 'language', 'language', NULL, NULL, 'language', NULL, NULL),
(8, 'library', 'library', 'A library is a present-day or last-known location where the primary source is situated.<br/>The knowledge of the physical location of the library where the primary source is held is important for establishing the reliability of the database as a research resource.<br/>Facilitated with assistance tools like ISNI, entering the library information helps the database register and identify the correct library. For other libraries (not on ISNI records), a new entry helps create a record of its existence in the database for subsequent use.', 'A library is a present-day or last-known location where the primary source is situated.', 'library', NULL, '	#52d3ec'),
(9, 'mathematical_parameter', 'mathematical parameter', 'There are two kind of mathematical parameters.  A table is said to be ‘displaced’ when a constant value is added to its arguments or/and its entry. The value of these added constant are the first kind of mathematical parameters. A table can also be ‘shifted’. This happens when the ordering of the arguments and/or entry is modified according to a permutation cycle. The number of shifts in the permutation cycle is the second type of mathematical parameters.<br/>The main purpose of identifying mathematical parameters is to compare  displaced and/or shifted tables with those which are not displaced or shifted. We note that both kinds of tables may rely on the same set of  astronomical parameters. Mathematical parameters can be shared by different sources. Additionally, shift and displacement are peculiar kinds of mathematical techniques linked to the manipulation of table sets and as such are worthy of investigation in their own right.', 'There are two kind of mathematical parameters.  A table is said to be ‘displaced’ when a constant value is added to its arguments or/and its entry. The value of these added constant are the first kind of mathematical parameters. A table can also be ‘shifted’. This happens when the ordering of the arguments and/or entry is modified according to a permutation cycle. The number of shifts in the permutation cycle is the second type of mathematical parameters.', 'mathematicalParameter', NULL, '#6b52ec'),
(10, 'number_unit', 'number unit', 'Historical actors have used various units to express time and space relations in astral sciences. Some of these units are directly related to celestial objects, others to measuring instruments. Some units are mathematical and abstract. Often, these units are distinct from the degree and second in common use today. Thus, modern units are not a suitable tool to record historical astronomical tables in most cases. The common units used by historical actors in astral science are implemented in the database for historical accuracy. This will also allow for mathematical analysis in DISHAS.\nThis level of complexity combines with a similar one concerning the types of numbers. ', 'Historical actors have used various units to express time and space relations in astral sciences. Some of these units are directly related to celestial objects, others to measuring instruments. Some units are mathematical and abstract. Often, these units are distinct from the degree and second in common use today. Thus, modern units are not a suitable tool to record historical astronomical tables in most cases. The common units used by historical actors in astral science are implemented in the database for historical accuracy. This will also allow for mathematical analysis in DISHAS.\nThis level of complexity combines with a similar one concerning the types of numbers. ', 'numberUnit', NULL, NULL),
(11, 'original_text', 'original item', 'An original item is a table, text, or diagram as it appears in a given primary source. In instances where the actual state of the item is the result of several distinctive production acts, e.g., an extra column added to a table by a later actor, each differing state is a specific item. A scholar (user) may input these instances as separate original items. In the current stage of development of DISHAS, only tables are considered; texts and diagram will be included at a later stage. <br/>The original item, as it appears in the primary source, is the fundamental unit of the database. Hence, elementary units are individual tables, diagrams, or texts that are actually witnessed in specific primary sources.  This level of granularity is central to make DISHAS an efficient tool for mathematical analysis and critical editing. <br/>We collect material information about the original item to allow source criticism.  These include the date and the place of production, the title of the work, the manuscript shelfmark, etc. ', 'An original item is a table, text, or diagram as it appears in a given primary source. In instances where the actual state of the item is the result of several distinctive production acts, e.g., an extra column added to a table by a later actor, each differing state is a specific item. A scholar (user) may input these instances as separate original items. In the current stage of development of DISHAS, only tables are considered; texts and diagram will be included at a later stage. ', 'originalText', NULL, '#ecb852'),
(12, 'parameter_feature', 'parameter feature', NULL, NULL, 'parameterFeature', NULL, NULL),
(13, 'parameter_format', 'parameter format', NULL, NULL, 'parameterFormat', NULL, NULL),
(14, 'parameter_group', 'parameter group', NULL, NULL, 'parameterGroup', NULL, NULL),
(15, 'parameter_set', 'astronomical parameter set', 'A parameter set is a set of  astronomical quantities that describes tables at the level of astronomical theories. A parameter set can be shared by several tables across different traditions. Broadly speaking, there are two kinds of parameters: explicit parameters that are directly read off the table, e.g., the maximum value(s); and implicit parameters that need to be retrieved from the table content computationally, e.g,  solar eccentricity. In some cases, these explicit or implicit  parameters are only significant in groups; whereas in other instances, parameters can be independently significant.<br/>Parameters are a central tool in (i) delineating and connecting  astronomical traditions and (ii) analysing the mathematical and astronomical content of the tables. <br/>A parameter-set is only linked to a intellectual author through an edited text because a single parameter-set can be shared by many different sources.', 'A parameter set is a set of  astronomical quantities that describes tables at the level of astronomical theories. A parameter set can be shared by several tables across different traditions. Broadly speaking, there are two kinds of parameters: explicit parameters that are directly read off the table, e.g., the maximum value(s); and implicit parameters that need to be retrieved from the table content computationally, e.g,  solar eccentricity. In some cases, these explicit or implicit  parameters are only significant in groups; whereas in other instances, parameters can be independently significant.', 'parameterSet', NULL, '#d3ec52	'),
(16, 'parameter_type', 'parameter type', NULL, NULL, 'parameterType', NULL, ''),
(17, 'parameter_unit', 'parameter unit', 'Parameters are meant to compare sources from different traditions which possibily used different type of units. In order to allow comparison accross these traditions, it is necessary to standardise units to express parameters. Parameters are meant to compare sources from different traditions which possibily used different type of units. In order to allow comparison accross these traditions, it is necessary to standardise units to express parameters. ', 'Parameters are meant to compare sources from different traditions which possibily used different type of units. In order to allow comparison accross these traditions, it is necessary to standardise units to express parameters. ', 'parameterUnit', NULL, NULL),
(18, 'parameter_value', 'parameter value', NULL, NULL, 'parameterValue', NULL, NULL),
(19, 'place', 'place', 'The definition of a place depends on the object to which it relates. For an original item, it refers to the place of production. For an author, it refers to his main place of activity where his work can be (approximately or accurately) geo-localised. For a work, the place refers to the original place of conception. <br/>This information is important to understand the diffusion and circulation of items in various works and traditions.<br/>Collecting information about the place, including, in some instances, its latitude and longitude, helps us represent the locations on a map. This visual representation can become an important tool for textual analysis and circulation studies.', 'The definition of a place depends on the object to which it relates. For an original item, it refers to the place of production. For an author, it refers to his main place of activity where his work can be (approximately or accurately) geo-localised. For a work, the place refers to the original place of conception. ', 'place', NULL, '#e3ff00'),
(20, 'primary_source', 'primary source', 'A primary source is  a manuscript or an early printed edition in which the original item is found, usually as part of a work. <br/>It is essential for the reliability of  DISHAS that it provide accurate identification of its primary sources. Additionally, having accurate  information about primary sources in the database can prove to be an important tool to study the history of modern  collections in astral sciences.<br/>Information about the primary source helps us identify the physical institution or library that holds the primary source. This identification allows scholars to locate the original source for further investigation.', 'A primary source is  a manuscript or an early printed edition in which the original item is found. ', 'primarySource', NULL, '#52ec6b'),
(21, 'script', 'script', NULL, NULL, 'script', NULL, NULL),
(22, 'secondary_source', 'secondary source', 'A secondary source is a book, an article, a chapter, etc. that indicate a contemporary edition, or a survey of the table. Any edited document in the database can only be linked to a single secondary source for breivity. <br/>Information about secondary sources provides a reliable and tractable resource for further studies. This information will help current and future scholars use the database for their research.<br/>Secondary sources provide reference material for further studies and as such, the information collected about it is aimed to make the database competent in facilitating this.', 'A secondary source is a book, an article, a chapter, etc. that indicate a contemporary edition, or a survey of the table. Any edited document in the database can only be linked to a single secondary source for breivity. ', 'secondarySource', NULL, '#a55c58'),
(23, 'table_content', 'table content', 'The table content is the collection of numerical values in the edited text. The relation between these numerical values and those  attested in the original item(s) associated with the edited text depends on the edition-type selected. We understand edited text as the type of work done by the intellectual author responsible of its creation. <br/>Comparing numerical contents of various tables and building new computational and analytic tools is central to the aim of DISHAS. In order to achieve this,  a minimum level of standardisation in the layout of tables and the format of recorded numbers is necessary. However, it is equally important to allow a certain level of flexibility to reflect the practices of historical actors faithfully. Hence, there are several possibilities about table layout, table types, and types of numbers. The choices of an intellectual author are carefully documented to ensure a meaningful comparison between the table contents of various edited texts.<br/>We collect information about the table contents in order to structure the contents for displaying purposes. The astronomical parameters  are linked to the edited texts (and not to the original text) because a given parameter set is always attributed to a table by an intellectual author after analyses.', 'The table content is the collection of numerical values in the edited text. The relation between these numerical values and those  attested in the original item(s) associated with the edited text depends on the edition-type selected. We understand edited text as the type of work done by the intellectual author responsible of its creation.', 'tableContent', NULL, '#8358a5'),
(24, 'table_type', 'table type', 'Table are classified in different types. These different types correspond to the specific steps in astronomical computations as organised by historical actors.  In modern terms, these types correspond to distinct mathematical functions which express a portion of an underlying astronomical theory. ', 'Table are classified in different types. These different types correspond to the specific steps in astronomical computations as organised by historical actors.  In modern terms, these types correspond to distinct mathematical functions which express a portion of an underlying astronomical theory. ', 'tableType', NULL, NULL),
(25, 'type_of_number', 'type of number', 'Over the course of time and in different domains of astral sciences, historical actors have used  different types of numbers to make their computations and also shape their tables. Some are fully or partially sexagesimal, others rely on floating numbers, etc. These different types of numbers are often very distinct from the decimal systems used in modern numerical programming. As a consequence, modern decimals are not always a suitable tool to record historical astronomical tables. The common types of numbers used by historical actors in astral sciences are implemented in the database for historical accuracy and computational analysis.This level of complexity combines with a similar one concerning units. <br/>DISHAS uses six types of number: <ul><li>Sexagesimal</li><li>Floating sexagesimal: floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next.</li><li>Historical: revolution and sign.</li><li>Integer and sexagesimal: integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system.</li><li>Historical decimal:  in Chinese sources fractionnal part can be expressed with \'fen\' and \'miao\'. There is 100 \'fen\' in the unit and 100 \'miao\' in a \'fen\'. Thus formaly Historical decimal are three positions numbers with a factor 100 between the positions.</li><li>Temporal: duration of time converted into days + sexagesimal. Ex : 7d6;30,05.</li></ul>', 'Over the course of time and in different domains of astral sciences, historical actors have used  different types of numbers to make their computations and also shape their tables. Some are fully or partially sexagesimal, others rely on floating numbers, etc. These different types of numbers are often very distinct from the decimal systems used in modern numerical programming. As a consequence, modern decimals are not always a suitable tool to record historical astronomical tables. The common types of numbers used by historical actors in astral sciences are implemented in the database for historical accuracy and computational analysis.\nThis level of complexity combines with a similar one concerning units. ', 'typeOfNumber', NULL, NULL),
(26, 'work', 'work', 'An astronomical work is a distinct intellectual creation consisting of  tables, texts, or diagrams. It is often identified by a title or incipit and attributed to a given historical actor. Its content and organisation may vary depending on the particular primary source.<br/>We can analyse the different attestations of a given work as original items rather than individual works are the fundamental unit of DISHAS. For instance, one can identify a core set of tables around which various satellites tables, texts, or diagrams may be presented depending on the primary source. With this design, the geographical and chronological evolution of these dynamical sets of original items can be analysed and studied. This ability is essential when it come to critical editing a work. Additionally, the choice of an original item as the fundamental unit also allows to analyse the intellectual composition of a given manuscript. This is an important step in understanding how manuscript shape intellectual traditions.<br/>A work needs to be identified by a current title or incipit. When possible supplementary information like place, dates, authors or translator is very useful for studying the circulation of the work.', 'An astronomical work is a distinct intellectual creation consisting of  tables, texts, or diagrams. It is often identified by a title or incipit and attributed to a given historical actor. Its content and organisation may vary depending on the particular primary source.', 'work', NULL, '#ec5286'),
(27, 'user_interface_text', 'user interface text', NULL, NULL, 'userInterfaceText', 'Texts managed by the super admin that are dispay in the website. ', NULL),
(28, NULL, 'type of edition', 'Three types of edition are defined in DISHAS<ul><li><h4>Type-A Edition Numerically consistent table</h4>The first stage involves the rendition of the individual table (taken from individual MSS of the same work) from its diplomatic transcription onto a more numerical consistent form, i.e., a numerical format free of any data integrity errors. The following is a list; albeit not an exhaustive one, of the different types of strategies employed in creating a numerically consistent table.<ol><li>The first stage involves the rendition of the individual table (taken from individual MSS of the same work) from its diplomatic transcription onto a more numerical consistent form, i.e., a numerical format free of any data integrity errors. The following is a list; albeit not an exhaustive one, of the different types of strategies employed in creating a numerically consistent table.</li><li>Numerical parsing on a local and global scale to keep regularity, e.g., correcting a number to maintain simple mathematical regularity. It should be noted here that this inspection is merely observational and not computational.</li><li>Using pattern recognition and visual inspection to correct numbers for constancy, uniformity, and accuracy (in reproduction).</li><li>Noting all numerical changes, e.g., instances where ambiguous, dubious, or inconsistent numbers are altered, alternatives or originals are duly noted as comments.</li></ol>Note: Any ancillary material (paratext, scribal error marks and corrections, change in handwriting, etc.) is NOT included in this table: these remain features that are to be noted in the TEI version of the table.</li><li><h4>Type-B Edition Numerical Reproduced Table</h4>The second stage involves employing the different individual tables (belonging to the same work, but taken from different MSS) and forming an EDITION table. This process can be defined based on a few different strategies:<ol><li>Any eclectic, stemmatic, or copy-text method (in general, any philological method) of comparing the different \'witness\' tables (from Part A) and establishing a critical \'base\' numerical table, with all variants duly noted in comments. These comments effectively make the apparatus criticus.</li><li>Any mathematical method (from Part C) of establishing a critical \'base\' numerical table and then employing tables (from Part A) to note the variants.</li><li>Any combination of the two strategies above.</li><li>Additionally, any previously \'critically\' edited table (from a set of extant or non-existing manuscripts) belonging to the same table-type and work (as set in Part A) can also be stored in Part B here. In such cases, it is important to note any relation, if applicable, of this previous work to the different tables in Part A. Failing this, a common reference to the work and table-type becomes minimally necessary.</li></ol>Several different \'critical editions\' (based on different editorial strategies) of the same table may be stored in this stage; all of which, are identified with the correct relational structure to the tables in parts A and C.</li><li><h4>Type-C Edition Numerically Recomputed Table(s)</h4>The third stage involves the recomputed tables---corresponding to each individual table in Part A---that are stored relationally in the SQL database. These tables are generated based on the following possible strategies:<ol><li>A modern mathematical recomputation of the table based on (a) choice of appropriate parameter, (b) mathematical equations governing the underlying function tabulated, (c) selection of a suitable generative or iterative algorithm.</li><li>A historically congruous mathematical algorithm based on editorial decisions. These tables are generated by a modern scholar based on his or her own understanding of the mathematical apparatus presumably intended to have been used by the original author in his time. These tables form a collection of our modern efforts to reconstruct numerical tables within its historical context.</li><li>Any other derivatives or combinations of mathematical procedures, algorithms, iterative techniques, rounding schemes, etc. used to generate the numerical entries of the table form separate mathematical tables that are also stored in this part.</li></li></ul>', NULL, 'typeOfEdition', NULL, NULL),
(29, NULL, 'shift and displacement', '\n<b style=\"font-weight: 150%\">Base example</b>\n<div class=\"row\">\n	<div class=\"col-md-4\">\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>10</p>\n					</td>\n					<td>\n						<p>7</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>20</p>\n					</td>\n					<td>\n						<p>8</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>30</p>\n					</td>\n					<td>\n						<p>9</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>40</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n</div>\n<b style=\"font-weight: 150%\">Shift</b><br/>\n<i>Argument-shift and entry-shift are always integer numbers</i>\n<div class=\"row\">\n	<div class=\"col-md-4\">\n\n		<p><strong>Argument-Shift = 2</strong><br /><strong>Entry-Shift = 0</strong></p>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>30</p>\n					</td>\n					<td>\n						<p>7</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>40</p>\n					</td>\n					<td>\n						<p>8</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>10</p>\n					</td>\n					<td>\n						<p>9</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>20</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n	<div class=\"col-md-4\">\n		<p><strong>Argument-Shift = 0<br /></strong><strong>Entry-Shift = 2</strong></p>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>10</p>\n					</td>\n					<td>\n						<p>9</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>20</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>30</p>\n					</td>\n					<td>\n						<p>7</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>40</p>\n					</td>\n					<td>\n						<p>8</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n	<div class=\"col-md-4\">\n		<p><strong>Argument-Shift = 2<br /></strong><strong>Entry-Shift = 3</strong></p>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>30</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>40</p>\n					</td>\n					<td>\n						<p>7</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>10</p>\n					</td>\n					<td>\n						<p>8</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>20</p>\n					</td>\n					<td>\n						<p>9</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n</div>\n<b style=\"font-weight: 150%\">Displacement</b><br/>\n<i>Argument-displacement and entry-displacement are the same type of numbers as the value of the table. It might vary from argument and entry.</i>\n<div class=\"row\">\n	<div class=\"col-md-4\">\n		<strong>Argument-Displacement = 2<br /></strong><strong>Entry-Displacement = 0</strong>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>12</p>\n					</td>\n					<td>\n						<p>7</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>22</p>\n					</td>\n					<td>\n						<p>8</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>32</p>\n					</td>\n					<td>\n						<p>9</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>42</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n	<div class=\"col-md-4\">\n		<strong>Argument-Displacement = 0<br /></strong><strong>Entry-Displacement = 3</strong>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>10</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>20</p>\n					</td>\n					<td>\n						<p>11</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>30</p>\n					</td>\n					<td>\n						<p>12</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>40</p>\n					</td>\n					<td>\n						<p>13</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n	<div class=\"col-md-4\">\n		<strong>Argument-Displacement = 2<br /></strong><strong>Entry-Shift = 3</strong>\n		<table align=\"center\" class=\"table table-bordered\">\n			<tbody>\n				<tr>\n					<td class=\'col-md-2\'>\n						<p><strong>Argument</strong></p>\n					</td>\n					<td class=\'col-md-2\'>\n						<p><strong>Entry</strong></p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>12</p>\n					</td>\n					<td>\n						<p>10</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>22</p>\n					</td>\n					<td>\n						<p>11</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>32</p>\n					</td>\n					<td>\n						<p>12</p>\n					</td>\n				</tr>\n				<tr>\n					<td>\n						<p>42</p>\n					</td>\n					<td>\n						<p>13</p>\n					</td>\n				</tr>\n			</tbody>\n		</table>\n	</div>\n</div>', NULL, 'shiftDisplacement', NULL, NULL),
(30, 'pdf_file', 'PDF File', '', '', 'PDFFile', NULL, NULL),
(31, 'image_file', 'Image File', '', '', 'ImageFile', NULL, NULL),
(32, 'formula_definition', 'formula definition', '', '', 'FormulaDefinition', NULL, NULL),
(33, 'xml_file', 'XML File', '', '', 'XMLFile', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `edited_text`
--

CREATE TABLE `edited_text` (
  `id` int(11) NOT NULL,
  `secondary_source_id` int(11) DEFAULT NULL,
  `historian_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `edited_text_title` longtext COLLATE utf8_unicode_ci,
  `date` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `online_resource` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `public` tinyint(1) DEFAULT NULL,
  `page_range` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `small_edited_text_title` longtext COLLATE utf8_unicode_ci,
  `table_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `edited_text`
--

INSERT INTO `edited_text` (`id`, `secondary_source_id`, `historian_id`, `created_by_id`, `updated_by_id`, `edited_text_title`, `date`, `type`, `online_resource`, `comment`, `public`, `page_range`, `created`, `updated`, `small_edited_text_title`, `table_type_id`) VALUES
(19, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:38:33', '2018-10-08 11:38:33', 'I love tests', 4),
(20, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:39:57', '2018-10-08 11:39:57', 'I love tests', 4),
(21, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:42:05', '2018-10-08 11:42:05', 'I love tests', 4),
(22, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:42:15', '2018-10-08 11:42:15', 'I love tests', 4),
(23, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:43:52', '2018-10-08 11:43:52', 'I love tests', 4),
(24, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:44:41', '2018-10-08 11:44:41', 'I love tests', 4),
(25, 1, NULL, 2, 2, 'I love tests', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:44:59', '2018-10-08 11:44:59', 'I love tests', 4),
(26, 1, NULL, 2, 2, 'fdgdfg', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:47:38', '2018-10-08 11:47:38', 'fdgdfg', 4),
(27, 1, NULL, 2, 2, 'zefzef', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:48:46', '2018-10-08 11:48:46', 'zefzef', 4),
(28, 1, NULL, 2, 2, 'taezrt', 2018, 'b', NULL, NULL, 0, NULL, '2018-10-08 11:50:26', '2018-10-08 14:44:14', 'taezrt', 4);

-- --------------------------------------------------------

--
-- Structure de la table `edited_text_edited_text`
--

CREATE TABLE `edited_text_edited_text` (
  `edited_text_source` int(11) NOT NULL,
  `edited_text_target` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `edited_text_original_text`
--

CREATE TABLE `edited_text_original_text` (
  `edited_text_id` int(11) NOT NULL,
  `original_text_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formula_definition`
--

CREATE TABLE `formula_definition` (
  `id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `explanation` longtext COLLATE utf8_unicode_ci,
  `modern_definition` longtext COLLATE utf8_unicode_ci,
  `formula_JSON` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `bibliography` longtext COLLATE utf8_unicode_ci,
  `parameter_explanation` longtext COLLATE utf8_unicode_ci,
  `estimator_definition` longtext COLLATE utf8_unicode_ci,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `table_type_id` int(11) DEFAULT NULL,
  `author` varchar(1000) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `formula_definition`
--

INSERT INTO `formula_definition` (`id`, `image_id`, `name`, `explanation`, `modern_definition`, `formula_JSON`, `bibliography`, `parameter_explanation`, `estimator_definition`, `created_by_id`, `updated_by_id`, `created`, `updated`, `table_type_id`, `author`) VALUES
(1, 1, 'coucou', '<p>fghrt</p>', '<p>rthrtjytj</p>', '\"<p>gkjhjkjhk<\\/p>\"', '<p>azarzettfhfghjb</p>', '<p>zegfjhnlopiop</p>', '<p>rgfdghvchfg</p>', 11, 11, '2018-06-07 11:17:46', '2018-06-07 11:17:46', NULL, ''),
(2, 2, 'zefzef', '<p>zefze</p>', '<p>zef</p>', '\"<p>zef<\\/p>\"', '<p>ezf</p>', '<p>zef</p>', '<p>zef</p>', 11, 11, '2018-06-07 11:22:41', '2018-06-07 11:22:41', NULL, ''),
(3, 3, 'zefzef', '<p>zefze</p>', '<p>zef</p>', '\"<p>zef<\\/p>\"', '<p>ezf</p>', '<p>zef</p>', '<p>zef</p>', 11, 11, '2018-06-07 11:24:29', '2018-06-07 11:24:29', NULL, ''),
(4, 4, 'zefzef', '<p>zefze</p>', '<p>zef</p>', '\"<p>zef<\\/p>\"', '<p>ezf</p>', '<p>zef</p>', '<p>zef</p>', 11, 11, '2018-06-07 11:25:10', '2018-06-07 11:25:10', NULL, ''),
(5, 5, 'zefzef', '<p>zefze</p>', '<p>zef</p>', '\"<p>zef<\\/p>\"', '<p>ezf</p>', '<p>zef</p>', '<p>zef</p>', 11, 11, '2018-06-07 11:29:02', '2018-06-07 11:29:02', NULL, ''),
(6, 7, 'Solar Declination', '<p>The solar declination (usually called &delta;) is the length of the arc from a given point ☼ on the ecliptic dropped perpendicularly onto the celestial equator. It is a function of &lambda;, the magnitude of the arc from the vernal equinox ♈ (the intersection point of the ecliptic and the equator that the sun crosses at the vernal equinox). The solar declination can be measured northward from the equator or southward; in modern times its value is considered to be positive or negative respectively.<br />\r\nThe declination and the right ascension &alpha; together form the equatorial coordinates of any point on the ecliptic (or indeed, any point on the entire celestial sphere). In ancient and medieval times, however, it was usually not thought of in terms of coordinates. Rather, it was the altitude of the point above the equator at the instant that it crosses the meridian.</p>', '<p>The declination depends on the obliquity of the ecliptic &epsilon;. Its precise definition is</p>\r\n\r\n<p><span class=\"math-tex\">\\(\\sin\\delta=\\sin\\lambda\\sin\\varepsilon\\)</span>,</p>\r\n\r\n<p>although occasionally it was approximated by</p>\r\n\r\n<p><span class=\"math-tex\">\\(\\delta=\\varepsilon\\sin\\lambda\\)</span>.</p>', '\"<p>arcsin(sin(x)sin(p0))<\\/p>\"', '<p>Olaf Pedersen, <em>A Survey of the Almagest</em>. Reissue New York: Springer, 2011. pp. 94-96.<br />\r\nGlen Van Brummelen, <em>The Mathematics of the Heavens and the Earth</em>. Princeton, NJ: Princeton University Press, 2009. pp. 1-4, 46-48.</p>', '<p>The obliquity of the ecliptic&nbsp;<span class=\"math-tex\">\\(\\varepsilon\\)</span> usually took on values between <span class=\"math-tex\">\\(23.5°\\)</span>and <span class=\"math-tex\">\\(24°\\)</span>.</p>', '<p>Least squares TODO</p>', 11, 11, '2018-06-07 15:37:17', '2018-06-07 17:23:26', 8, '');

-- --------------------------------------------------------

--
-- Structure de la table `fos_user`
--

CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `fos_user`
--

INSERT INTO `fos_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`) VALUES
(2, 'Galla Topalian', 'galla topalian', 'galla.topalian@obspm.fr', 'galla.topalian@obspm.fr', 1, NULL, '$2y$13$6c9ONDuhTF7lHAgmSyL4EupWwpMDZu5ghk1vpjm./c/3s8wJaYRxa', '2018-10-08 09:50:39', 'iOgjcWYqITM9PJbNfbwe-z5XzdtTcwmKOnUv9fFsK7A', '2017-12-07 14:15:42', 'a:2:{i:0;s:16:\"ROLE_SUPER_ADMIN\";i:1;s:15:\"ROLE_ALFA_ADMIN\";}'),
(3, 'Test do-not-contact', 'test do-not-contact', 'galla@yopmail.com', 'galla@yopmail.com', 1, NULL, '$2y$13$iZPUyukfWlOvKVui6GQv5uL4DyxOnGtYeMMW4BAix98dNiL3lyxpK', '2018-06-26 11:18:55', '3Ih7XCFM-_UluVeKQqC7nJlwtH2kOjcM7VZyq5JmxNc', '2017-03-02 16:17:59', 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(4, 'Anuj Misra', 'anuj misra', 'anuj.misra@gmail.com', 'anuj.misra@gmail.com', 1, NULL, '$2y$13$ZYjcreJhdDI7LZbRqQvHzuBW6BOcR18Ho5msUFbbP3ChOxRO9epaq', '2018-01-03 10:57:34', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(5, 'Matthieu Husson', 'matthieu husson', 'matthieu.husson@obspm.fr', 'matthieu.husson@obspm.fr', 1, NULL, '$2y$13$c0KTdst6J8ixB5bWVLxPEeWfw7WQxIpHz/I5zcuHSgt972aM6K/9u', '2017-12-05 17:49:22', NULL, NULL, 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}'),
(6, 'José Chabás', 'josé chabás', 'jose.chabas@upf.edu', 'jose.chabas@upf.edu', 1, NULL, '$2y$13$JuEdt3DkRiYhqdvHbGNSpuGdLXgb/WC4Z2ZpwiQuUrcMdMV32nJqS', '2017-12-11 16:04:45', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(7, 'Richard Kremer', 'richard kremer', 'Richard.L.Kremer@dartmouth.edu', 'richard.l.kremer@dartmouth.edu', 1, NULL, '$2y$13$D/w6LDbqDzLGSKb9bOr.X.PzjXdAUj1eAHZFp3Z6oGMYHq1.qCDcu', '2017-12-11 16:03:44', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(8, 'Glen van Brummelen', 'glen van brummelen', 'gvb@questu.ca', 'gvb@questu.ca', 1, NULL, '$2y$13$VVA2UDgmt8FRgaEvKb31CeqxprQzjlYvO9YsFq.S/LKG250JMyS9O', '2017-12-11 16:10:10', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(9, 'Clemency Montelle', 'clemency montelle', 'clemency.montelle@canterbury.ac.nz', 'clemency.montelle@canterbury.ac.nz', 1, NULL, '$2y$13$dXKWaP1VfCIM9ggcCZH.G.Iau9i0cu2pLlMzrW.toaLD3A9FvaUmC', '2017-12-11 16:25:29', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(10, 'Benno van Dalen', 'benno van dalen', 'bvdalen@ptolemaeus.badw.de', 'bvdalen@ptolemaeus.badw.de', 1, NULL, '$2y$13$QmECn0U9SExJ/PcoQL6e7.gi6x3W5ZLqbB1JMv8fGzX4w6IQPTU/S', '2017-12-22 15:03:03', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(11, 'Antonin Penon', 'antonin penon', 'antonin.penon@obspm.fr', 'antonin.penon@obspm.fr', 1, NULL, '$2y$13$RneylPMF1DiPVpaRs1/xiuAUXT/sRhuJD4lA7Pos54fLhO8iXbyKi', '2018-09-25 09:46:50', NULL, NULL, 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}'),
(12, 'testuser', 'testuser', 'test@example.com', 'test@example.com', 1, NULL, '$2y$13$61JXe8DngMPwFnNP45EBtO6WltnehFYrxWKF5dC0DKC4rv7HI40iK', '2018-05-30 09:55:09', NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}');

-- --------------------------------------------------------

--
-- Structure de la table `historian`
--

CREATE TABLE `historian` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `historian`
--

INSERT INTO `historian` (`id`, `created_by_id`, `updated_by_id`, `last_name`, `first_name`, `created`, `updated`) VALUES
(3, 2, 2, 'Holmes', 'Sherlock', '2018-10-08 11:23:27', '2018-10-08 11:23:27');

-- --------------------------------------------------------

--
-- Structure de la table `historical_actor`
--

CREATE TABLE `historical_actor` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `actor_name` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `tpq` int(11) DEFAULT NULL,
  `taq` int(11) DEFAULT NULL,
  `viaf_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `image_file`
--

CREATE TABLE `image_file` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `image_file`
--

INSERT INTO `image_file` (`id`, `created_by_id`, `updated_by_id`, `file_name`, `file_user_name`, `file_original_name`, `file_size`, `created`, `updated`, `updated_at`) VALUES
(1, 11, 11, 'DISHAS_antonin-penon_etrdfuhojiitytiuy_2018-06-07.jpg', 'etrdfuhojiitytiuy', 'coffee.jpg', 109899, '2018-06-07 11:17:46', '2018-06-07 11:17:46', '2018-06-07 11:17:46'),
(2, 11, 11, 'DISHAS_antonin-penon_zef_2018-06-07.sqlite', 'zef', 'jupyterhub.sqlite', 28672, '2018-06-07 11:22:41', '2018-06-07 11:22:41', '2018-06-07 11:22:41'),
(3, 11, 11, 'DISHAS_antonin-penon_zef_2018-06-07.sqlite', 'zef', 'jupyterhub.sqlite', 28672, '2018-06-07 11:24:29', '2018-06-07 11:24:29', '2018-06-07 11:24:29'),
(4, 11, 11, 'DISHAS_antonin-penon_zef_2018-06-07.sqlite', 'zef', 'jupyterhub.sqlite', 28672, '2018-06-07 11:25:10', '2018-06-07 11:25:10', '2018-06-07 11:25:10'),
(5, 11, 11, 'DISHAS_antonin-penon_zef_2018-06-07.sqlite', 'zef', 'jupyterhub.sqlite', 28672, '2018-06-07 11:29:02', '2018-06-07 11:29:02', '2018-06-07 11:29:02'),
(7, 11, 11, 'DISHAS_antonin-penon_Solar-declination_2018-06-08.png', 'Solar declination', 'glenscheme.png', 76936, '2018-06-07 15:37:17', '2018-06-08 16:31:49', '2018-06-08 16:31:49'),
(8, 11, 11, 'DISHAS_antonin-penon_Right-ascension_scheme_2018-06-08.png', 'Right ascension_scheme', 'paf.png', 76895, '2018-06-08 17:46:59', '2018-06-08 17:46:59', '2018-06-08 17:46:59');

-- --------------------------------------------------------

--
-- Structure de la table `journal`
--

CREATE TABLE `journal` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `journal_title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `iso_639_2` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iso_639_1` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `language`
--

INSERT INTO `language` (`id`, `iso_639_2`, `iso_639_1`, `language_name`) VALUES
(1, 'aar', 'aa', 'Afar'),
(2, 'abk', 'ab', 'Abkhazian'),
(3, 'ace', '', 'Achinese'),
(4, 'ach', '', 'Acoli'),
(5, 'ada', '', 'Adangme'),
(6, 'ady', '', 'Adyghe Adygei'),
(7, 'afa', '', 'Afro-Asiatic languages'),
(8, 'afh', '', 'Afrihili'),
(9, 'afr', 'af', 'Afrikaans'),
(10, 'ain', '', 'Ainu'),
(11, 'aka', 'ak', 'Akan'),
(12, 'akk', '', 'Akkadian'),
(13, 'alb', 'sq', 'Albanian (B)'),
(14, 'sqi', 'sq', 'Albanian (T)'),
(15, 'ale', '', 'Aleut'),
(16, 'alg', '', 'Algonquian languages'),
(17, 'alt', '', 'Southern Altai'),
(18, 'amh', 'am', 'Amharic'),
(19, 'ang', '', 'English, Old (ca.450-1100)'),
(20, 'anp', '', 'Angika'),
(21, 'apa', '', 'Apache languages'),
(22, 'ara', 'ar', 'Arabic'),
(23, 'arc', '', 'Official Aramaic (700-300 BCE) Imperial Aramaic (700-300 BCE)'),
(24, 'arg', 'an', 'Aragonese'),
(25, 'arm', 'hy', 'Armenian (B)'),
(26, 'hye', 'hy', 'Armenian (T)'),
(27, 'arn', '', 'Mapudungun Mapuche'),
(28, 'arp', '', 'Arapaho'),
(29, 'art', '', 'Artificial languages'),
(30, 'arw', '', 'Arawak'),
(31, 'asm', 'as', 'Assamese'),
(32, 'ast', '', 'Asturian Bable Leonese Asturleonese'),
(33, 'ath', '', 'Athapascan languages'),
(34, 'aus', '', 'Australian languages'),
(35, 'ava', 'av', 'Avaric'),
(36, 'ave', 'ae', 'Avestan'),
(37, 'awa', '', 'Awadhi'),
(38, 'aym', 'ay', 'Aymara'),
(39, 'aze', 'az', 'Azerbaijani'),
(40, 'bad', '', 'Banda languages'),
(41, 'bai', '', 'Bamileke languages'),
(42, 'bak', 'ba', 'Bashkir'),
(43, 'bal', '', 'Baluchi'),
(44, 'bam', 'bm', 'Bambara'),
(45, 'ban', '', 'Balinese'),
(46, 'baq', 'eu', 'Basque (B)'),
(47, 'eus', 'eu', 'Basque (T)'),
(48, 'bas', '', 'Basa'),
(49, 'bat', '', 'Baltic languages'),
(50, 'bej', '', 'Beja Bedawiyet'),
(51, 'bel', 'be', 'Belarusian'),
(52, 'bem', '', 'Bemba'),
(53, 'ben', 'bn', 'Bengali'),
(54, 'ber', '', 'Berber languages'),
(55, 'bho', '', 'Bhojpuri'),
(56, 'bih', 'bh', 'Bihari languages'),
(57, 'bik', '', 'Bikol'),
(58, 'bin', '', 'Bini Edo'),
(59, 'bis', 'bi', 'Bislama'),
(60, 'bla', '', 'Siksika'),
(61, 'bnt', '', 'Bantu languages'),
(62, 'tib', 'bo', 'Tibetan (B)'),
(63, 'bod', 'bo', 'Tibetan (T)'),
(64, 'bos', 'bs', 'Bosnian'),
(65, 'bra', '', 'Braj'),
(66, 'bre', 'br', 'Breton'),
(67, 'btk', '', 'Batak languages'),
(68, 'bua', '', 'Buriat'),
(69, 'bug', '', 'Buginese'),
(70, 'bul', 'bg', 'Bulgarian'),
(71, 'bur', 'my', 'Burmese (B)'),
(72, 'mya', 'my', 'Burmese (T)'),
(73, 'byn', '', 'Blin Bilin'),
(74, 'cad', '', 'Caddo'),
(75, 'cai', '', 'Central American Indian languages'),
(76, 'car', '', 'Galibi Carib'),
(77, 'cat', 'ca', 'Catalan Valencian'),
(78, 'cau', '', 'Caucasian languages'),
(79, 'ceb', '', 'Cebuano'),
(80, 'cel', '', 'Celtic languages'),
(81, 'cze', 'zh', 'Czech (B)'),
(82, 'ces', 'zh', 'Czech (T)'),
(83, 'cha', 'ch', 'Chamorro'),
(84, 'chb', '', 'Chibcha'),
(85, 'che', 'ce', 'Chechen'),
(86, 'chg', '', 'Chagatai'),
(87, 'chi', 'zh', 'Chinese (B)'),
(88, 'zho', 'zh', 'Chinese (T)'),
(89, 'chk', '', 'Chuukese'),
(90, 'chm', '', 'Mari'),
(91, 'chn', '', 'Chinook jargon'),
(92, 'cho', '', 'Choctaw'),
(93, 'chp', '', 'Chipewyan Dene Suline'),
(95, 'chu', 'cu', 'Church Slavic Old Slavonic Church Slavonic Old Bulgarian Old Church Slavonic'),
(96, 'chv', 'cv', 'Chuvash'),
(97, 'chy', '', 'Cheyenne'),
(98, 'cmc', '', 'Chamic languages'),
(99, 'cop', '', 'Coptic'),
(100, 'cor', 'kw', 'Cornish'),
(101, 'cos', 'co', 'Corsican'),
(102, 'cpe', '', 'Creoles and pidgins, English based'),
(103, 'cpf', '', 'Creoles and pidgins, French-based'),
(104, 'cpp', '', 'Creoles and pidgins, Portuguese-based'),
(105, 'cre', 'cr', 'Cree'),
(106, 'crh', '', 'Crimean Tatar Crimean Turkish'),
(107, 'crp', '', 'Creoles and pidgins'),
(108, 'csb', '', 'Kashubian'),
(109, 'cus', '', 'Cushitic languages'),
(110, 'wel', 'cy', 'Welsh (B)'),
(111, 'cym', 'cy', 'Welsh (T)'),
(112, 'dak', '', 'Dakota'),
(113, 'dan', 'da', 'Danish'),
(114, 'dar', '', 'Dargwa'),
(115, 'day', '', 'Land Dayak languages'),
(116, 'del', '', 'Delaware'),
(117, 'den', '', 'Slave (Athapascan)'),
(118, 'ger', 'de', 'German (B)'),
(119, 'deu', 'de', 'German (T)'),
(120, 'dgr', '', 'Dogrib'),
(121, 'din', '', 'Dinka'),
(122, 'div', 'dv', 'Divehi Dhivehi Maldivian'),
(124, 'dra', '', 'Dravidian languages'),
(125, 'dsb', '', 'Lower Sorbian'),
(126, 'dua', '', 'Duala'),
(127, 'dum', '', 'Dutch, Middle (ca.1050-1350)'),
(128, 'dut', 'nl', 'Dutch Flemish (B)'),
(129, 'nld', 'nl', 'Dutch Flemish (T)'),
(130, 'dyu', '', 'Dyula'),
(131, 'dzo', 'dz', 'Dzongkha'),
(132, 'efi', '', 'Efik'),
(133, 'egy', '', 'Egyptian (Ancient)'),
(134, 'eka', '', 'Ekajuk'),
(135, 'gre', 'el', 'Greek, Modern (1453-) (B)'),
(136, 'ell', 'el', 'Greek, Modern (1453-) (T)'),
(137, 'elx', '', 'Elamite'),
(138, 'eng', 'en', 'English'),
(139, 'enm', '', 'English, Middle (1100-1500)'),
(140, 'epo', 'eo', 'Esperanto'),
(141, 'est', 'et', 'Estonian'),
(142, 'ewe', 'ee', 'Ewe'),
(143, 'ewo', '', 'Ewondo'),
(144, 'fan', '', 'Fang'),
(145, 'fao', 'fo', 'Faroese'),
(146, 'per', 'fa', 'Persian (B)'),
(147, 'fas', 'fa', 'Persian (T)'),
(148, 'fat', '', 'Fanti'),
(149, 'fij', 'fj', 'Fijian'),
(150, 'fil', '', 'Filipino Pilipino'),
(151, 'fin', 'fi', 'Finnish'),
(152, 'fiu', '', 'Finno-Ugrian languages'),
(153, 'fon', '', 'Fon'),
(154, 'fre', 'fr', 'French (B)'),
(155, 'fra', 'fr', 'French (T)'),
(156, 'frm', '', 'French, Middle (ca.1400-1600)'),
(157, 'fro', '', 'French, Old (842-ca.1400)'),
(158, 'frr', '', 'Northern Frisian'),
(159, 'frs', '', 'Eastern Frisian'),
(160, 'fry', 'fy', 'Western Frisian'),
(161, 'ful', 'ff', 'Fulah'),
(162, 'fur', '', 'Friulian'),
(163, 'gaa', '', 'Ga'),
(164, 'gay', '', 'Gayo'),
(165, 'gba', '', 'Gbaya'),
(166, 'gem', '', 'Germanic languages'),
(167, 'geo', 'ka', 'Georgian (B)'),
(168, 'kat', 'ka', 'Georgian (T)'),
(169, 'gez', '', 'Geez'),
(170, 'gil', '', 'Gilbertese'),
(171, 'gla', 'gd', 'Gaelic Scottish Gaelic'),
(173, 'glg', 'gl', 'Galician'),
(174, 'glv', 'gv', 'Manx'),
(175, 'gmh', '', 'German, Middle High (ca.1050-1500)'),
(176, 'goh', '', 'German, Old High (ca.750-1050)'),
(177, 'gon', '', 'Gondi'),
(178, 'gor', '', 'Gorontalo'),
(179, 'got', '', 'Gothic'),
(180, 'grb', '', 'Grebo'),
(181, 'grc', '', 'Greek, Ancient (to 1453)'),
(182, 'grn', 'gn', 'Guarani'),
(183, 'gsw', '', 'Swiss German Alemannic Alsatian'),
(184, 'guj', 'gu', 'Gujarati'),
(185, 'gwi', '', 'Gwich\'in'),
(186, 'hai', '', 'Haida'),
(187, 'hat', 'ht', 'Haitian Haitian Creole'),
(188, 'hau', 'ha', 'Hausa'),
(189, 'haw', '', 'Hawaiian'),
(190, 'heb', 'he', 'Hebrew'),
(191, 'her', 'hz', 'Herero'),
(192, 'hil', '', 'Hiligaynon'),
(193, 'him', '', 'Himachali languages Western Pahari languages'),
(194, 'hin', 'hi', 'Hindi'),
(195, 'hit', '', 'Hittite'),
(196, 'hmn', '', 'Hmong Mong'),
(197, 'hmo', 'ho', 'Hiri Motu'),
(198, 'hrv', 'hr', 'Croatian'),
(199, 'hsb', '', 'Upper Sorbian'),
(200, 'hun', 'hu', 'Hungarian'),
(201, 'hup', '', 'Hupa'),
(202, 'iba', '', 'Iban'),
(203, 'ibo', 'ig', 'Igbo'),
(204, 'ice', 'is', 'Icelandic (B)'),
(205, 'isl', 'is', 'Icelandic (T)'),
(206, 'ido', 'io', 'Ido'),
(207, 'iii', 'ii', 'Sichuan Yi Nuosu'),
(208, 'ijo', '', 'Ijo languages'),
(209, 'iku', 'iu', 'Inuktitut'),
(210, 'ile', 'ie', 'Interlingue Occidental'),
(211, 'ilo', '', 'Iloko'),
(212, 'ina', 'ia', 'Interlingua (International Auxiliary Language Association)'),
(213, 'inc', '', 'Indic languages'),
(214, 'ind', 'id', 'Indonesian'),
(215, 'ine', '', 'Indo-European languages'),
(216, 'inh', '', 'Ingush'),
(217, 'ipk', 'ik', 'Inupiaq'),
(218, 'ira', '', 'Iranian languages'),
(219, 'iro', '', 'Iroquoian languages'),
(220, 'ita', 'it', 'Italian'),
(221, 'jav', 'jv', 'Javanese'),
(222, 'jbo', '', 'Lojban'),
(223, 'jpn', 'ja', 'Japanese'),
(224, 'jpr', '', 'Judeo-Persian'),
(225, 'jrb', '', 'Judeo-Arabic'),
(226, 'kaa', '', 'Kara-Kalpak'),
(227, 'kab', '', 'Kabyle'),
(228, 'kac', '', 'Kachin Jingpho'),
(229, 'kal', 'kl', 'Kalaallisut Greenlandic'),
(230, 'kam', '', 'Kamba'),
(231, 'kan', 'kn', 'Kannada'),
(232, 'kar', '', 'Karen languages'),
(233, 'kas', 'ks', 'Kashmiri'),
(234, 'kau', 'kr', 'Kanuri'),
(235, 'kaw', '', 'Kawi'),
(236, 'kaz', 'kk', 'Kazakh'),
(237, 'kbd', '', 'Kabardian'),
(238, 'kha', '', 'Khasi'),
(239, 'khi', '', 'Khoisan languages'),
(240, 'khm', 'km', 'Central Khmer'),
(241, 'kho', '', 'Khotanese Sakan'),
(242, 'kik', 'ki', 'Kikuyu Gikuyu'),
(243, 'kin', 'rw', 'Kinyarwanda'),
(244, 'kir', 'ky', 'Kirghiz Kyrgyz'),
(245, 'kmb', '', 'Kimbundu'),
(246, 'kok', '', 'Konkani'),
(247, 'kom', 'kv', 'Komi'),
(248, 'kon', 'kg', 'Kongo'),
(249, 'kor', 'ko', 'Korean'),
(250, 'kos', '', 'Kosraean'),
(251, 'kpe', '', 'Kpelle'),
(252, 'krc', '', 'Karachay-Balkar'),
(253, 'krl', '', 'Karelian'),
(254, 'kro', '', 'Kru languages'),
(255, 'kru', '', 'Kurukh'),
(256, 'kua', 'kj', 'Kuanyama Kwanyama'),
(257, 'kum', '', 'Kumyk'),
(258, 'kur', 'ku', 'Kurdish'),
(259, 'kut', '', 'Kutenai'),
(260, 'lad', '', 'Ladino'),
(261, 'lah', '', 'Lahnda'),
(262, 'lam', '', 'Lamba'),
(263, 'lao', 'lo', 'Lao'),
(264, 'lat', 'la', 'Latin'),
(265, 'lav', 'lv', 'Latvian'),
(266, 'lez', '', 'Lezghian'),
(267, 'lim', 'li', 'Limburgan Limburger Limburgish'),
(268, 'lin', 'ln', 'Lingala'),
(269, 'lit', 'lt', 'Lithuanian'),
(270, 'lol', '', 'Mongo'),
(271, 'loz', '', 'Lozi'),
(272, 'ltz', 'lb', 'Luxembourgish Letzeburgesch'),
(273, 'lua', '', 'Luba-Lulua'),
(274, 'lub', 'lu', 'Luba-Katanga'),
(275, 'lug', 'lg', 'Ganda'),
(276, 'lui', '', 'Luiseno'),
(277, 'lun', '', 'Lunda'),
(278, 'luo', '', 'Luo (Kenya and Tanzania)'),
(279, 'lus', '', 'Lushai'),
(280, 'mac', 'mk', 'Macedonian (B)'),
(281, 'mkd', 'mk', 'Macedonian (T)'),
(282, 'mad', '', 'Madurese'),
(283, 'mag', '', 'Magahi'),
(284, 'mah', 'mh', 'Marshallese'),
(285, 'mai', '', 'Maithili'),
(286, 'mak', '', 'Makasar'),
(287, 'mal', 'ml', 'Malayalam'),
(288, 'man', '', 'Mandingo'),
(289, 'mao', 'mi', 'Maori (B)'),
(290, 'mri', 'mi', 'Maori (T)'),
(291, 'map', '', 'Austronesian languages'),
(292, 'mar', 'mr', 'Marathi'),
(293, 'mas', '', 'Masai'),
(294, 'may', 'ms', 'Malay (B)'),
(295, 'msa', 'ms', 'Malay (T)'),
(296, 'mdf', '', 'Moksha'),
(297, 'mdr', '', 'Mandar'),
(298, 'men', '', 'Mende'),
(299, 'mga', '', 'Irish, Middle (900-1200)'),
(300, 'mic', '', 'Mi\'kmaq Micmac'),
(301, 'min', '', 'Minangkabau'),
(302, 'mis', '', 'Uncoded languages'),
(303, 'mkh', '', 'Mon-Khmer languages'),
(304, 'mlg', 'mg', 'Malagasy'),
(305, 'mlt', 'mt', 'Maltese'),
(306, 'mnc', '', 'Manchu'),
(307, 'mni', '', 'Manipuri'),
(308, 'mno', '', 'Manobo languages'),
(309, 'moh', '', 'Mohawk'),
(310, 'mon', 'mn', 'Mongolian'),
(311, 'mos', '', 'Mossi'),
(312, 'mul', '', 'Multiple languages'),
(313, 'mun', '', 'Munda languages'),
(314, 'mus', '', 'Creek'),
(315, 'mwl', '', 'Mirandese'),
(316, 'mwr', '', 'Marwari'),
(317, 'myn', '', 'Mayan languages'),
(318, 'myv', '', 'Erzya'),
(319, 'nah', '', 'Nahuatl languages'),
(320, 'nai', '', 'North American Indian languages'),
(321, 'nap', '', 'Neapolitan'),
(322, 'nau', 'na', 'Nauru'),
(323, 'nav', 'nv', 'Navajo Navaho'),
(324, 'nbl', 'nr', 'Ndebele, South South Ndebele'),
(325, 'nde', 'nd', 'Ndebele, North North Ndebele'),
(326, 'ndo', 'ng', 'Ndonga'),
(327, 'nds', '', 'Low German Low Saxon'),
(328, 'nep', 'ne', 'Nepali'),
(329, 'new', '', 'Nepal Bhasa Newari'),
(330, 'nia', '', 'Nias'),
(331, 'nic', '', 'Niger-Kordofanian languages'),
(332, 'niu', '', 'Niuean'),
(333, 'nno', 'nn', 'Norwegian Nynorsk Nynorsk, Norwegian'),
(334, 'nob', 'nb', 'Bokmål, Norwegian Norwegian Bokmål'),
(335, 'nog', '', 'Nogai'),
(336, 'non', '', 'Norse, Old'),
(337, 'nor', 'no', 'Norwegian'),
(338, 'nqo', '', 'N\'Ko'),
(339, 'nso', '', 'Pedi Sepedi Northern Sotho'),
(340, 'nub', '', 'Nubian languages'),
(341, 'nwc', '', 'Classical Newari Old Newari Classical Nepal Bhasa'),
(342, 'nya', 'ny', 'Chichewa Chewa Nyanja'),
(343, 'nym', '', 'Nyamwezi'),
(344, 'nyn', '', 'Nyankole'),
(345, 'nyo', '', 'Nyoro'),
(346, 'nzi', '', 'Nzima'),
(347, 'oci', 'oc', 'Occitan (post 1500)'),
(348, 'oji', 'oj', 'Ojibwa'),
(349, 'ori', 'or', 'Oriya'),
(350, 'orm', 'om', 'Oromo'),
(351, 'osa', '', 'Osage'),
(352, 'oss', 'os', 'Ossetian Ossetic'),
(353, 'ota', '', 'Turkish, Ottoman (1500-1928)'),
(354, 'oto', '', 'Otomian languages'),
(355, 'paa', '', 'Papuan languages'),
(356, 'pag', '', 'Pangasinan'),
(357, 'pal', '', 'Pahlavi'),
(358, 'pam', '', 'Pampanga Kapampangan'),
(359, 'pan', 'pa', 'Panjabi Punjabi'),
(360, 'pap', '', 'Papiamento'),
(361, 'pau', '', 'Palauan'),
(362, 'peo', '', 'Persian, Old (ca.600-400 B.C.)'),
(363, 'phi', '', 'Philippine languages'),
(364, 'phn', '', 'Phoenician'),
(365, 'pli', 'pi', 'Pali'),
(366, 'pol', 'pl', 'Polish'),
(367, 'pon', '', 'Pohnpeian'),
(368, 'por', 'pt', 'Portuguese'),
(369, 'pra', '', 'Prakrit languages'),
(370, 'pro', '', 'Provençal, Old (to 1500) Occitan'),
(371, 'pus', 'ps', 'Pushto Pashto'),
(373, 'que', 'qu', 'Quechua'),
(374, 'raj', '', 'Rajasthani'),
(375, 'rap', '', 'Rapanui'),
(376, 'rar', '', 'Rarotongan Cook Islands Maori'),
(377, 'roa', '', 'Romance languages'),
(378, 'roh', 'rm', 'Romansh'),
(379, 'rom', '', 'Romany'),
(380, 'rum', 'ro', 'Romanian Moldavian Moldovan (B)'),
(381, 'ron', 'ro', 'Romanian Moldavian Moldovan (T)'),
(382, 'run', 'rn', 'Rundi'),
(383, 'rup', '', 'Aromanian Arumanian Macedo-Romanian'),
(384, 'rus', 'ru', 'Russian'),
(385, 'sad', '', 'Sandawe'),
(386, 'sag', 'sg', 'Sango'),
(387, 'sah', '', 'Yakut'),
(388, 'sai', '', 'South American Indian languages'),
(389, 'sal', '', 'Salishan languages'),
(390, 'sam', '', 'Samaritan Aramaic'),
(391, 'san', 'sa', 'Sanskrit'),
(392, 'sas', '', 'Sasak'),
(393, 'sat', '', 'Santali'),
(394, 'scn', '', 'Sicilian'),
(395, 'sco', '', 'Scots'),
(396, 'sel', '', 'Selkup'),
(397, 'sem', '', 'Semitic languages'),
(398, 'sga', '', 'Irish, Old (to 900)'),
(399, 'sgn', '', 'Sign Languages'),
(400, 'shn', '', 'Shan'),
(401, 'sid', '', 'Sidamo'),
(402, 'sin', 'si', 'Sinhala Sinhalese'),
(403, 'sio', '', 'Siouan languages'),
(404, 'sit', '', 'Sino-Tibetan languages'),
(405, 'sla', '', 'Slavic languages'),
(406, 'slo', 'sk', 'Slovak (B)'),
(407, 'slk', 'sk', 'Slovak (T)'),
(408, 'slv', 'sl', 'Slovenian'),
(409, 'sma', '', 'Southern Sami'),
(410, 'sme', 'se', 'Northern Sami'),
(411, 'smi', '', 'Sami languages'),
(412, 'smj', '', 'Lule Sami'),
(413, 'smn', '', 'Inari Sami'),
(414, 'smo', 'sm', 'Samoan'),
(415, 'sms', '', 'Skolt Sami'),
(416, 'sna', 'sn', 'Shona'),
(417, 'snd', 'sd', 'Sindhi'),
(418, 'snk', '', 'Soninke'),
(419, 'sog', '', 'Sogdian'),
(420, 'som', 'so', 'Somali'),
(421, 'son', '', 'Songhai languages'),
(422, 'sot', 'st', 'Sotho, Southern'),
(423, 'spa', 'es', 'Spanish Castilian'),
(424, 'srd', 'sc', 'Sardinian'),
(425, 'srn', '', 'Sranan Tongo'),
(426, 'srp', 'sr', 'Serbian'),
(427, 'srr', '', 'Serer'),
(428, 'ssa', '', 'Nilo-Saharan languages'),
(429, 'ssw', 'ss', 'Swati'),
(430, 'suk', '', 'Sukuma'),
(431, 'sun', 'su', 'Sundanese'),
(432, 'sus', '', 'Susu'),
(433, 'sux', '', 'Sumerian'),
(434, 'swa', 'sw', 'Swahili'),
(435, 'swe', 'sv', 'Swedish'),
(436, 'syc', '', 'Classical Syriac'),
(437, 'syr', '', 'Syriac'),
(438, 'tah', 'ty', 'Tahitian'),
(439, 'tai', '', 'Tai languages'),
(440, 'tam', 'ta', 'Tamil'),
(441, 'tat', 'tt', 'Tatar'),
(442, 'tel', 'te', 'Telugu'),
(443, 'tem', '', 'Timne'),
(444, 'ter', '', 'Tereno'),
(445, 'tet', '', 'Tetum'),
(446, 'tgk', 'tg', 'Tajik'),
(447, 'tgl', 'tl', 'Tagalog'),
(448, 'tha', 'th', 'Thai'),
(449, 'tig', '', 'Tigre'),
(450, 'tir', 'ti', 'Tigrinya'),
(451, 'tiv', '', 'Tiv'),
(452, 'tkl', '', 'Tokelau'),
(453, 'tlh', '', 'Klingon tlhIngan-Hol'),
(454, 'tli', '', 'Tlingit'),
(455, 'tmh', '', 'Tamashek'),
(456, 'tog', '', 'Tonga (Nyasa)'),
(457, 'ton', 'to', 'Tonga (Tonga Islands)'),
(458, 'tpi', '', 'Tok Pisin'),
(459, 'tsi', '', 'Tsimshian'),
(460, 'tsn', 'tn', 'Tswana'),
(461, 'tso', 'ts', 'Tsonga'),
(462, 'tuk', 'tk', 'Turkmen'),
(463, 'tum', '', 'Tumbuka'),
(464, 'tup', '', 'Tupi languages'),
(465, 'tur', 'tr', 'Turkish'),
(466, 'tut', '', 'Altaic languages'),
(467, 'tvl', '', 'Tuvalu'),
(468, 'twi', 'tw', 'Twi'),
(469, 'tyv', '', 'Tuvinian'),
(470, 'udm', '', 'Udmurt'),
(471, 'uga', '', 'Ugaritic'),
(472, 'uig', 'ug', 'Uighur Uyghur'),
(473, 'ukr', 'uk', 'Ukrainian'),
(474, 'umb', '', 'Umbundu'),
(475, 'und', '', 'Undetermined'),
(476, 'urd', 'ur', 'Urdu'),
(477, 'uzb', 'uz', 'Uzbek'),
(478, 'vai', '', 'Vai'),
(479, 'ven', 've', 'Venda'),
(480, 'vie', 'vi', 'Vietnamese'),
(481, 'vol', 'vo', 'Volapük'),
(482, 'vot', '', 'Votic'),
(483, 'wak', '', 'Wakashan languages'),
(484, 'wal', '', 'Wolaitta Wolaytta'),
(485, 'war', '', 'Waray'),
(486, 'was', '', 'Washo'),
(487, 'wen', '', 'Sorbian languages'),
(488, 'wln', 'wa', 'Walloon'),
(489, 'wol', 'wo', 'Wolof'),
(490, 'xal', '', 'Kalmyk Oirat'),
(491, 'xho', 'xh', 'Xhosa'),
(492, 'yao', '', 'Yao'),
(493, 'yap', '', 'Yapese'),
(494, 'yid', 'yi', 'Yiddish'),
(495, 'yor', 'yo', 'Yoruba'),
(496, 'ypk', '', 'Yupik languages'),
(497, 'zap', '', 'Zapotec'),
(498, 'zbl', '', 'Blissymbols Blissymbolics Bliss'),
(499, 'zen', '', 'Zenaga'),
(500, 'zgh', '', 'Standard Moroccan Tamazight'),
(501, 'zha', 'za', 'Zhuang Chuang'),
(502, 'znd', '', 'Zande languages'),
(503, 'zul', 'zu', 'Zulu'),
(504, 'zun', '', 'Zuni'),
(505, 'zxx', '', 'No linguistic content Not applicable'),
(506, 'zza', '', 'Zaza Dimili Dimli Kirdki Kirmanjki Zazaki');

-- --------------------------------------------------------

--
-- Structure de la table `library`
--

CREATE TABLE `library` (
  `id` int(11) NOT NULL,
  `library_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `library_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `library_identifier` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mathematical_parameter`
--

CREATE TABLE `mathematical_parameter` (
  `id` int(11) NOT NULL,
  `argument_shift` int(11) DEFAULT NULL,
  `entry_shift` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `argument_displacement_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entry_displacement_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `argument_displacement_float` double DEFAULT NULL,
  `entry_displacement_float` double DEFAULT NULL,
  `type_of_number_id` int(11) NOT NULL,
  `type_of_parameter` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `number_unit`
--

CREATE TABLE `number_unit` (
  `id` int(11) NOT NULL,
  `unit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `number_unit`
--

INSERT INTO `number_unit` (`id`, `unit`, `code_name`) VALUES
(1, 'degree', 'degree'),
(2, 'day', 'day'),
(3, 'degree/day', 'degree_day'),
(4, 'degree/hour', 'degree_hour'),
(5, 'du', 'du'),
(6, 'du/day', 'du_day'),
(7, 'du/hour', 'du_hour'),
(10, 'no unit', 'no_unit');

-- --------------------------------------------------------

--
-- Structure de la table `original_text`
--

CREATE TABLE `original_text` (
  `id` int(11) NOT NULL,
  `text_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `original_text_title` longtext COLLATE utf8_unicode_ci,
  `image_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tpq` int(11) DEFAULT NULL,
  `taq` int(11) DEFAULT NULL,
  `page_range` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `public` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `table_type_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `primary_source_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  `historical_actor_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `script_id` int(11) DEFAULT NULL,
  `small_text_title` longtext COLLATE utf8_unicode_ci,
  `page_min` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_max` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_folio` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parameter_definition`
--

CREATE TABLE `parameter_definition` (
  `id` int(11) NOT NULL,
  `name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `definition` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parameter_feature`
--

CREATE TABLE `parameter_feature` (
  `id` int(11) NOT NULL,
  `feature` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `parameter_feature`
--

INSERT INTO `parameter_feature` (`id`, `feature`) VALUES
(1, 'one number'),
(2, 'one number and a range'),
(4, 'one number and three ranges'),
(3, 'one number and two ranges');

-- --------------------------------------------------------

--
-- Structure de la table `parameter_format`
--

CREATE TABLE `parameter_format` (
  `id` int(11) NOT NULL,
  `parameter_type_id` int(11) DEFAULT NULL,
  `parameter_unit_id` int(11) DEFAULT NULL,
  `parameter_feature_id` int(11) DEFAULT NULL,
  `parameter_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parameter_group_id` int(11) DEFAULT NULL,
  `table_type_id` int(11) DEFAULT NULL,
  `parameter_definition_id` int(11) DEFAULT NULL,
  `formula_definition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `parameter_format`
--

INSERT INTO `parameter_format` (`id`, `parameter_type_id`, `parameter_unit_id`, `parameter_feature_id`, `parameter_name`, `parameter_group_id`, `table_type_id`, `parameter_definition_id`, `formula_definition_id`) VALUES
(1, 3, 10, 1, 'chords: chord norm', NULL, 5, NULL, NULL),
(2, 3, 10, 1, 'sine: radius of the circle', NULL, 6, NULL, NULL),
(3, 3, 1, 1, 'declination: obliquity of the ecliptic', NULL, 8, NULL, NULL),
(4, 3, 10, 1, 'shadow: length of the gnomon entry at 45°', NULL, 7, NULL, NULL),
(5, 2, 1, 1, 'right ascension: right ascension of cap', 1, 9, NULL, NULL),
(6, 2, 1, 1, 'right ascension: right ascension of aqr', 1, 9, NULL, NULL),
(7, 2, 1, 1, 'right ascension: righ ascension of psc', 1, 9, NULL, NULL),
(8, 1, 1, 1, 'right ascension: obliquity of the ecliptic', NULL, 9, NULL, NULL),
(9, 2, 1, 1, 'oblique ascension: oblique ascension of ari', 2, 10, NULL, NULL),
(10, 2, 1, 1, 'oblique ascension: oblique ascension of tau', 2, 10, NULL, NULL),
(11, 2, 1, 1, 'oblique ascension: oblique ascension of gem', 2, 10, NULL, NULL),
(12, 1, 1, 1, 'oblique ascension: obliquity of the ecliptic (ra)', NULL, 10, NULL, NULL),
(13, 1, 1, 1, 'oblique ascension: obliquity of the ecliptic (ad)', NULL, 10, NULL, NULL),
(14, 1, 1, 1, 'oblique ascension: geographical latitude', NULL, 10, NULL, NULL),
(15, 2, 12, 2, 'length of diurnal seasonal hour: maximum value', NULL, 12, NULL, NULL),
(16, 1, 1, 1, 'length of diurnal seasonal hour: obliquity of the ecliptic', NULL, 12, NULL, NULL),
(17, 1, 1, 1, 'length of diurnal seasonal hour: geographical latitude', NULL, 12, NULL, NULL),
(18, 2, 1, 1, 'ascensional difference: entry at 90°', NULL, 11, NULL, NULL),
(19, 1, 1, 1, 'ascensional difference: obliquity of the ecliptic', NULL, 11, NULL, NULL),
(20, 1, 1, 1, 'ascensional difference: geographical latitude', NULL, 11, NULL, NULL),
(21, 2, 11, 2, 'equation of time: low minimum', 3, 1, NULL, NULL),
(22, 2, 11, 2, 'equation of time: low maximum', 3, 1, NULL, NULL),
(23, 2, 11, 2, 'equation of time: high minimum', 3, 1, NULL, NULL),
(24, 2, 11, 2, 'equation of time: high maximum', 3, 1, NULL, NULL),
(25, 1, 1, 1, 'equation of time: obliquity of the ecliptic', NULL, 1, NULL, NULL),
(26, 1, 1, 1, 'equation of time: solar eccentricity', NULL, 1, NULL, NULL),
(27, 1, 1, 1, 'equation of time: solar apogee longitude', NULL, 1, NULL, NULL),
(28, 1, 1, 1, 'equation of time: epoch constant', NULL, 1, NULL, NULL),
(29, 1, 10, 1, 'equation of time: hour conversion rate', NULL, 1, NULL, NULL),
(30, 3, 3, 1, 'mean motion access and recess: value for 1 day', NULL, 13, NULL, NULL),
(31, 3, 3, 1, 'mean motion apogees and stars: value for 1 day', NULL, 14, NULL, NULL),
(32, 3, 3, 1, 'mean motion solar apogees: value for 1 day', NULL, 17, NULL, NULL),
(33, 3, 3, 1, 'mean motion precesssion: value for 1 day', NULL, 18, NULL, NULL),
(34, 3, 3, 1, 'mean motion solar tropical longitude: value for 1 day', NULL, 2, NULL, NULL),
(35, 3, 2, 1, 'mean motion solar tropical longitude: length of the tropical year in days', NULL, 2, NULL, NULL),
(36, 3, 1, 1, 'mean motion solar sideral longitude: value for 1 day', NULL, 3, NULL, NULL),
(37, 3, 2, 1, 'mean motion solar sideral longitude : length of the sideral year in days', NULL, 3, NULL, NULL),
(38, 3, 3, 1, 'mean motion lunar longitude: value for 1 day', NULL, 37, NULL, NULL),
(39, 3, 3, 1, 'mean motion lunar anomaly: value for 1 day', NULL, 38, NULL, NULL),
(40, 3, 3, 1, 'mean motion lunar node: value for 1 day', NULL, 42, NULL, NULL),
(41, 3, 3, 1, 'mean motion longitude plus lunar node: value for 1 day', NULL, 43, NULL, NULL),
(42, 3, 3, 1, 'mean motion lunar elongation: value for 1 day', NULL, 44, NULL, NULL),
(43, 3, 3, 1, 'mean motion double elongation: value for 1 day', NULL, 45, NULL, NULL),
(44, 3, 3, 1, 'mean motion longitude saturn: value for 1 day', NULL, 61, NULL, NULL),
(45, 3, 3, 1, 'mean motion longitude jupiter: value for 1 day', NULL, 55, NULL, NULL),
(46, 3, 3, 1, 'mean motion longitude mars: value for 1 day', NULL, 49, NULL, NULL),
(47, 3, 3, 1, 'mean motion anomaly venus: value for 1 day', NULL, 31, NULL, NULL),
(48, 3, 3, 1, 'mean motion anomaly mercury: value for 1 day', NULL, 25, NULL, NULL),
(49, 2, 11, 2, 'equation of the sun: maximum value', NULL, 4, NULL, NULL),
(50, 1, 1, 1, 'equation of the sun: solar eccentricity', NULL, 4, NULL, NULL),
(51, 2, 11, 2, 'equation  moon center: maximum value', NULL, 46, NULL, NULL),
(52, 1, 1, 1, 'equation  moon center: lunar eccentricity', NULL, 46, NULL, NULL),
(53, 2, 11, 2, 'equation moon anomaly: maximum value', NULL, 47, NULL, NULL),
(54, 1, 1, 1, 'equation moon anomaly: lunar eccentricity', NULL, 47, NULL, NULL),
(55, 1, 10, 1, 'equation moon anomaly: lunar epicycle radius', NULL, 47, NULL, NULL),
(56, 2, 11, 2, 'equation saturn center: maximum value', NULL, 62, NULL, NULL),
(57, 1, 1, 1, 'equation saturn center: eccentricity', NULL, 62, NULL, NULL),
(58, 2, 11, 2, 'equation saturn anomaly at mean distance: maximum value', NULL, 63, NULL, NULL),
(59, 1, 1, 1, 'equation saturn anomaly at mean distance: eccentricity', NULL, 63, NULL, NULL),
(60, 1, 10, 1, 'equation saturn anomaly at mean distance: radius of the epicycle', NULL, 63, NULL, NULL),
(61, 2, 11, 2, 'equation saturn anomaly at maximum distance: maximum value', NULL, 74, NULL, NULL),
(62, 1, 1, 1, 'equation saturn anomaly at maximum distance: eccentricity', NULL, 74, NULL, NULL),
(63, 1, 10, 1, 'equation saturn anomaly at maximum distance: radius of the epicycle', NULL, 74, NULL, NULL),
(64, 2, 11, 2, 'equation saturn anomaly at minimum distance: maximum value', NULL, 75, NULL, NULL),
(65, 1, 1, 1, 'equation saturn anomaly at minimum distance: eccentricity', NULL, 75, NULL, NULL),
(66, 1, 10, 1, 'equation saturn anomaly at minimum distance: radius of the epicycle', NULL, 75, NULL, NULL),
(67, 2, 11, 2, 'equation jupiter center: maximum value', NULL, 56, NULL, NULL),
(68, 1, 1, 1, 'equation jupiter center: eccentricity', NULL, 56, NULL, NULL),
(69, 2, 11, 2, 'equation jupiter anomaly at mean distance: maximum value', NULL, 57, NULL, NULL),
(70, 1, 1, 1, 'equation jupiter anomaly at mean distance: eccentricity', NULL, 57, NULL, NULL),
(71, 1, 10, 1, 'equation jupiter anomaly at mean distance: radius of the epicycle', NULL, 57, NULL, NULL),
(72, 2, 11, 2, 'equation jupiter anomaly at maximum distance: maximum value', NULL, 68, NULL, NULL),
(73, 1, 1, 1, 'equation jupiter anomaly at maximum distance: eccentricity', NULL, 68, NULL, NULL),
(74, 1, 10, 1, 'equation jupiter anomaly at maximum distance: radius of the epicycle', NULL, 68, NULL, NULL),
(75, 2, 11, 2, 'equation jupiter anomaly at minimum distance: maximum value', NULL, 69, NULL, NULL),
(76, 1, 1, 1, 'equation jupiter anomaly at minimum distance: eccentricity', NULL, 69, NULL, NULL),
(77, 1, 10, 1, 'equation jupiter anomaly at minimum distance: radius of the epicycle', NULL, 69, NULL, NULL),
(78, 2, 11, 2, 'equation mars center: maximum value', NULL, 50, NULL, NULL),
(79, 1, 1, 1, 'equation mars center: eccentricity', NULL, 50, NULL, NULL),
(80, 2, 11, 2, 'equation mars anomaly at mean distance: maximum value', NULL, 51, NULL, NULL),
(81, 1, 1, 1, 'equation mars anomaly at mean distance: eccentricity', NULL, 51, NULL, NULL),
(82, 1, 10, 1, 'equation mars anomaly at mean distance: radius of the epicycle', NULL, 51, NULL, NULL),
(83, 2, 11, 2, 'equation mars anomaly at maximum distance: maximum value', NULL, 70, NULL, NULL),
(84, 1, 1, 1, 'equation mars anomaly at maximum distance: eccentricity', NULL, 70, NULL, NULL),
(85, 1, 10, 1, 'equation mars anomaly at maximum distance: radius of the epicycle', NULL, 70, NULL, NULL),
(86, 2, 11, 2, 'equation mars anomaly at minimum distance: maximum value', NULL, 71, NULL, NULL),
(87, 1, 1, 1, 'equation mars anomaly at minimum distance: eccentricity', NULL, 71, NULL, NULL),
(88, 1, 10, 1, 'equation mars anomaly at minimum distance: radius of the epicycle', NULL, 71, NULL, NULL),
(89, 2, 11, 2, 'equation venus center: maximum value', NULL, 32, NULL, NULL),
(90, 1, 1, 1, 'equation venus center: eccentricity', NULL, 32, NULL, NULL),
(91, 2, 11, 2, 'equation venus anomaly at mean distance: maximum value', NULL, 33, NULL, NULL),
(92, 1, 1, 1, 'equation venus anomaly at mean distance: eccentricity', NULL, 33, NULL, NULL),
(93, 1, 10, 1, 'equation venus anomaly at mean distance: radius of the epicycle', NULL, 33, NULL, NULL),
(94, 2, 11, 2, 'equation venus anomaly at maximum distance: maximum value', NULL, 76, NULL, NULL),
(95, 1, 1, 1, 'equation venus anomaly at maximum distance: eccentricity', NULL, 76, NULL, NULL),
(96, 1, 10, 1, 'equation venus anomaly at maximum distance: radius of the epicycle', NULL, 76, NULL, NULL),
(97, 2, 11, 2, 'equation venus anomaly at minimum distance: maximum value', NULL, 77, NULL, NULL),
(98, 1, 1, 1, 'equation venus anomaly at minimum distance: eccentricity', NULL, 77, NULL, NULL),
(99, 1, 10, 1, 'equation venus anomaly at minimum distance: radius of the epicycle', NULL, 77, NULL, NULL),
(100, 2, 11, 2, 'equation mercury center: maximum value', NULL, 26, NULL, NULL),
(101, 1, 1, 1, 'equation mercury center: eccentricity', NULL, 26, NULL, NULL),
(102, 2, 11, 2, 'equation mercury anomaly at mean distance: maximum value', NULL, 27, NULL, NULL),
(103, 1, 1, 1, 'equation mercury anomaly at mean distance: eccentricity', NULL, 27, NULL, NULL),
(104, 1, 10, 1, 'equation mercury anomaly at mean distance: radius of the epicycle', NULL, 27, NULL, NULL),
(105, 2, 11, 2, 'equation mercury anomaly at maximum distance: maximum value', NULL, 72, NULL, NULL),
(106, 1, 1, 1, 'equation mercury anomaly at maximum distance: eccentricity', NULL, 72, NULL, NULL),
(107, 1, 10, 1, 'equation mercury anomaly at maximum distance: radius of the epicycle', NULL, 72, NULL, NULL),
(108, 2, 11, 2, 'equation mercury anomaly at minimum distance: maximum value', NULL, 73, NULL, NULL),
(109, 1, 1, 1, 'equation mercury anomaly at minimum distance: eccentricity', NULL, 73, NULL, NULL),
(110, 1, 10, 1, 'equation mercury anomaly at minimum distance: radius of the epicycle', NULL, 73, NULL, NULL),
(111, 2, 111, 3, 'total equation double argument table saturn: maximum value', NULL, 64, NULL, NULL),
(112, 1, 1, 1, 'total equation double argument table saturn: eccentricity', NULL, 64, NULL, NULL),
(113, 1, 10, 1, 'total equation double argument table saturn: radius of the epicycle', NULL, 64, NULL, NULL),
(114, 2, 111, 3, 'total equation double argument table jupiter: maximum value', NULL, 58, NULL, NULL),
(115, 1, 1, 1, 'total equation double argument table jupiter: eccentricity', NULL, 58, NULL, NULL),
(116, 1, 10, 1, 'total equation double argument table jupiter: radius of the epicycle', NULL, 58, NULL, NULL),
(117, 2, 111, 3, 'total equation double argument table mars: maximum value', NULL, 52, NULL, NULL),
(118, 1, 1, 1, 'total equation double argument table mars: eccentricity', NULL, 52, NULL, NULL),
(119, 1, 10, 1, 'total equation double argument table mars: radius of the epicycle', NULL, 52, NULL, NULL),
(120, 2, 111, 3, 'total equation double argument table venus: maximum value', NULL, 34, NULL, NULL),
(121, 1, 1, 1, 'total equation double argument table venus: eccentricity', NULL, 34, NULL, NULL),
(122, 1, 10, 1, 'total equation double argument table venus: radius of the epicycle', NULL, 34, NULL, NULL),
(123, 2, 111, 3, 'total equation double argument table mercury: maximum value', NULL, 28, NULL, NULL),
(124, 1, 1, 1, 'total equation double argument table mercury: eccentricity', NULL, 28, NULL, NULL),
(125, 1, 10, 1, 'total equation double argument table mercury: radius of the epicycle', NULL, 28, NULL, NULL),
(126, 2, 4, 1, 'solar velocities: maximum value', 4, 20, NULL, NULL),
(127, 2, 4, 1, 'solar velocities: minimum value', 4, 20, NULL, NULL),
(128, 2, 1, 1, 'solar velocities: interval', 4, 20, NULL, NULL),
(129, 2, 4, 1, 'lunar velocities: maximum value', 5, 23, NULL, NULL),
(130, 2, 4, 1, 'lunar velocities: minimum value', 5, 23, NULL, NULL),
(131, 2, 1, 1, 'lunar velocities: interval', 5, 23, NULL, NULL),
(132, 2, 1, 1, 'lunar latitude: maximum value', NULL, 48, NULL, NULL),
(133, 2, 1, 1, 'saturn latitude: maximum value', 6, 65, NULL, NULL),
(134, 2, 1, 1, 'saturn latitude: minimum value', 6, 65, NULL, NULL),
(135, 1, 1, 1, 'saturn latitude: inclination of the deferent plane', NULL, 65, NULL, NULL),
(136, 1, 1, 1, 'saturn latitude: maxium deviation of the epicycle', NULL, 65, NULL, NULL),
(137, 1, 1, 1, 'saturn latitude: northern limit', NULL, 65, NULL, NULL),
(138, 2, 1, 1, 'jupiter latitude: maximum value', 7, 59, NULL, NULL),
(139, 2, 1, 1, 'jupiter latitude: minimum value', 7, 59, NULL, NULL),
(140, 1, 1, 1, 'jupiter latitude: inclination of the deferent plane', NULL, 59, NULL, NULL),
(141, 1, 1, 1, 'jupiter latitude: maxium deviation of the epicycle', NULL, 59, NULL, NULL),
(142, 1, 1, 1, 'jupiter latitude: northern limit', NULL, 59, NULL, NULL),
(143, 2, 1, 1, 'mars latitude: maximum value', 8, 53, NULL, NULL),
(144, 2, 1, 1, 'mars latitude: minimum value', 8, 53, NULL, NULL),
(145, 1, 1, 1, 'mars latitude: inclination of the deferent plane', NULL, 53, NULL, NULL),
(146, 1, 1, 1, 'mars latitude: maxium deviation of the epicycle', NULL, 53, NULL, NULL),
(147, 1, 1, 1, 'mars latitude: northern limit', NULL, 53, NULL, NULL),
(148, 2, 1, 1, 'venus latitude: maximum value', 9, 35, NULL, NULL),
(149, 2, 1, 1, 'venus latitude: minimum value', 9, 35, NULL, NULL),
(150, 1, 1, 1, 'venus latitude: maximum inclination of the deferent', NULL, 35, NULL, NULL),
(151, 1, 1, 1, 'venus latitude: maximum deviation of the epicycle', NULL, 35, NULL, NULL),
(152, 1, 1, 1, 'venus latitude: maximum slant of the epiycle', NULL, 35, NULL, NULL),
(153, 2, 1, 1, 'mercury latitude: maximum value', 10, 29, NULL, NULL),
(154, 2, 1, 1, 'mercury latitude: minimum value', 10, 29, NULL, NULL),
(155, 1, 1, 1, 'mercury latitude: maximum inclination of the deferent', NULL, 29, NULL, NULL),
(156, 1, 1, 1, 'mercury latitude: maximum deviation of the epicycle', NULL, 29, NULL, NULL),
(157, 1, 1, 1, 'mercury latitude: maximum slant of the epiycle', NULL, 29, NULL, NULL),
(158, 2, 1, 1, 'planetary stations saturn: value at apogee', 11, 66, NULL, NULL),
(159, 2, 1, 1, 'planetary stations saturn: central station', 11, 66, NULL, NULL),
(160, 2, 1, 1, 'planetary stations saturn: station at perigee', 11, 66, NULL, NULL),
(161, 1, 1, 1, 'planetary stations saturn: planetary eccentricity', NULL, 66, NULL, NULL),
(162, 1, 10, 1, 'planetary stations saturn: planetary epicycle radius', NULL, 66, NULL, NULL),
(163, 1, 10, 1, 'planetary stations saturn: velocity quotient', NULL, 66, NULL, NULL),
(164, 2, 1, 1, 'planetary stations jupiter: value at apogee', 12, 60, NULL, NULL),
(165, 2, 1, 1, 'planetary stations jupiter: central station', 12, 60, NULL, NULL),
(166, 2, 1, 1, 'planetary stations jupiter: station at perigee', 12, 60, NULL, NULL),
(167, 1, 1, 1, 'planetary stations jupiter: planetary eccentricity', NULL, 60, NULL, NULL),
(168, 1, 10, 1, 'planetary stations jupiter: planetary epicycle radius', NULL, 60, NULL, NULL),
(169, 1, 10, 1, 'planetary stations jupiter: velocity quotient', NULL, 60, NULL, NULL),
(170, 2, 1, 1, 'planetary stations mars: value at apogee', 13, 54, NULL, NULL),
(171, 2, 1, 1, 'planetary stations mars: central station', 13, 54, NULL, NULL),
(172, 2, 1, 1, 'planetary stations mars: station at perigee', 13, 54, NULL, NULL),
(173, 1, 1, 1, 'planetary stations mars: planetary eccentricity', NULL, 54, NULL, NULL),
(174, 1, 10, 1, 'planetary stations mars: planetary epicycle radius', NULL, 54, NULL, NULL),
(175, 1, 10, 1, 'planetary stations mars: velocity quotient', NULL, 54, NULL, NULL),
(176, 2, 1, 1, 'planetary stations venus: value at apogee', 14, 36, NULL, NULL),
(177, 2, 1, 1, 'planetary stations venus: central station', 14, 36, NULL, NULL),
(178, 2, 1, 1, 'planetary stations venus: station at perigee', 14, 36, NULL, NULL),
(179, 1, 1, 1, 'planetary stations venus: planetary eccentricity', NULL, 36, NULL, NULL),
(180, 1, 10, 1, 'planetary stations venus: planetary epicycle radius', NULL, 36, NULL, NULL),
(181, 1, 10, 1, 'planetary stations venus: velocity quotient', NULL, 36, NULL, NULL),
(182, 2, 1, 1, 'planetary stations mercury: value at apogee', 15, 30, NULL, NULL),
(183, 2, 1, 1, 'planetary stations mercury: central station', 15, 30, NULL, NULL),
(184, 2, 1, 1, 'planetary stations mercury: station at perigee', 15, 30, NULL, NULL),
(185, 1, 1, 1, 'planetary stations mercury: planetary eccentricity', NULL, 30, NULL, NULL),
(186, 1, 10, 1, 'planetary stations mercury: planetary epicycle radius', NULL, 30, NULL, NULL),
(187, 1, 10, 1, 'planetary stations mercury: velocity quotient', NULL, 30, NULL, NULL),
(188, 2, 2, 1, 'parallax: maximum value of the argument for cancer', 16, 24, NULL, NULL),
(189, 2, 1, 1, 'parallax: first component of parallax for cancer', 16, 24, NULL, NULL),
(190, 2, 1, 1, 'parallax: second component of the parallax for cancer', 16, 24, NULL, NULL),
(191, 1, 1, 1, 'parallax: geographical latitude', NULL, 24, NULL, NULL),
(192, 3, 2, 1, 'syzygies: length of the mean synodic month', NULL, 19, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `parameter_group`
--

CREATE TABLE `parameter_group` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `parameter_group`
--

INSERT INTO `parameter_group` (`id`, `group_name`, `group_color`) VALUES
(1, NULL, 'color1'),
(2, NULL, 'color2'),
(3, NULL, 'color3'),
(4, NULL, 'color4'),
(5, NULL, 'color5'),
(6, NULL, 'color6'),
(7, NULL, 'color7'),
(8, NULL, 'color8'),
(9, NULL, 'color9'),
(10, NULL, 'color10'),
(11, NULL, 'color11'),
(12, NULL, 'color12'),
(13, NULL, 'color13'),
(14, NULL, 'color14'),
(15, NULL, 'color15'),
(16, NULL, 'color16');

-- --------------------------------------------------------

--
-- Structure de la table `parameter_set`
--

CREATE TABLE `parameter_set` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `table_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parameter_type`
--

CREATE TABLE `parameter_type` (
  `id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `parameter_type`
--

INSERT INTO `parameter_type` (`id`, `type`) VALUES
(2, 'explicit'),
(1, 'implicit'),
(3, 'implicit and explicit');

-- --------------------------------------------------------

--
-- Structure de la table `parameter_unit`
--

CREATE TABLE `parameter_unit` (
  `id` int(11) NOT NULL,
  `unit` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `parameter_unit`
--

INSERT INTO `parameter_unit` (`id`, `unit`) VALUES
(112, 'argument 1 = degree ; argument 2 = degree ; entry = day'),
(111, 'argument 1 = degree ; argument 2 = degree ; entry = degree'),
(12, 'argument 1 = degree ; entry = day'),
(11, 'argument 1 = degree ; entry = degree'),
(2, 'day'),
(1, 'degree'),
(3, 'degree/day'),
(4, 'degree/hour'),
(5, 'du'),
(6, 'du/day'),
(7, 'du/hour'),
(10, 'no unit');

-- --------------------------------------------------------

--
-- Structure de la table `parameter_value`
--

CREATE TABLE `parameter_value` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `parameter_format_id` int(11) DEFAULT NULL,
  `parameter_set_id` int(11) DEFAULT NULL,
  `value_is_int_sexa` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `type_of_number_id` int(11) NOT NULL,
  `value_float` double DEFAULT NULL,
  `value_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range1_initial_float` double DEFAULT NULL,
  `range1_initial_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range1_final_float` double DEFAULT NULL,
  `range1_final_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range2_initial_float` double DEFAULT NULL,
  `range2_initial_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range2_final_float` double DEFAULT NULL,
  `range2_final_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range3_initial_float` double DEFAULT NULL,
  `range3_initial_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range3_final_float` double DEFAULT NULL,
  `range3_final_original_base` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pdf_file`
--

CREATE TABLE `pdf_file` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `entity_definition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `pdf_file`
--

INSERT INTO `pdf_file` (`id`, `created_by_id`, `updated_by_id`, `file_name`, `file_user_name`, `file_original_name`, `file_size`, `created`, `updated`, `updated_at`, `entity_definition_id`) VALUES
(1, 11, 11, 'DISHAS_antonin-penon_solar-declination_2018-05-25.pdf', 'solar declination', 'DISHAS solar declination.pdf', 367107, '2018-05-25 15:32:21', '2018-05-25 16:35:56', '2018-05-25 16:35:56', 24),
(2, 11, 11, 'DISHAS_antonin-penon_Glossary--astronomical-parameter_2018-05-25.pdf', 'glossary: astronomical parameter', 'DISHAS_admin_glossary_parameter_parameter_set.pdf', 916985, '2018-05-25 16:13:49', '2018-05-25 16:13:49', '2018-05-25 16:13:49', NULL),
(3, 11, 11, 'DISHAS_antonin-penon_Shift-and-displacement_2018-05-25.pdf', 'shift and displacement', 'DISHAS_admin_shift_displacement.pdf', 486045, '2018-05-25 16:15:07', '2018-05-25 16:15:07', '2018-05-25 16:15:07', NULL),
(4, 2, 2, 'DISHAS_galla-topalian_Website-documentation-for-administrators_2018-05-25.pdf', 'website documentation for administrators', 'DISHAS_admin_user_documentation.pdf', 1114967, '2018-05-25 16:17:24', '2018-05-25 16:17:24', '2018-05-25 16:17:24', NULL),
(5, 11, 11, 'DISHAS_antonin-penon_tickè_2018-06-08.pdf', 'tickè', 'Electronic_ticket.pdf', 86499, '2018-06-08 14:35:16', '2018-06-08 14:35:16', '2018-06-08 14:35:16', NULL),
(6, 11, 11, 'DISHAS_antonin-penon_coucou_2018-06-08.pdf', 'coucou', 'Electronic_ticket.pdf', 86499, '2018-06-08 14:46:41', '2018-06-08 14:46:41', '2018-06-08 14:46:41', NULL),
(7, 11, 11, 'DISHAS_antonin-penon_patate_2018-06-08.pdf', 'patate', 'DISHAS solar declination.pdf', 367107, '2018-06-08 14:53:09', '2018-06-08 15:28:48', '2018-06-08 15:28:48', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `place`
--

CREATE TABLE `place` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `place_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `place_lat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `place_long` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `primary_source`
--

CREATE TABLE `primary_source` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `prim_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `shelfmark` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `digital_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prim_title` longtext COLLATE utf8_unicode_ci,
  `prim_editor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `library_id` int(11) DEFAULT NULL,
  `small_prim_title` longtext COLLATE utf8_unicode_ci,
  `date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `python_script`
--

CREATE TABLE `python_script` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `script_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `script_user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `script_original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `script_size` int(11) NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `script`
--

CREATE TABLE `script` (
  `id` int(11) NOT NULL,
  `iso_15924` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) DEFAULT NULL,
  `script_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `script`
--

INSERT INTO `script` (`id`, `iso_15924`, `number`, `script_name`) VALUES
(1, 'Adlm', 166, 'Adlam'),
(2, 'Afak', 439, 'Afaka'),
(3, 'Aghb', 239, 'Caucasian Albanian'),
(4, 'Ahom', 338, 'Ahom, Tai Ahom'),
(5, 'Arab', 160, 'Arabic'),
(6, 'Aran', 161, 'Arabic (Nastaliq variant)'),
(7, 'Armi', 124, 'Imperial Aramaic'),
(8, 'Armn', 230, 'Armenian'),
(9, 'Avst', 134, 'Avestan'),
(10, 'Bali', 360, 'Balinese'),
(11, 'Bamu', 435, 'Bamum'),
(12, 'Bass', 259, 'Bassa Vah'),
(13, 'Batk', 365, 'Batak'),
(14, 'Beng', 325, 'Bengali (Bangla)'),
(15, 'Bhks', 334, 'Bhaiksuki'),
(16, 'Blis', 550, 'Blissymbols'),
(17, 'Bopo', 285, 'Bopomofo'),
(18, 'Brah', 300, 'Brahmi'),
(19, 'Brai', 570, 'Braille'),
(20, 'Bugi', 367, 'Buginese'),
(21, 'Buhd', 372, 'Buhid'),
(22, 'Cakm', 349, 'Chakma'),
(23, 'Cans', 440, 'Unified Canadian Aboriginal Syllabics'),
(24, 'Cari', 201, 'Carian'),
(25, 'Cham', 358, 'Cham'),
(26, 'Cher', 445, 'Cherokee'),
(27, 'Cirt', 291, 'Cirth'),
(28, 'Copt', 204, 'Coptic'),
(29, 'Cprt', 403, 'Cypriot'),
(30, 'Cyrl', 220, 'Cyrillic'),
(31, 'Cyrs', 221, 'Cyrillic (Old Church Slavonic variant)'),
(32, 'Deva', 315, 'Devanagari (Nagari)'),
(33, 'Dogr', 328, 'Dogra'),
(34, 'Dsrt', 250, 'Deseret (Mormon)'),
(35, 'Dupl', 755, 'Duployan shorthand, Duployan stenography'),
(36, 'Egyd', 70, 'Egyptian demotic'),
(37, 'Egyh', 60, 'Egyptian hieratic'),
(38, 'Egyp', 50, 'Egyptian hieroglyphs'),
(39, 'Elba', 226, 'Elbasan'),
(40, 'Ethi', 430, 'Ethiopic (Geʻez)'),
(41, 'Geok', 241, 'Khutsuri (Asomtavruli and Nuskhuri)'),
(42, 'Geor', 240, 'Georgian (Mkhedruli and Mtavruli)'),
(43, 'Glag', 225, 'Glagolitic'),
(44, 'Gong', 312, 'Gunjala Gondi'),
(45, 'Gonm', 313, 'Masaram Gondi'),
(46, 'Goth', 206, 'Gothic'),
(47, 'Gran', 343, 'Grantha'),
(48, 'Grek', 200, 'Greek'),
(49, 'Gujr', 320, 'Gujarati'),
(50, 'Guru', 310, 'Gurmukhi'),
(51, 'Hanb', 503, 'Han with Bopomofo (alias for Han + Bopomofo)'),
(52, 'Hang', 286, 'Hangul (Hangŭl, Hangeul)'),
(53, 'Hani', 500, 'Han (Hanzi, Kanji, Hanja)'),
(54, 'Hano', 371, 'Hanunoo (Hanunóo)'),
(55, 'Hans', 501, 'Han (Simplified variant)'),
(56, 'Hant', 502, 'Han (Traditional variant)'),
(57, 'Hatr', 127, 'Hatran'),
(58, 'Hebr', 125, 'Hebrew'),
(59, 'Hira', 410, 'Hiragana'),
(60, 'Hluw', 80, 'Anatolian Hieroglyphs (Luwian Hieroglyphs, Hittite Hieroglyphs)'),
(61, 'Hmng', 450, 'Pahawh Hmong'),
(62, 'Hrkt', 412, 'Japanese syllabaries (alias for Hiragana + Katakana)'),
(63, 'Hung', 176, 'Old Hungarian (Hungarian Runic)'),
(64, 'Inds', 610, 'Indus (Harappan)'),
(65, 'Ital', 210, 'Old Italic (Etruscan, Oscan, etc.)'),
(66, 'Jamo', 284, 'Jamo (alias for Jamo subset of Hangul)'),
(67, 'Java', 361, 'Javanese'),
(68, 'Jpan', 413, 'Japanese (alias for Han + Hiragana + Katakana)'),
(69, 'Jurc', 510, 'Jurchen'),
(70, 'Kali', 357, 'Kayah Li'),
(71, 'Kana', 411, 'Katakana'),
(72, 'Khar', 305, 'Kharoshthi'),
(73, 'Khmr', 355, 'Khmer'),
(74, 'Khoj', 322, 'Khojki'),
(75, 'Kitl', 505, 'Khitan large script'),
(76, 'Kits', 288, 'Khitan small script'),
(77, 'Knda', 345, 'Kannada'),
(78, 'Kore', 287, 'Korean (alias for Hangul + Han)'),
(79, 'Kpel', 436, 'Kpelle'),
(80, 'Kthi', 317, 'Kaithi'),
(81, 'Lana', 351, 'Tai Tham (Lanna)'),
(82, 'Laoo', 356, 'Lao'),
(83, 'Latf', 217, 'Latin (Fraktur variant)'),
(84, 'Latg', 216, 'Latin (Gaelic variant)'),
(85, 'Latn', 215, 'Latin'),
(86, 'Leke', 364, 'Leke'),
(87, 'Lepc', 335, 'Lepcha (Róng)'),
(88, 'Limb', 336, 'Limbu'),
(89, 'Lina', 400, 'Linear A'),
(90, 'Linb', 401, 'Linear B'),
(91, 'Lisu', 399, 'Lisu (Fraser)'),
(92, 'Loma', 437, 'Loma'),
(93, 'Lyci', 202, 'Lycian'),
(94, 'Lydi', 116, 'Lydian'),
(95, 'Mahj', 314, 'Mahajani'),
(96, 'Maka', 366, 'Makasar'),
(97, 'Mand', 140, 'Mandaic, Mandaean'),
(98, 'Mani', 139, 'Manichaean'),
(99, 'Marc', 332, 'Marchen'),
(100, 'Maya', 90, 'Mayan hieroglyphs'),
(101, 'Medf', 265, 'Medefaidrin (Oberi Okaime, Oberi Ɔkaimɛ)'),
(102, 'Mend', 438, 'Mende Kikakui'),
(103, 'Merc', 101, 'Meroitic Cursive'),
(104, 'Mero', 100, 'Meroitic Hieroglyphs'),
(105, 'Mlym', 347, 'Malayalam'),
(106, 'Modi', 324, 'Modi, Moḍī'),
(107, 'Mong', 145, 'Mongolian'),
(108, 'Moon', 218, 'Moon (Moon code, Moon script, Moon type)'),
(109, 'Mroo', 264, 'Mro, Mru'),
(110, 'Mtei', 337, 'Meitei Mayek (Meithei, Meetei)'),
(111, 'Mult', 323, 'Multani'),
(112, 'Mymr', 350, 'Myanmar (Burmese)'),
(113, 'Narb', 106, 'Old North Arabian (Ancient North Arabian)'),
(114, 'Nbat', 159, 'Nabataean'),
(115, 'Newa', 333, 'Newa, Newar, Newari, Nepāla lipi'),
(116, 'Nkgb', 420, 'Nakhi Geba (\'Na-\'Khi ²Ggŏ-¹baw, Naxi Geba)'),
(117, 'Nkoo', 165, 'N’Ko'),
(118, 'Nshu', 499, 'Nüshu'),
(119, 'Ogam', 212, 'Ogham'),
(120, 'Olck', 261, 'Ol Chiki (Ol Cemet’, Ol, Santali)'),
(121, 'Orkh', 175, 'Old Turkic, Orkhon Runic'),
(122, 'Orya', 327, 'Oriya (Odia)'),
(123, 'Osge', 219, 'Osage'),
(124, 'Osma', 260, 'Osmanya'),
(125, 'Palm', 126, 'Palmyrene'),
(126, 'Pauc', 263, 'Pau Cin Hau'),
(127, 'Perm', 227, 'Old Permic'),
(128, 'Phag', 331, 'Phags-pa'),
(129, 'Phli', 131, 'Inscriptional Pahlavi'),
(130, 'Phlp', 132, 'Psalter Pahlavi'),
(131, 'Phlv', 133, 'Book Pahlavi'),
(132, 'Phnx', 115, 'Phoenician'),
(133, 'Piqd', 293, 'Klingon (KLI pIqaD)'),
(134, 'Plrd', 282, 'Miao (Pollard)'),
(135, 'Prti', 130, 'Inscriptional Parthian'),
(136, 'Qaaa', 900, 'Reserved for private use (start)'),
(137, 'Qabx', 949, 'Reserved for private use (end)'),
(138, 'Rjng', 363, 'Rejang (Redjang, Kaganga)'),
(139, 'Roro', 620, 'Rongorongo'),
(140, 'Runr', 211, 'Runic'),
(141, 'Samr', 123, 'Samaritan'),
(142, 'Sara', 292, 'Sarati'),
(143, 'Sarb', 105, 'Old South Arabian'),
(144, 'Saur', 344, 'Saurashtra'),
(145, 'Sgnw', 95, 'SignWriting'),
(146, 'Shaw', 281, 'Shavian (Shaw)'),
(147, 'Shrd', 319, 'Sharada, Śāradā'),
(148, 'Sidd', 302, 'Siddham, Siddhaṃ, Siddhamātṛkā'),
(149, 'Sind', 318, 'Khudawadi, Sindhi'),
(150, 'Sinh', 348, 'Sinhala'),
(151, 'Sora', 398, 'Sora Sompeng'),
(152, 'Soyo', 329, 'Soyombo'),
(153, 'Sund', 362, 'Sundanese'),
(154, 'Sylo', 316, 'Syloti Nagri'),
(155, 'Syrc', 135, 'Syriac'),
(156, 'Syre', 138, 'Syriac (Estrangelo variant)'),
(157, 'Syrj', 137, 'Syriac (Western variant)'),
(158, 'Syrn', 136, 'Syriac (Eastern variant)'),
(159, 'Tagb', 373, 'Tagbanwa'),
(160, 'Takr', 321, 'Takri, Ṭākrī, Ṭāṅkrī'),
(161, 'Tale', 353, 'Tai Le'),
(162, 'Talu', 354, 'New Tai Lue'),
(163, 'Taml', 346, 'Tamil'),
(164, 'Tang', 520, 'Tangut'),
(165, 'Tavt', 359, 'Tai Viet'),
(166, 'Telu', 340, 'Telugu'),
(167, 'Teng', 290, 'Tengwar'),
(168, 'Tfng', 120, 'Tifinagh (Berber)'),
(169, 'Tglg', 370, 'Tagalog (Baybayin, Alibata)'),
(170, 'Thaa', 170, 'Thaana'),
(171, 'Thai', 352, 'Thai'),
(172, 'Tibt', 330, 'Tibetan'),
(173, 'Tirh', 326, 'Tirhuta'),
(174, 'Ugar', 40, 'Ugaritic'),
(175, 'Vaii', 470, 'Vai'),
(176, 'Visp', 280, 'Visible Speech'),
(177, 'Wara', 262, 'Warang Citi (Varang Kshiti)'),
(178, 'Wole', 480, 'Woleai'),
(179, 'Xpeo', 30, 'Old Persian'),
(180, 'Xsux', 20, 'Cuneiform, Sumero-Akkadian'),
(181, 'Yiii', 460, 'Yi'),
(182, 'Zanb', 339, 'Zanabazar Square (Zanabazarin Dörböljin Useg, Xewtee Dörböljin Bicig, Horizontal Square Script)'),
(183, 'Zinh', 994, 'Code for inherited script'),
(184, 'Zmth', 995, 'Mathematical notation'),
(185, 'Zsye', 993, 'Symbols (Emoji variant)'),
(186, 'Zsym', 996, 'Symbols'),
(187, 'Zxxx', 997, 'Unwritten documents'),
(188, 'Zyyy', 998, 'Undetermined script'),
(189, 'Zzzz', 999, 'Uncoded script');

-- --------------------------------------------------------

--
-- Structure de la table `secondary_source`
--

CREATE TABLE `secondary_source` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `collective_book_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `sec_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sec_title` longtext COLLATE utf8_unicode_ci,
  `sec_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sec_pub_date` int(11) DEFAULT NULL,
  `sec_page_range` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sec_publisher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sec_pub_place` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sec_volume` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `sec_online_identifier` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `small_sec_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `secondary_source`
--

INSERT INTO `secondary_source` (`id`, `journal_id`, `collective_book_id`, `created_by_id`, `updated_by_id`, `sec_type`, `sec_title`, `sec_identifier`, `sec_pub_date`, `sec_page_range`, `sec_publisher`, `sec_pub_place`, `sec_volume`, `created`, `updated`, `sec_online_identifier`, `small_sec_title`) VALUES
(1, NULL, NULL, 2, 2, 'monography', 'blablabla', NULL, 2015, NULL, NULL, NULL, NULL, '2018-10-08 11:30:28', '2018-10-08 11:30:28', NULL, 'blablabla');

-- --------------------------------------------------------

--
-- Structure de la table `secondary_source_historian`
--

CREATE TABLE `secondary_source_historian` (
  `secondary_source_id` int(11) NOT NULL,
  `historian_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `secondary_source_historian`
--

INSERT INTO `secondary_source_historian` (`secondary_source_id`, `historian_id`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `table_content`
--

CREATE TABLE `table_content` (
  `id` int(11) NOT NULL,
  `entry_type_of_number_id` int(11) DEFAULT NULL,
  `argument1type_of_number_id` int(11) DEFAULT NULL,
  `argument2type_of_number_id` int(11) DEFAULT NULL,
  `argument3type_of_number_id` int(11) DEFAULT NULL,
  `table_type_id` int(11) DEFAULT NULL,
  `argument1_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `argument1_number_of_cell` int(11) DEFAULT NULL,
  `argument2_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `argument2_number_of_cell` int(11) DEFAULT NULL,
  `argument3_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `argument3_number_of_cell` int(11) DEFAULT NULL,
  `mathematical_parameter_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `entry_number_unit_id` int(11) DEFAULT NULL,
  `argument1number_unit_id` int(11) DEFAULT NULL,
  `argument2number_unit_id` int(11) DEFAULT NULL,
  `argument3number_unit_id` int(11) DEFAULT NULL,
  `entry_number_of_cell` int(11) DEFAULT NULL,
  `entry_significant_fractional_place` int(11) DEFAULT NULL,
  `argument1_significant_fractional_place` int(11) DEFAULT NULL,
  `argument2_significant_fractional_place` int(11) DEFAULT NULL,
  `argument3_significant_fractional_place` int(11) DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `public` tinyint(1) DEFAULT NULL,
  `edited_text_id` int(11) DEFAULT NULL,
  `argument1_number_of_steps` int(11) DEFAULT NULL,
  `argument2_number_of_steps` int(11) DEFAULT NULL,
  `argument3_number_of_steps` int(11) DEFAULT NULL,
  `arg_number` int(11) DEFAULT NULL,
  `value_original` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `value_float` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `has_difference_table` tinyint(1) DEFAULT NULL,
  `difference_value_original` json DEFAULT NULL COMMENT '(DC2Type:json_array)',
  `difference_value_float` json DEFAULT NULL COMMENT '(DC2Type:json_array)',
  `corrected_value_float` json DEFAULT NULL COMMENT '(DC2Type:json_array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `table_content`
--

INSERT INTO `table_content` (`id`, `entry_type_of_number_id`, `argument1type_of_number_id`, `argument2type_of_number_id`, `argument3type_of_number_id`, `table_type_id`, `argument1_name`, `argument1_number_of_cell`, `argument2_name`, `argument2_number_of_cell`, `argument3_name`, `argument3_number_of_cell`, `mathematical_parameter_id`, `created_by_id`, `updated_by_id`, `created`, `updated`, `entry_number_unit_id`, `argument1number_unit_id`, `argument2number_unit_id`, `argument3number_unit_id`, `entry_number_of_cell`, `entry_significant_fractional_place`, `argument1_significant_fractional_place`, `argument2_significant_fractional_place`, `argument3_significant_fractional_place`, `comment`, `public`, `edited_text_id`, `argument1_number_of_steps`, `argument2_number_of_steps`, `argument3_number_of_steps`, `arg_number`, `value_original`, `value_float`, `has_difference_table`, `difference_value_original`, `difference_value_float`, `corrected_value_float`) VALUES
(1, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, '2018-10-08 11:38:33', '2018-10-08 11:38:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, '2018-10-08 11:39:57', '2018-10-08 11:39:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, '2018-10-08 11:43:52', '2018-10-08 11:43:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, '2018-10-08 14:44:14', '2018-10-08 14:44:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `table_content_parameter_set`
--

CREATE TABLE `table_content_parameter_set` (
  `table_content_id` int(11) NOT NULL,
  `parameter_set_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `table_type`
--

CREATE TABLE `table_type` (
  `id` int(11) NOT NULL,
  `astronomical_object_id` int(11) NOT NULL,
  `table_type_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `table_parameter_output` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `accept_multiple_content` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `table_type`
--

INSERT INTO `table_type` (`id`, `astronomical_object_id`, `table_type_name`, `table_parameter_output`, `accept_multiple_content`) VALUES
(1, 1, 'equation of time', NULL, NULL),
(2, 1, 'mean motion solar tropical longitude', NULL, 1),
(3, 1, 'mean motion solar sideral longitude', NULL, 1),
(4, 1, 'equation of the sun', NULL, NULL),
(5, 11, 'chords', NULL, NULL),
(6, 11, 'sine', NULL, NULL),
(7, 11, 'shadow', NULL, NULL),
(8, 8, 'declination', NULL, NULL),
(9, 8, 'right ascension', NULL, NULL),
(10, 8, 'oblique ascension', NULL, NULL),
(11, 8, 'ascensional difference', NULL, NULL),
(12, 8, 'length of diurnal seasonal hour', NULL, NULL),
(13, 9, 'mean motion access and recess', NULL, 1),
(14, 9, 'mean motion apogees and stars', NULL, 1),
(17, 9, 'mean motion solar apogee', NULL, 1),
(18, 9, 'mean motion precession', NULL, 1),
(19, 10, 'syzygies', NULL, 1),
(20, 10, 'solar velocities', NULL, NULL),
(23, 10, 'lunar velocities', NULL, NULL),
(24, 10, 'parallax', NULL, NULL),
(25, 2, 'mean motion anomaly Mercury', NULL, 1),
(26, 2, 'equation Mercury center', NULL, NULL),
(27, 2, 'equation Mercury anomaly at mean distance', NULL, NULL),
(28, 2, 'total equation double-argument table Mercury', NULL, NULL),
(29, 2, 'Mercury latitude', NULL, NULL),
(30, 2, 'planetary Stations Mercury', NULL, NULL),
(31, 3, 'mean motion anomaly Venus', NULL, 1),
(32, 3, 'equation Venus center', NULL, NULL),
(33, 3, 'equation Venus anomaly at mean distance', NULL, NULL),
(34, 3, 'total equation double-argument table Venus', NULL, NULL),
(35, 3, 'Venus latitude', NULL, NULL),
(36, 3, 'planetary stations Venus', NULL, NULL),
(37, 4, 'mean motion lunar longitude', NULL, 1),
(38, 4, 'mean motion lunar anomaly', NULL, 1),
(42, 4, 'mean motion lunar node', NULL, 1),
(43, 4, 'mean motion longitude plus lunar node', NULL, 1),
(44, 4, 'mean motion lunar elongation', NULL, 1),
(45, 4, 'mean motion double elongation', NULL, 1),
(46, 4, 'equation Moon center', NULL, NULL),
(47, 4, 'equation Moon anomaly', NULL, NULL),
(48, 4, 'lunar latitude', NULL, NULL),
(49, 5, 'mean motion longitude Mars', NULL, 1),
(50, 5, 'equation Mars center', NULL, NULL),
(51, 5, 'equation Mars anomaly at mean distance', NULL, NULL),
(52, 5, 'total equation double-argument table Mars', NULL, NULL),
(53, 5, 'Mars latitude', NULL, NULL),
(54, 5, 'planetary stations Mars', NULL, NULL),
(55, 6, 'mean motion longitude Jupiter', NULL, 1),
(56, 6, 'equation Jupiter center', NULL, NULL),
(57, 6, 'equation Jupiter anomaly at mean distance', NULL, NULL),
(58, 6, 'total equation double-argument table Jupiter', NULL, NULL),
(59, 6, 'Jupiter latitude', NULL, NULL),
(60, 6, 'planetary stations Jupiter', NULL, NULL),
(61, 7, 'mean motion longitude Saturn', NULL, 1),
(62, 7, 'equation Saturn center', NULL, NULL),
(63, 7, 'equation Saturn anomaly at mean distance', NULL, NULL),
(64, 7, 'total equation double-argument table Saturn', NULL, NULL),
(65, 7, 'Saturn latitude', NULL, NULL),
(66, 7, 'planetary stations Saturn', NULL, NULL),
(67, 11, 'cosine', NULL, NULL),
(68, 6, 'equation Jupiter anomaly at maximum distance', NULL, NULL),
(69, 6, 'equation Jupiter anomaly at minimum distance', NULL, NULL),
(70, 5, 'equation Mars anomaly at maximum distance', NULL, NULL),
(71, 5, 'equation Mars anomaly at minimum distance', NULL, NULL),
(72, 2, 'equation Mercury anomaly at maximum distance', NULL, NULL),
(73, 2, 'equation Mercury anomaly at minimum distance', NULL, NULL),
(74, 7, 'equation Saturn anomaly at maximum distance', NULL, NULL),
(75, 7, 'equation Saturn anomaly at minimum distance', NULL, NULL),
(76, 3, 'equation Venus anomaly at maximum distance', NULL, NULL),
(77, 3, 'equation Venus anomaly at minimum distance', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `type_of_number`
--

CREATE TABLE `type_of_number` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_defintion` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `base_JSON` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `code_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `integer_separator_JSON` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `type_of_number`
--

INSERT INTO `type_of_number` (`id`, `type_name`, `type_defintion`, `base_JSON`, `code_name`, `integer_separator_JSON`) VALUES
(1, 'sexagesimal', NULL, '[[60],[60]]', 'sexagesimal', NULL),
(2, 'floating sexagesimal', 'floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next', '[[60],[60]]', 'floating_sexagesimal', NULL),
(3, 'historical', 'revolution and sign', '[[30,12,10],[60]]', 'historical', '[\"s \",\"r \"]'),
(4, 'integer and sexagesimal', 'integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system', '[[10],[60]]', 'integer_and_sexagesimal', NULL),
(5, 'historical decimal', NULL, '[[10],[100]]', 'historical_decimal', NULL),
(6, 'temporal', 'duration of time converted into days + sexagesimal. Ex : 7d6;30,05', '[[10],[24,60]]', 'temporal', NULL),
(7, 'decimal', 'contemporean decimal format', '[[10],[10]]', 'decimal', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_interface_text`
--

CREATE TABLE `user_interface_text` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `textName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `textContent` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `user_interface_text`
--

INSERT INTO `user_interface_text` (`id`, `created_by_id`, `updated_by_id`, `textName`, `textContent`, `created`, `updated`) VALUES
(1, NULL, 2, 'public_about', '<p>Digital humanities transform the availability of historical sources gradually, along with the means to analyse, edit, and relate them. These changes should be addressed, anticipated, and fostered by various research communities in history. DISHAS (Digital Information System for the History of Astral Sciences) is a collective enterprise supported by the CHAMA (Commission for the History of Ancient and Medieval Astronomy) that addresses changes in the field of the history of astral sciences. DISHAS relies on a network of international projects in Chinese, Sanskrit, Arabic, Latin and Hebrew sources.In the long run, it aims, in collaboration with partner projects, at providing tools to the community to edit and analyse the different types of sources usually treated in the history of astral sciences, namely, scientific instruments, prose and versified texts, iconography and technical/geometrical diagrams, and astronomical tables. As a pilot attempt, DISHAS focuses on astronomical tables.</p>\n<p>For centuries across Eurasia, astronomical tables were constructed, compiled and copied to meet a wide range of religious, ritualistic and political needs, to make calendars, to predict the future astrologically, to understand the natural world. Such tables circulated among cultures and were appropriated and transformed by a great diversity of actors. Thus, the numerical data conveyed in these tables provide rich evidence for ancient scientific practices. For example, in tables we can recover how complex, massive numerical computations were handled in the ancient world and thus contribute to the history of computational mathematics. Tables also reveal how astral phenomena were modelled and how reasoning and prediction were shaped. As written documents of a special type, falling between computation per se and data storage, astronomical tables expose epistemic writing practices in their layouts on the page and their combination into &ldquo;sets&rdquo; of tables. Created by complex computation with often-interlinked algorithms, circulating tables generally were adapted to new contexts and purposes rather than recomputed from scratch. Thus in addition to their individual contents, astronomical tables viewed more generally can provide unmatched sources for studying the transmission of computational know-how, writing technologies and layouts, theoretical models, and numerical parameters. With enhanced digital editorial and analytical tools, scholars will be able to chart previously unrecognized paths of circulation, to learn how large collections of tables were shaped, and to track the spread and appropriation of particular computational practices.In the study of astronomical tables DISHAS has two main objectives which are best described by the following research questions:</p>\n<ul style=\"text-align: justify;\">\n<li>What type of database should we design in order to &ldquo;edit&rdquo; sources of the ancient astral sciences in the context of the digital humanities? How can a single tool handle diverse tabular layouts, different types of numbers, different kinds of errors and variants between copies, as well as the differences in publishing results in paper and digital formats?</li>\n<li>What kinds of tools should we create to explore materials within this database and to analyse these sources? How can we employ modern computational power with the necessary attention to the historical computation practices of the actors? How can we describe the algorithms presented in texts and their eventual effective uses in computing tables?</li>\n</ul>\n<p>These two groups of questions are obviously closely related and need to be treated together in order to guarantee the compatibility of the analytical tools with the database on which they should operate. For the first group of questions we intend to produce innovative table- and parameter-databases and to develop new ways to publish our research. For the second set of questions we will create shared, historically based computation routines allowing us to explore in new ways the numerical content of astronomical tables.In order to address these challenges DISHAS made general choices which are briefly describe here. The first important choice is deciding the fundamental unit of the database. Individual tables as they appears in a given primary sources (i.e., Original item) are selected as the fundamental unit. This level of granularity is central to make DISHAS an efficient tool for mathematical analysis and critical editing. The fact that original items rather than works are the fundamental unit of the database allows to analyse the different attestations of a given work. For instance one can identify a core set of tables around which various satellites tables, diagrams or texts are more or less present depending on the primary source. The geographical and chronological evolution of these dynamical sets of original items can be analysed. This possibilities are essential when it comes to produce critical edition of works. The choice of original item as the fundamental unit also allows to analyse the intellectual composition of a given manuscript in details. This is also an important step in understanding how manuscript shape intellectual traditions.In the history of astral sciences, various actors have used different units and numbers to express space and time relationships, and also compute and shape their tables. Some units are directly related to celestial objects, others to measuring instruments; some units are mathematical and abstract. Similarly, some numbers are fully or partially sexagesimal, others rely on floating numbers, etc. As a consequence of this, modern units and numbers are not always suitable to record historical astronomical tables. In DISHAS, historically attested units and numbers types are used. This allows us to develop mathematical tools for computation and analysis that are faithful to the historical actors&rsquo; practices.</p>', '2017-11-14 00:00:00', '2017-11-22 08:42:15'),
(2, NULL, NULL, 'public_team', NULL, '2017-11-14 00:00:00', '2017-11-14 00:00:00'),
(5, NULL, 2, 'public_contact', '<h1 id=\"mcetoc_1c01b80jk0\">Scientific editors</h1>\r\n<ul style=\"list-style-type: square;\">\r\n<li>\r\n<h4>Matthieu Husson <small>matthieu.husson[at]obspm.fr</small></h4>\r\n</li>\r\n<li>\r\n<h4>Clemency Montelle&nbsp;<small>clemency.montelle[at]canterbury.ac.nz</small></h4>\r\n</li>\r\n<li>\r\n<h4>Benno van Dalen&nbsp;<small>bvdalen[at]ptolemaeus.badw.de</small></h4>\r\n</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<h1 id=\"mcetoc_1c01b92051\">Technical team</h1>\r\n<ul style=\"list-style-type: square;\">\r\n<li>\r\n<h4>Galla Topalian</h4>\r\n</li>\r\n<li>\r\n<h4>Olivier Becker</h4>\r\n</li>\r\n<li>\r\n<h4>Antonin Penon</h4>\r\n</li>\r\n</ul>\r\n<p>&nbsp;</p>', '2017-11-14 00:00:00', '2017-11-28 13:51:23'),
(6, NULL, NULL, 'public_partners', NULL, '2017-11-14 00:00:00', '2017-11-14 00:00:00'),
(7, NULL, 2, 'private_annuncement', NULL, '2017-11-14 00:00:00', '2017-12-04 16:00:39'),
(9, NULL, NULL, 'private_admin_user_documentation', NULL, '2017-11-17 00:00:00', '2017-11-17 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `work`
--

CREATE TABLE `work` (
  `id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `incipit` longtext COLLATE utf8_unicode_ci,
  `taq` int(11) DEFAULT NULL,
  `tpq` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `translator_id` int(11) DEFAULT NULL,
  `small_incipit` longtext COLLATE utf8_unicode_ci,
  `title` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `work_historical_actor`
--

CREATE TABLE `work_historical_actor` (
  `work_id` int(11) NOT NULL,
  `historical_actor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `xml_file`
--

CREATE TABLE `xml_file` (
  `id` int(11) NOT NULL,
  `entity_definition_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `xml_file`
--

INSERT INTO `xml_file` (`id`, `entity_definition_id`, `created_by_id`, `updated_by_id`, `file_name`, `file_user_name`, `file_original_name`, `file_size`, `created`, `updated`, `updated_at`) VALUES
(1, NULL, 2, 2, 'DISHAS_bnf_lat_7281_2018-10-02.xml', 'bnf_lat_7281', 'DISHAS_bnf_lat_7281_2018-09-27.xml', 134202, '2018-10-02 09:27:08', '2018-10-02 09:27:08', '2018-10-02 09:27:08');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `alfa_author`
--
ALTER TABLE `alfa_author`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_DBCEA4D4674D812` (`canonical_name`);

--
-- Index pour la table `alfa_authority`
--
ALTER TABLE `alfa_authority`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `alfa_library`
--
ALTER TABLE `alfa_library`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `alfa_primary_source`
--
ALTER TABLE `alfa_primary_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_727B07EAFE2541D7` (`library_id`);

--
-- Index pour la table `alfa_primary_source_work`
--
ALTER TABLE `alfa_primary_source_work`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B4756399BB3453DB` (`work_id`),
  ADD KEY `IDX_B47563995FA7A186` (`primary_source_id`),
  ADD KEY `IDX_B475639981EC865B` (`authority_id`);

--
-- Index pour la table `alfa_work`
--
ALTER TABLE `alfa_work`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C9D27E1B108734B1` (`work_type_id`),
  ADD KEY `IDX_C9D27E1BF675F31B` (`author_id`);

--
-- Index pour la table `alfa_work_type`
--
ALTER TABLE `alfa_work_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `astronomical_object`
--
ALTER TABLE `astronomical_object`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D59B7F71C3364215` (`object_name`);

--
-- Index pour la table `definition`
--
ALTER TABLE `definition`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `edited_text`
--
ALTER TABLE `edited_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A0B589C6A185918` (`secondary_source_id`),
  ADD KEY `IDX_A0B589C6754F370E` (`historian_id`),
  ADD KEY `IDX_A0B589C6B03A8386` (`created_by_id`),
  ADD KEY `IDX_A0B589C6896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_A0B589C6C77FCD9C` (`table_type_id`);

--
-- Index pour la table `edited_text_edited_text`
--
ALTER TABLE `edited_text_edited_text`
  ADD PRIMARY KEY (`edited_text_source`,`edited_text_target`),
  ADD KEY `IDX_C1468F43A587E930` (`edited_text_source`),
  ADD KEY `IDX_C1468F43BC62B9BF` (`edited_text_target`);

--
-- Index pour la table `edited_text_original_text`
--
ALTER TABLE `edited_text_original_text`
  ADD PRIMARY KEY (`edited_text_id`,`original_text_id`),
  ADD KEY `IDX_9E696439A2294B63` (`edited_text_id`),
  ADD KEY `IDX_9E696439EF0D9BBF` (`original_text_id`);

--
-- Index pour la table `formula_definition`
--
ALTER TABLE `formula_definition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_F4B38E703DA5256D` (`image_id`),
  ADD KEY `IDX_F4B38E70B03A8386` (`created_by_id`),
  ADD KEY `IDX_F4B38E70896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_F4B38E70C77FCD9C` (`table_type_id`);

--
-- Index pour la table `fos_user`
--
ALTER TABLE `fos_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479C05FB297` (`confirmation_token`);

--
-- Index pour la table `historian`
--
ALTER TABLE `historian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C89B677EB03A8386` (`created_by_id`),
  ADD KEY `IDX_C89B677E896DBBDE` (`updated_by_id`);

--
-- Index pour la table `historical_actor`
--
ALTER TABLE `historical_actor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E5AA9C7FB03A8386` (`created_by_id`),
  ADD KEY `IDX_E5AA9C7F896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_E5AA9C7FDA6A219` (`place_id`);

--
-- Index pour la table `image_file`
--
ALTER TABLE `image_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7EA5DC8EB03A8386` (`created_by_id`),
  ADD KEY `IDX_7EA5DC8E896DBBDE` (`updated_by_id`);

--
-- Index pour la table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C1A7E74DB03A8386` (`created_by_id`),
  ADD KEY `IDX_C1A7E74D896DBBDE` (`updated_by_id`);

--
-- Index pour la table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A18098BCB03A8386` (`created_by_id`),
  ADD KEY `IDX_A18098BC896DBBDE` (`updated_by_id`);

--
-- Index pour la table `mathematical_parameter`
--
ALTER TABLE `mathematical_parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_82AA4923B03A8386` (`created_by_id`),
  ADD KEY `IDX_82AA4923896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_82AA49238086C413` (`type_of_number_id`);

--
-- Index pour la table `number_unit`
--
ALTER TABLE `number_unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9E7FC7E6DCBB0C53` (`unit`);

--
-- Index pour la table `original_text`
--
ALTER TABLE `original_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_31FECCF1B03A8386` (`created_by_id`),
  ADD KEY `IDX_31FECCF1896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_31FECCF1C77FCD9C` (`table_type_id`),
  ADD KEY `IDX_31FECCF1DA6A219` (`place_id`),
  ADD KEY `IDX_31FECCF15FA7A186` (`primary_source_id`),
  ADD KEY `IDX_31FECCF1BB3453DB` (`work_id`),
  ADD KEY `IDX_31FECCF1D21B6558` (`historical_actor_id`),
  ADD KEY `IDX_31FECCF182F1BAF4` (`language_id`),
  ADD KEY `IDX_31FECCF1A1C01850` (`script_id`);

--
-- Index pour la table `parameter_definition`
--
ALTER TABLE `parameter_definition`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `parameter_feature`
--
ALTER TABLE `parameter_feature`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D589C9E61FD77566` (`feature`);

--
-- Index pour la table `parameter_format`
--
ALTER TABLE `parameter_format`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3AC4782EF123013` (`parameter_type_id`),
  ADD KEY `IDX_3AC4782E32E3CC8D` (`parameter_unit_id`),
  ADD KEY `IDX_3AC4782E45723E74` (`parameter_feature_id`),
  ADD KEY `IDX_3AC4782E132604DB` (`parameter_group_id`),
  ADD KEY `IDX_3AC4782EC77FCD9C` (`table_type_id`),
  ADD KEY `IDX_3AC4782EAD7333DB` (`parameter_definition_id`),
  ADD KEY `IDX_3AC4782EC5D8FA67` (`formula_definition_id`);

--
-- Index pour la table `parameter_group`
--
ALTER TABLE `parameter_group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `parameter_set`
--
ALTER TABLE `parameter_set`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E20C6C75B03A8386` (`created_by_id`),
  ADD KEY `IDX_E20C6C75896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_E20C6C75C77FCD9C` (`table_type_id`);

--
-- Index pour la table `parameter_type`
--
ALTER TABLE `parameter_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_23D0542C8CDE5729` (`type`);

--
-- Index pour la table `parameter_unit`
--
ALTER TABLE `parameter_unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_73B50F56DCBB0C53` (`unit`);

--
-- Index pour la table `parameter_value`
--
ALTER TABLE `parameter_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6DB2A2B8B03A8386` (`created_by_id`),
  ADD KEY `IDX_6DB2A2B8896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_6DB2A2B82F7D5BB7` (`parameter_format_id`),
  ADD KEY `IDX_6DB2A2B8F48507E9` (`parameter_set_id`),
  ADD KEY `IDX_6DB2A2B88086C413` (`type_of_number_id`);

--
-- Index pour la table `pdf_file`
--
ALTER TABLE `pdf_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7FBE7B5EB03A8386` (`created_by_id`),
  ADD KEY `IDX_7FBE7B5E896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_7FBE7B5EF0B5051D` (`entity_definition_id`);

--
-- Index pour la table `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_741D53CDB03A8386` (`created_by_id`),
  ADD KEY `IDX_741D53CD896DBBDE` (`updated_by_id`);

--
-- Index pour la table `primary_source`
--
ALTER TABLE `primary_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A77DC7C3B03A8386` (`created_by_id`),
  ADD KEY `IDX_A77DC7C3896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_A77DC7C3FE2541D7` (`library_id`);

--
-- Index pour la table `python_script`
--
ALTER TABLE `python_script`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BECDA7ADB03A8386` (`created_by_id`),
  ADD KEY `IDX_BECDA7AD896DBBDE` (`updated_by_id`);

--
-- Index pour la table `script`
--
ALTER TABLE `script`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `secondary_source`
--
ALTER TABLE `secondary_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FB4701CB478E8802` (`journal_id`),
  ADD KEY `IDX_FB4701CB1B60F5AF` (`collective_book_id`),
  ADD KEY `IDX_FB4701CBB03A8386` (`created_by_id`),
  ADD KEY `IDX_FB4701CB896DBBDE` (`updated_by_id`);

--
-- Index pour la table `secondary_source_historian`
--
ALTER TABLE `secondary_source_historian`
  ADD PRIMARY KEY (`secondary_source_id`,`historian_id`),
  ADD KEY `IDX_570282A1A185918` (`secondary_source_id`),
  ADD KEY `IDX_570282A1754F370E` (`historian_id`);

--
-- Index pour la table `table_content`
--
ALTER TABLE `table_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FCF671A698790F3B` (`argument1type_of_number_id`),
  ADD KEY `IDX_FCF671A672FFD259` (`argument2type_of_number_id`),
  ADD KEY `IDX_FCF671A69DAD64B8` (`argument3type_of_number_id`),
  ADD KEY `IDX_FCF671A6C77FCD9C` (`table_type_id`),
  ADD KEY `IDX_FCF671A64BF283B1` (`mathematical_parameter_id`),
  ADD KEY `IDX_FCF671A6B03A8386` (`created_by_id`),
  ADD KEY `IDX_FCF671A6896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_FCF671A6D54E65B0` (`entry_type_of_number_id`),
  ADD KEY `IDX_FCF671A67A8DE8` (`entry_number_unit_id`),
  ADD KEY `IDX_FCF671A688C4387F` (`argument1number_unit_id`),
  ADD KEY `IDX_FCF671A699B95206` (`argument2number_unit_id`),
  ADD KEY `IDX_FCF671A6204289EE` (`argument3number_unit_id`),
  ADD KEY `IDX_FCF671A6A2294B63` (`edited_text_id`);

--
-- Index pour la table `table_content_parameter_set`
--
ALTER TABLE `table_content_parameter_set`
  ADD PRIMARY KEY (`table_content_id`,`parameter_set_id`),
  ADD KEY `IDX_B482EB89D94EFB80` (`table_content_id`),
  ADD KEY `IDX_B482EB89F48507E9` (`parameter_set_id`);

--
-- Index pour la table `table_type`
--
ALTER TABLE `table_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C60817E2FC08E877` (`astronomical_object_id`);

--
-- Index pour la table `type_of_number`
--
ALTER TABLE `type_of_number`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_interface_text`
--
ALTER TABLE `user_interface_text`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_E9006516A0C0E4C6` (`textName`),
  ADD KEY `IDX_E9006516B03A8386` (`created_by_id`),
  ADD KEY `IDX_E9006516896DBBDE` (`updated_by_id`);

--
-- Index pour la table `work`
--
ALTER TABLE `work`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_534E6880B03A8386` (`created_by_id`),
  ADD KEY `IDX_534E6880896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_534E6880DA6A219` (`place_id`),
  ADD KEY `IDX_534E68805370E40B` (`translator_id`);

--
-- Index pour la table `work_historical_actor`
--
ALTER TABLE `work_historical_actor`
  ADD PRIMARY KEY (`work_id`,`historical_actor_id`),
  ADD KEY `IDX_C057BDA5BB3453DB` (`work_id`),
  ADD KEY `IDX_C057BDA5D21B6558` (`historical_actor_id`);

--
-- Index pour la table `xml_file`
--
ALTER TABLE `xml_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9858065EF0B5051D` (`entity_definition_id`),
  ADD KEY `IDX_9858065EB03A8386` (`created_by_id`),
  ADD KEY `IDX_9858065E896DBBDE` (`updated_by_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `alfa_author`
--
ALTER TABLE `alfa_author`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT pour la table `alfa_authority`
--
ALTER TABLE `alfa_authority`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pour la table `alfa_library`
--
ALTER TABLE `alfa_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT pour la table `alfa_primary_source`
--
ALTER TABLE `alfa_primary_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;
--
-- AUTO_INCREMENT pour la table `alfa_primary_source_work`
--
ALTER TABLE `alfa_primary_source_work`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT pour la table `alfa_work`
--
ALTER TABLE `alfa_work`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
--
-- AUTO_INCREMENT pour la table `alfa_work_type`
--
ALTER TABLE `alfa_work_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `astronomical_object`
--
ALTER TABLE `astronomical_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `definition`
--
ALTER TABLE `definition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT pour la table `edited_text`
--
ALTER TABLE `edited_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT pour la table `formula_definition`
--
ALTER TABLE `formula_definition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `fos_user`
--
ALTER TABLE `fos_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `historian`
--
ALTER TABLE `historian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `historical_actor`
--
ALTER TABLE `historical_actor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `image_file`
--
ALTER TABLE `image_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `journal`
--
ALTER TABLE `journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=507;
--
-- AUTO_INCREMENT pour la table `library`
--
ALTER TABLE `library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mathematical_parameter`
--
ALTER TABLE `mathematical_parameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `number_unit`
--
ALTER TABLE `number_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `original_text`
--
ALTER TABLE `original_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `parameter_definition`
--
ALTER TABLE `parameter_definition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `parameter_feature`
--
ALTER TABLE `parameter_feature`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `parameter_format`
--
ALTER TABLE `parameter_format`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;
--
-- AUTO_INCREMENT pour la table `parameter_group`
--
ALTER TABLE `parameter_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pour la table `parameter_set`
--
ALTER TABLE `parameter_set`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `parameter_type`
--
ALTER TABLE `parameter_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `parameter_unit`
--
ALTER TABLE `parameter_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;
--
-- AUTO_INCREMENT pour la table `parameter_value`
--
ALTER TABLE `parameter_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `pdf_file`
--
ALTER TABLE `pdf_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `place`
--
ALTER TABLE `place`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `primary_source`
--
ALTER TABLE `primary_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `python_script`
--
ALTER TABLE `python_script`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `script`
--
ALTER TABLE `script`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;
--
-- AUTO_INCREMENT pour la table `secondary_source`
--
ALTER TABLE `secondary_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `table_content`
--
ALTER TABLE `table_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pour la table `table_type`
--
ALTER TABLE `table_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
--
-- AUTO_INCREMENT pour la table `type_of_number`
--
ALTER TABLE `type_of_number`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `user_interface_text`
--
ALTER TABLE `user_interface_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `work`
--
ALTER TABLE `work`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `xml_file`
--
ALTER TABLE `xml_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `alfa_primary_source`
--
ALTER TABLE `alfa_primary_source`
  ADD CONSTRAINT `FK_727B07EAFE2541D7` FOREIGN KEY (`library_id`) REFERENCES `alfa_library` (`id`);

--
-- Contraintes pour la table `alfa_primary_source_work`
--
ALTER TABLE `alfa_primary_source_work`
  ADD CONSTRAINT `FK_B47563995FA7A186` FOREIGN KEY (`primary_source_id`) REFERENCES `alfa_primary_source` (`id`),
  ADD CONSTRAINT `FK_B475639981EC865B` FOREIGN KEY (`authority_id`) REFERENCES `alfa_authority` (`id`),
  ADD CONSTRAINT `FK_B4756399BB3453DB` FOREIGN KEY (`work_id`) REFERENCES `alfa_work` (`id`);

--
-- Contraintes pour la table `alfa_work`
--
ALTER TABLE `alfa_work`
  ADD CONSTRAINT `FK_C9D27E1B108734B1` FOREIGN KEY (`work_type_id`) REFERENCES `alfa_work_type` (`id`),
  ADD CONSTRAINT `FK_C9D27E1BF675F31B` FOREIGN KEY (`author_id`) REFERENCES `alfa_author` (`id`);

--
-- Contraintes pour la table `edited_text`
--
ALTER TABLE `edited_text`
  ADD CONSTRAINT `FK_A0B589C6754F370E` FOREIGN KEY (`historian_id`) REFERENCES `historian` (`id`),
  ADD CONSTRAINT `FK_A0B589C6896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_A0B589C6A185918` FOREIGN KEY (`secondary_source_id`) REFERENCES `secondary_source` (`id`),
  ADD CONSTRAINT `FK_A0B589C6B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_A0B589C6C77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`);

--
-- Contraintes pour la table `edited_text_edited_text`
--
ALTER TABLE `edited_text_edited_text`
  ADD CONSTRAINT `FK_C1468F43A587E930` FOREIGN KEY (`edited_text_source`) REFERENCES `edited_text` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_C1468F43BC62B9BF` FOREIGN KEY (`edited_text_target`) REFERENCES `edited_text` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `edited_text_original_text`
--
ALTER TABLE `edited_text_original_text`
  ADD CONSTRAINT `FK_9E696439A2294B63` FOREIGN KEY (`edited_text_id`) REFERENCES `edited_text` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9E696439EF0D9BBF` FOREIGN KEY (`original_text_id`) REFERENCES `original_text` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `formula_definition`
--
ALTER TABLE `formula_definition`
  ADD CONSTRAINT `FK_F4B38E703DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image_file` (`id`),
  ADD CONSTRAINT `FK_F4B38E70896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_F4B38E70B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_F4B38E70C77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`);

--
-- Contraintes pour la table `historian`
--
ALTER TABLE `historian`
  ADD CONSTRAINT `FK_C89B677E896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_C89B677EB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `historical_actor`
--
ALTER TABLE `historical_actor`
  ADD CONSTRAINT `FK_E5AA9C7F896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_E5AA9C7FB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_E5AA9C7FDA6A219` FOREIGN KEY (`place_id`) REFERENCES `place` (`id`);

--
-- Contraintes pour la table `image_file`
--
ALTER TABLE `image_file`
  ADD CONSTRAINT `FK_7EA5DC8E896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_7EA5DC8EB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `FK_C1A7E74D896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_C1A7E74DB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `library`
--
ALTER TABLE `library`
  ADD CONSTRAINT `FK_A18098BC896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_A18098BCB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `mathematical_parameter`
--
ALTER TABLE `mathematical_parameter`
  ADD CONSTRAINT `FK_82AA49238086C413` FOREIGN KEY (`type_of_number_id`) REFERENCES `type_of_number` (`id`),
  ADD CONSTRAINT `FK_82AA4923896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_82AA4923B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `original_text`
--
ALTER TABLE `original_text`
  ADD CONSTRAINT `FK_31FECCF15FA7A186` FOREIGN KEY (`primary_source_id`) REFERENCES `primary_source` (`id`),
  ADD CONSTRAINT `FK_31FECCF182F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`),
  ADD CONSTRAINT `FK_31FECCF1896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_31FECCF1A1C01850` FOREIGN KEY (`script_id`) REFERENCES `script` (`id`),
  ADD CONSTRAINT `FK_31FECCF1B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_31FECCF1BB3453DB` FOREIGN KEY (`work_id`) REFERENCES `work` (`id`),
  ADD CONSTRAINT `FK_31FECCF1C77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`),
  ADD CONSTRAINT `FK_31FECCF1D21B6558` FOREIGN KEY (`historical_actor_id`) REFERENCES `historical_actor` (`id`),
  ADD CONSTRAINT `FK_31FECCF1DA6A219` FOREIGN KEY (`place_id`) REFERENCES `place` (`id`);

--
-- Contraintes pour la table `parameter_format`
--
ALTER TABLE `parameter_format`
  ADD CONSTRAINT `FK_3AC4782E132604DB` FOREIGN KEY (`parameter_group_id`) REFERENCES `parameter_group` (`id`),
  ADD CONSTRAINT `FK_3AC4782E32E3CC8D` FOREIGN KEY (`parameter_unit_id`) REFERENCES `parameter_unit` (`id`),
  ADD CONSTRAINT `FK_3AC4782E45723E74` FOREIGN KEY (`parameter_feature_id`) REFERENCES `parameter_feature` (`id`),
  ADD CONSTRAINT `FK_3AC4782EAD7333DB` FOREIGN KEY (`parameter_definition_id`) REFERENCES `parameter_definition` (`id`),
  ADD CONSTRAINT `FK_3AC4782EC5D8FA67` FOREIGN KEY (`formula_definition_id`) REFERENCES `formula_definition` (`id`),
  ADD CONSTRAINT `FK_3AC4782EC77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`),
  ADD CONSTRAINT `FK_3AC4782EF123013` FOREIGN KEY (`parameter_type_id`) REFERENCES `parameter_type` (`id`);

--
-- Contraintes pour la table `parameter_set`
--
ALTER TABLE `parameter_set`
  ADD CONSTRAINT `FK_E20C6C75896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_E20C6C75B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_E20C6C75C77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`);

--
-- Contraintes pour la table `parameter_value`
--
ALTER TABLE `parameter_value`
  ADD CONSTRAINT `FK_6DB2A2B82F7D5BB7` FOREIGN KEY (`parameter_format_id`) REFERENCES `parameter_format` (`id`),
  ADD CONSTRAINT `FK_6DB2A2B88086C413` FOREIGN KEY (`type_of_number_id`) REFERENCES `type_of_number` (`id`),
  ADD CONSTRAINT `FK_6DB2A2B8896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_6DB2A2B8B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_6DB2A2B8F48507E9` FOREIGN KEY (`parameter_set_id`) REFERENCES `parameter_set` (`id`);

--
-- Contraintes pour la table `pdf_file`
--
ALTER TABLE `pdf_file`
  ADD CONSTRAINT `FK_7FBE7B5E896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_7FBE7B5EB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_7FBE7B5EF0B5051D` FOREIGN KEY (`entity_definition_id`) REFERENCES `definition` (`id`);

--
-- Contraintes pour la table `place`
--
ALTER TABLE `place`
  ADD CONSTRAINT `FK_741D53CD896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_741D53CDB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `primary_source`
--
ALTER TABLE `primary_source`
  ADD CONSTRAINT `FK_A77DC7C3896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_A77DC7C3B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_A77DC7C3FE2541D7` FOREIGN KEY (`library_id`) REFERENCES `library` (`id`);

--
-- Contraintes pour la table `python_script`
--
ALTER TABLE `python_script`
  ADD CONSTRAINT `FK_BECDA7AD896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_BECDA7ADB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `secondary_source`
--
ALTER TABLE `secondary_source`
  ADD CONSTRAINT `FK_FB4701CB1B60F5AF` FOREIGN KEY (`collective_book_id`) REFERENCES `secondary_source` (`id`),
  ADD CONSTRAINT `FK_FB4701CB478E8802` FOREIGN KEY (`journal_id`) REFERENCES `journal` (`id`),
  ADD CONSTRAINT `FK_FB4701CB896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_FB4701CBB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `secondary_source_historian`
--
ALTER TABLE `secondary_source_historian`
  ADD CONSTRAINT `FK_570282A1754F370E` FOREIGN KEY (`historian_id`) REFERENCES `historian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_570282A1A185918` FOREIGN KEY (`secondary_source_id`) REFERENCES `secondary_source` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_content`
--
ALTER TABLE `table_content`
  ADD CONSTRAINT `FK_FCF671A6204289EE` FOREIGN KEY (`argument3number_unit_id`) REFERENCES `number_unit` (`id`),
  ADD CONSTRAINT `FK_FCF671A64BF283B1` FOREIGN KEY (`mathematical_parameter_id`) REFERENCES `mathematical_parameter` (`id`),
  ADD CONSTRAINT `FK_FCF671A672FFD259` FOREIGN KEY (`argument2type_of_number_id`) REFERENCES `type_of_number` (`id`),
  ADD CONSTRAINT `FK_FCF671A67A8DE8` FOREIGN KEY (`entry_number_unit_id`) REFERENCES `number_unit` (`id`),
  ADD CONSTRAINT `FK_FCF671A688C4387F` FOREIGN KEY (`argument1number_unit_id`) REFERENCES `number_unit` (`id`),
  ADD CONSTRAINT `FK_FCF671A6896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_FCF671A698790F3B` FOREIGN KEY (`argument1type_of_number_id`) REFERENCES `type_of_number` (`id`),
  ADD CONSTRAINT `FK_FCF671A699B95206` FOREIGN KEY (`argument2number_unit_id`) REFERENCES `number_unit` (`id`),
  ADD CONSTRAINT `FK_FCF671A69DAD64B8` FOREIGN KEY (`argument3type_of_number_id`) REFERENCES `type_of_number` (`id`),
  ADD CONSTRAINT `FK_FCF671A6A2294B63` FOREIGN KEY (`edited_text_id`) REFERENCES `edited_text` (`id`),
  ADD CONSTRAINT `FK_FCF671A6B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_FCF671A6C77FCD9C` FOREIGN KEY (`table_type_id`) REFERENCES `table_type` (`id`),
  ADD CONSTRAINT `FK_FCF671A6D54E65B0` FOREIGN KEY (`entry_type_of_number_id`) REFERENCES `type_of_number` (`id`);

--
-- Contraintes pour la table `table_content_parameter_set`
--
ALTER TABLE `table_content_parameter_set`
  ADD CONSTRAINT `FK_B482EB89D94EFB80` FOREIGN KEY (`table_content_id`) REFERENCES `table_content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B482EB89F48507E9` FOREIGN KEY (`parameter_set_id`) REFERENCES `parameter_set` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_type`
--
ALTER TABLE `table_type`
  ADD CONSTRAINT `FK_C60817E2FC08E877` FOREIGN KEY (`astronomical_object_id`) REFERENCES `astronomical_object` (`id`);

--
-- Contraintes pour la table `user_interface_text`
--
ALTER TABLE `user_interface_text`
  ADD CONSTRAINT `FK_E9006516896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_E9006516B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`);

--
-- Contraintes pour la table `work`
--
ALTER TABLE `work`
  ADD CONSTRAINT `FK_534E68805370E40B` FOREIGN KEY (`translator_id`) REFERENCES `historical_actor` (`id`),
  ADD CONSTRAINT `FK_534E6880896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_534E6880B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_534E6880DA6A219` FOREIGN KEY (`place_id`) REFERENCES `place` (`id`);

--
-- Contraintes pour la table `work_historical_actor`
--
ALTER TABLE `work_historical_actor`
  ADD CONSTRAINT `FK_C057BDA5BB3453DB` FOREIGN KEY (`work_id`) REFERENCES `work` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_C057BDA5D21B6558` FOREIGN KEY (`historical_actor_id`) REFERENCES `historical_actor` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `xml_file`
--
ALTER TABLE `xml_file`
  ADD CONSTRAINT `FK_9858065E896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_9858065EB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `fos_user` (`id`),
  ADD CONSTRAINT `FK_9858065EF0B5051D` FOREIGN KEY (`entity_definition_id`) REFERENCES `definition` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
