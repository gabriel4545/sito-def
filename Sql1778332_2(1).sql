-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 89.46.111.192
-- Creato il: Ott 03, 2025 alle 07:03
-- Versione del server: 5.7.43-47-log
-- Versione PHP: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Sql1778332_2`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Allegati`
--

CREATE TABLE `Allegati` (
  `id` int(11) NOT NULL,
  `nomeFile` varchar(255) NOT NULL,
  `percorsoFile` text NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Allegati`
--

INSERT INTO `Allegati` (`id`, `nomeFile`, `percorsoFile`, `isDeleted`) VALUES
(1, 'IntroduzioneHTML.png', '/assets/imgs/portfolio/IntroduzioneHTML.png', 0),
(2, 'IntroduzionePHP.png', '/assets/imgs/portfolio/IntroduzionePHP.png', 0),
(3, 'IntroduzioneC#.png', '/assets/imgs/portfolio/IntroduzioneC#.png', 0),
(4, 'Screenshot 2025-07-21 153231.png', 'uploads/articoli/articolo_20251001152147_68dd2aeb34342.png', 0),
(5, 'Screenshot 2025-08-28 161706.png', 'uploads/articoli/articolo_20251002104522_68de3ba295388.png', 0),
(6, 'Screenshot 2025-10-02 130039.png', 'uploads/articoli/articolo_20251002130114_68de5b7a8a855.png', 0),
(7, 'mazda-6e-2025-7839_45.jpg', 'uploads/articoli/articolo_20251002132405_68de60d523fe2.jpg', 0),
(8, 'mazda-6e-2025-7839_45(1).jpg', 'uploads/articoli/articolo_20251002132523_68de6123899ba.jpg', 0),
(9, 'ford-cx740s-eu-CX740S_Select_Studio-1000x667-min.jpg', 'uploads/articoli/articolo_20251002133436_68de634cbb87c.jpg', 0),
(10, 'ford-cx740s-eu-CX740S_Select_Studio-1000x667-min.jpg', 'uploads/articoli/articolo_20251002133804_68de641c6b108.jpg', 0);

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

--
-- Dump dei dati per la tabella `Articoli`
--

INSERT INTO `Articoli` (`id`, `categoriaArticolo`, `titolo`, `testo`, `allegato`, `isDeleted`, `data`) VALUES
(6, 3, 'Prova', '\"JSON encoding\"', NULL, 1, '2025-10-01 14:35:01'),
(7, 3, 'Prova', 'prova prtova\ncviao\ncaio', NULL, 1, '2025-10-01 14:36:27'),
(8, 3, 'Prova 4', '<section> <h1>Introduzione a C#</h1> <p><strong>C#</strong> (si legge <em>si-sharp</em>) Ã¨ un linguaggio di programmazione moderno, tipizzato e orientato agli oggetti, creato da Microsoft. Ãˆ ideale per sviluppare applicazioni desktop, web, mobile, giochi (con Unity) e servizi cloud su .NET.</p> <h2>Prerequisiti</h2> <ul> <li>Installa <strong>.NET SDK</strong> da <a href=\\\"https://dotnet.microsoft.com/\\\">dotnet.microsoft.com</a>.</li> <li>Editor consigliato: <strong>Visual Studio Code</strong> o <strong>Visual Studio</strong>.</li> </ul> <h2>Crea e avvia il primo progetto</h2> <ol> <li>Apri il terminale.</li> <li>Esegui: <code>dotnet new console -n HelloCSharp</code></li> <li>Entra nella cartella: <code>cd HelloCSharp</code></li> <li>Avvia: <code>dotnet run</code></li> </ol> <h2>Hello, World!</h2> <pre><code>// Program.cs using System;\n\nclass Program\n{\nstatic void Main()\n{\nConsole.WriteLine(\\\"Ciao, mondo!\\\");\n}\n}\n</code></pre>\n\n<h2>Tipi di dato e variabili</h2> <ul> <li><strong>Primitivi</strong>: <code>int</code>, <code>double</code>, <code>bool</code>, <code>char</code></li> <li><strong>Stringhe</strong>: <code>string</code></li> <li><strong>Var</strong> (inferenza di tipo): <code>var x = 10;</code></li> </ul> <pre><code>int eta = 30; double prezzo = 9.99; bool attivo = true; string nome = \\\"Ada\\\"; var messaggio = $\\\"Ciao {nome}, hai {eta} anni.\\\"; Console.WriteLine(messaggio); </code></pre> <h2>Controllo di flusso</h2> <pre><code>int n = 5;\n\nif (n > 0)\n{\nConsole.WriteLine(\\\"Positivo\\\");\n}\nelse if (n == 0)\n{\nConsole.WriteLine(\\\"Zero\\\");\n}\nelse\n{\nConsole.WriteLine(\\\"Negativo\\\");\n}\n\nfor (int i = 0; i < 3; i++)\nConsole.WriteLine(i);\n\nint j = 0;\nwhile (j < 3)\n{\nConsole.WriteLine(j);\nj++;\n}\n</code></pre>\n\n<h2>Metodi e parametri</h2> <pre><code>static int Somma(int a, int b) => a + b;', 4, 1, '2025-10-01 14:40:41'),
(9, 1, 'Prova 3', 'ciao, questa Ã¨ la prova numero 3', 5, 1, '2025-10-02 10:45:22'),
(10, 2, 'Il Van Tour Hikvision arriva a Scandicci!', '<p><span class=\\\"break-words\n          tvm-parent-container\\\"><span dir=\\\"ltr\\\">ðŸš Il Van Tour Hikvision arriva a Scandicci!<br />ðŸ“… Venerd&igrave; 10 ottobre 2025 &ndash; dalle 9,30 alle 18,00 <br />ðŸ“ HDI Distribuzione &ndash; via del Padule 44B<br /> Filiale di Scandicci (FI)<br /><br />Siamo felici di ospitare una tappa del Van Tour <a class=\\\"sjkaPHPSCljuEVirqBzQIpNsTKFWmzcDTtg \\\" tabindex=\\\"0\\\" href=\\\"https://www.linkedin.com/company/hikvision-italy/\\\" target=\\\"_self\\\" data-test-app-aware-link=\\\"\\\">HIKVISION ITALY</a>, l&rsquo;evento itinerante che porta su strada le ultime soluzioni tecnologiche del catalogo ProExpert.<br /><br />ðŸŽ¯ A bordo del van potrete scoprire dal vivo:<br />ðŸ”¹ AcuSense 3.0 &amp; AcuSearch<br />ðŸ”¹ Nuove telecamere Smart Hybrid Light<br />ðŸ”¹ Gamma Polimero<br />ðŸ”¹ Sistema AXPro + sensori<br />ðŸ”¹ CurtainVu<br />ðŸ”¹ Telecamere Wi-Fi<br />ðŸ”¹ Super ColorVu 3.0<br />ðŸ”¹ Power X analogico<br />ðŸ”¹ Termografia HeatPro<br />ðŸ”¹ Soluzioni Intercom &amp; Controllo Accessi<br />ðŸ”¹ Networking<br />ðŸ”¹ App HikPartner Pro<br /> <br />ðŸ“½ï¸ All&rsquo;esterno, un LED wall presenter&agrave; in loop video dimostrativi delle tecnologie esposte.<br />ðŸ‘¨&zwj;ðŸ’¼ I nostri tecnici, insieme al team Hikvision, saranno a disposizione per approfondimenti e demo personalizzate.<br /><br />ðŸ‘‰ Non mancare! Un&rsquo;occasione unica per aggiornarsi, confrontarsi e toccare con mano l&rsquo;innovazione.</span></span></p>', 6, 0, '2025-10-02 13:01:14'),
(14, 1, 'Mazda 6e: la rinascita elettrica della berlina', '<article style=\\\"font-family:sans-serif;line-height:1.5;color:#222;max-width:900px;margin:20px auto;padding:0 10px;\\\">\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Design ed estetica</h2>\n    <p>La 6e interpreta il linguaggio Kodo con superfici pulite e proporzioni da berlina sportiva. La coda liftback a 5 porte massimizza la praticitÃ  senza sacrificare la linea filante.</p>\n    <ul style=\\\"margin:0 0 10px 18px;padding:0;\\\">\n      <li>Carrozzeria liftback 5 porte</li>\n      <li>Maniglie a filo e profilo aerodinamico</li>\n      <li>Firma luminosa LED sottile</li>\n      <li>Proporzioni equilibrate, look premium</li>\n    </ul>\n    <img src=\\\"https://tse3.mm.bing.net/th/id/OIP.gHpvsxp1t1hzG3Srk8x1rgHaE8?pid=Api\\\" alt=\\\"Vista laterale Mazda 6e\\\" style=\\\"max-width:100%;border:1px solid #ddd;border-radius:6px;margin:10px 0;\\\">\n  </section>\n\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Interni e tecnologia</h2>\n    <p>Abitacolo minimalista con grande display centrale, quadro strumenti digitale e head-up display AR. Illuminazione ambientale e materiali curati completano lâ€™atmosfera.</p>\n    <ul style=\\\"margin:0 0 10px 18px;padding:0;\\\">\n      <li>Infotainment ampio</li>\n      <li>Strumentazione digitale</li>\n      <li>Head-up display AR</li>\n      <li>Sedili ergonomici e audio premium</li>\n    </ul>\n  </section>\n\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Prestazioni e autonomia</h2>\n    <p>Trazione posteriore, autonomia fino a <strong>550 km WLTP</strong>, accelerazione 0â€“100 km/h in circa 7â€“8 s.</p>\n    <ul style=\\\"margin:0 0 10px 18px;padding:0;\\\">\n      <li>Trazione: RWD</li>\n      <li>Autonomia: ~550 km*</li>\n      <li>0â€“100 km/h: ~7â€“8 s*</li>\n      <li>Potenza: ~190â€“250 CV*</li>\n    </ul>\n    <p style=\\\"font-size:.85rem;color:#555;\\\">*Valori indicativi.</p>\n  </section>\n\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Ricarica</h2>\n    <p>Supporto ricarica rapida DC fino a 150â€“200 kW: 10â€“80% in poco piÃ¹ di 20 minuti.</p>\n  </section>\n\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Concorrenza</h2>\n    <p>Rivali dirette: Tesla Model 3, BMW i5, Mercedes EQE. La 6e punta su design e qualitÃ  Mazda.</p>\n    <img src=\\\"https://tse1.mm.bing.net/th/id/OIP.TG6pMiXB_RBlcc98HtnfegHaDt?pid=Api\\\" alt=\\\"Frontale Mazda 6e\\\" style=\\\"max-width:100%;border:1px solid #ddd;border-radius:6px;margin:10px 0;\\\">\n  </section>\n\n  <section>\n    <h2 style=\\\"color:#b60015;margin:15px 0 5px;font-size:1.4rem;\\\">Conclusioni</h2>\n    <p>La <strong>Mazda 6e</strong> segna un passo importante: berlina elettrica moderna con estetica ricercata, interni tecnologici e autonomia competitiva.</p>\n  </section>\n</article>', 8, 0, '2025-10-02 13:23:11'),
(16, 1, 'Ford Explorer, non quello che ti aspetti', '<div style=\\\"font-family:sans-serif;line-height:1.6;color:#222;max-width:900px;margin:20px auto;padding:0 10px;\\\">\n\n  <div>\n    <h2 style=\\\"color:#003399;margin:15px 0 10px;font-size:1.5rem;\\\">Ford Explorer EV: il nuovo volto elettrico della casa americana</h2>\n    <p style=\\\"margin:0 0 12px;\\\">Con lâ€™arrivo del <strong>Ford Explorer EV</strong>, il marchio dellâ€™Ovale Blu porta in Europa un SUV elettrico che unisce design deciso, tecnologia moderna e grande versatilitÃ . Basato sulla piattaforma MEB del gruppo Volkswagen, lâ€™Explorer EV conserva unâ€™anima americana con linee scolpite e presenza su strada.</p>\n  </div>\n\n  <div>\n    <h2 style=\\\"color:#003399;margin:15px 0 10px;font-size:1.5rem;\\\">Interni moderni e spazio a bordo</h2>\n    <p style=\\\"margin:0 0 12px;\\\">A bordo spicca il grande display verticale da 14,6\\\", regolabile nellâ€™inclinazione, affiancato da una strumentazione digitale compatta. Pavimento piatto e vani ben studiati, come la MegaConsole centrale, massimizzano praticitÃ  e spazio.</p>\n  </div>\n\n  <div>\n    <h2 style=\\\"color:#003399;margin:15px 0 10px;font-size:1.5rem;\\\">Prestazioni ed efficienza</h2>\n    <p style=\\\"margin:0 0 12px;\\\">Tra le configurazioni disponibili, la <strong>Long Range RWD</strong> Ã¨ quella che brilla: singolo motore posteriore da 210 kW (286 CV), fino a <strong>602 km WLTP</strong> con una carica e 0â€“100 km/h in circa 6,4 s. Meno soste, piÃ¹ fluiditÃ  nei lunghi viaggi.</p>\n    <p style=\\\"margin:0 0 12px;\\\">Rispetto allâ€™AWD, la Long Range RWD offre maggiore autonomia ed efficienza, oltre a una guida piÃ¹ â€œpuraâ€ grazie alla spinta sullâ€™asse posteriore.</p>\n  </div>\n\n  <div>\n    <h2 style=\\\"color:#003399;margin:15px 0 10px;font-size:1.5rem;\\\">La scelta migliore: Long Range RWD</h2>\n    <p style=\\\"margin:0 0 12px;\\\">La Long Range RWD rappresenta il miglior compromesso tra autonomia, prestazioni e costi: oltre 600 km disponibili, meccanica meno complessa rispetto allâ€™AWD e peso inferiore che aiuta consumi e dinamica.</p>\n    <p style=\\\"margin:0 0 12px;\\\">Ãˆ ideale per lâ€™uso quotidiano e per i viaggi lunghi senza ansia da ricarica.</p>\n  </div>\n\n  <div>\n    <h2 style=\\\"color:#003399;margin:15px 0 10px;font-size:1.5rem;\\\">Conclusioni</h2>\n    <p style=\\\"margin:0 0 12px;\\\">Il <strong>Ford Explorer EV</strong> convince per design, tecnologia e autonomia. Tra le versioni, la <strong>Long Range RWD</strong> Ã¨ la scelta piÃ¹ sensata: autonomia al top, prestazioni brillanti e una guida equilibrata adatta a famiglie e viaggiatori.</p>\n  </div>\n\n</div>', 10, 0, '2025-10-02 13:38:04');

-- --------------------------------------------------------

--
-- Struttura della tabella `CategorieArticoli`
--

CREATE TABLE `CategorieArticoli` (
  `id` int(11) NOT NULL,
  `categoriaArticolo` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `CategorieArticoli`
--

INSERT INTO `CategorieArticoli` (`id`, `categoriaArticolo`, `isDeleted`) VALUES
(1, 'Automotive', 0),
(2, 'Security', 0),
(3, 'ICT & Web Development', 0),
(5, 'Pillole della mia mente', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `MotiviRichieste`
--

CREATE TABLE `MotiviRichieste` (
  `id` int(11) NOT NULL,
  `nomeMotivoRichiesta` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `MotiviRichieste`
--

INSERT INTO `MotiviRichieste` (`id`, `nomeMotivoRichiesta`, `isDeleted`) VALUES
(1, 'Informazioni', 0),
(2, 'Prova', 1),
(3, 'Sviluppo e gestione sito web', 0),
(4, 'Consulenza Clienti HDi Distribuzione', 0),
(5, 'Collaborazioni', 0),
(6, 'aa', 1);

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

--
-- Dump dei dati per la tabella `Richieste`
--

INSERT INTO `Richieste` (`id`, `nome`, `cognome`, `telefono`, `cellulare`, `mail`, `indirizzo`, `nazione`, `testoRichiesta`, `motivoRichiesta`, `isDeleted`, `data`) VALUES
(5, 'Pippo', 'Palmieri', '3401939000', '3419056488', 'pippo@pi.it', 'Via di rpova 34', 'Italia', 'Bongiorno\r\ncome va', 1, 1, '2025-01-18 14:20:56'),
(8, 'Pippo', 'Baudo', '', '3401939012', 'pippo@baudo.it', '', '', 'Ciao, questa Ã¨ una prova', 1, 0, '2025-10-02 10:01:57'),
(9, 'aa', 'aa', '', '3412512512', 'aaa@aa.it', '', '', 'asdasd', 5, 1, '2025-10-02 12:57:56'),
(10, 'Marco', 'Pizzi', '', '3326598456', 'm.pizzi@gmail.com', '', '', 'ciao, ho bisono', 1, 1, '2025-10-02 15:56:00');

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

--
-- Dump dei dati per la tabella `UtentiSito`
--

INSERT INTO `UtentiSito` (`id`, `login`, `password`, `isDeleted`) VALUES
(2, 'gabriel@gmail.com', '$2y$10$mBGfnEY01TYSLMNbYNeZW.2yYO6tuXVVfB5gu/pr.G1/NGZsPA8gW', 0),
(6, 'rino@prof.it', '$2y$10$cG/KmT.WwW6PitN2fqxfMuaXrazRM772SM8.N4X0RZ2z9S4sqSFf2', 0);

--
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
