-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 25 Août 2014 à 23:55
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `aupresserre`
--
CREATE DATABASE IF NOT EXISTS `smartfulframework` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `smartfulframework`;

-- --------------------------------------------------------

--
-- Structure de la table `adherents`
--

CREATE TABLE IF NOT EXISTS `adherents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `sexe` enum('f','m') NOT NULL,
  `age` int(11) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `validation` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `adherents`
--

INSERT INTO `adherents` (`id`, `prenom`, `nom`, `email`, `pass`, `sexe`, `age`, `date_inscription`, `validation`) VALUES
(7, 'grande', 'MADAME', 'grande_madame@hotmail.com', 'caa70946d8da3b59d1e0e798712934907f004695', 'f', 23, '2014-06-23 17:08:04', 1);

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_admin` varchar(50) NOT NULL,
  `pass_admin` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `admin`
--

INSERT INTO `admin` (`id`, `nom_admin`, `pass_admin`) VALUES
(1, 'admin1', '123a456z');

-- --------------------------------------------------------

--
-- Structure de la table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `albums`
--

INSERT INTO `albums` (`id`, `titre`, `description`) VALUES
(1, 'test_1', 'Ceci est un album de test. \r\nTest numéro 1 !										\r\n										'),
(2, 'test_2', 'Et voilà le TEEEEEEEEEEEEEEEst 2 !!!\r\nSamba !!!										\r\n										'),
(4, 'test_3', 'Et pour le fun, un troisième test !!!										\r\n										');

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

CREATE TABLE IF NOT EXISTS `archives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(500) NOT NULL,
  `date_ajout` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `archives`
--

INSERT INTO `archives` (`id`, `titre`, `date_ajout`) VALUES
(3, 'Note-robotique-Rivaton-2', '2014-06-24 22:44:13');

-- --------------------------------------------------------

--
-- Structure de la table `billets`
--

CREATE TABLE IF NOT EXISTS `billets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `date_ajout` datetime NOT NULL,
  `date_modif` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `billets`
--

INSERT INTO `billets` (`id`, `titre`, `contenu`, `date_ajout`, `date_modif`) VALUES
(1, 'Sortie Kart', 'L’Association Machin veut s\'amuser. Séance de Karting programmé pour le samedi 15 octobre 2016.', '2014-06-23 18:46:33', '2014-06-23 18:46:33'),
(4, 'La semaine de la science ', 'Du 8 au 16 octobre c\'est la fête de la science, et nous comptons bien y apporter notre dose de folie ! \r\nVenez nous tenir main forte !!!', '2014-06-23 18:54:59', '2014-06-23 19:15:41');

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_album` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Contenu de la table `photos`
--

INSERT INTO `photos` (`id`, `id_album`, `titre`) VALUES
(16, 2, 'espace_lune.jpg'),
(18, 1, 'nage.jpg'),
(24, 4, 'ciel_vaisseau.jpeg'),
(25, 4, 'ciel_vaisseau2.jpg');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
