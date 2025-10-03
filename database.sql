-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
-- Versione PHP: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- Struttura della tabella `Allegati`
--

CREATE TABLE `Allegati` (
  `id` int(11) NOT NULL,
  `nomeFile` varchar(255) NOT NULL,
  `percorsoFile` text NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `Articoli`
--

CREATE TABLE `Articoli` (
  `id` int(11) NOT NULL,
  `categoriaArticolo` int(11) NOT NULL,
  `titolo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `testo` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `allegato` int(11) DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `annualita` year(4) GENERATED ALWAYS AS (year(`data`)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Struttura della tabella `CategorieArticoli`
--

CREATE TABLE `CategorieArticoli` (
  `id` int(11) NOT NULL,
  `categoriaArticolo` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `MotiviRichieste`
--

CREATE TABLE `MotiviRichieste` (
  `id` int(11) NOT NULL,
  `nomeMotivoRichiesta` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `Richieste`
--

CREATE TABLE `Richieste` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `cellulare` varchar(20) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `indirizzo` text,
  `nazione` varchar(100) DEFAULT NULL,
  `testoRichiesta` text,
  `motivoRichiesta` int(11) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `UtentiSito`
--

CREATE TABLE `UtentiSito` (
  `id` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Allegati`
--
ALTER TABLE `Allegati`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `Articoli`
--
ALTER TABLE `Articoli`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoriaArticolo` (`categoriaArticolo`),
  ADD KEY `fk_allegato` (`allegato`);

--
-- Indici per le tabelle `CategorieArticoli`
--
ALTER TABLE `CategorieArticoli`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `MotiviRichieste`
--
ALTER TABLE `MotiviRichieste`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `Richieste`
--
ALTER TABLE `Richieste`
  ADD PRIMARY KEY (`id`),
  ADD KEY `motivoRichiesta` (`motivoRichiesta`);

--
-- Indici per le tabelle `UtentiSito`
--
ALTER TABLE `UtentiSito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Allegati`
--
ALTER TABLE `Allegati`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `Articoli`
--
ALTER TABLE `Articoli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT per la tabella `CategorieArticoli`
--
ALTER TABLE `CategorieArticoli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `MotiviRichieste`
--
ALTER TABLE `MotiviRichieste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `Richieste`
--
ALTER TABLE `Richieste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `UtentiSito`
--
ALTER TABLE `UtentiSito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Articoli`
--
ALTER TABLE `Articoli`
  ADD CONSTRAINT `Articoli_ibfk_1` FOREIGN KEY (`categoriaArticolo`) REFERENCES `CategorieArticoli` (`id`),
  ADD CONSTRAINT `fk_allegato` FOREIGN KEY (`allegato`) REFERENCES `Allegati` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `Richieste`
--
ALTER TABLE `Richieste`
  ADD CONSTRAINT `Richieste_ibfk_1` FOREIGN KEY (`motivoRichiesta`) REFERENCES `MotiviRichieste` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
