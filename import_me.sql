--
-- Datenbank: `filmdb`
--

CREATE DATABASE `filmdb`;
USE `filmdb`;
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_films`
--

CREATE TABLE `tbl_films` (
  `FilmID` int(11) NOT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `ReleaseYear` int(11) DEFAULT NULL,
  `FskID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_film_actor`
--

CREATE TABLE `tbl_film_actor` (
  `FilmID` int(11) NOT NULL,
  `ActorID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_agelimit`
--

CREATE TABLE `tbl_agelimit` (
  `FskID` int(11) NOT NULL,
  `AgeLimit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_actors`
--

CREATE TABLE `tbl_actors` (
  `ActorID` int(11) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tbl_films`
--
ALTER TABLE `tbl_films`
  ADD PRIMARY KEY (`FilmID`),
  ADD KEY `FskID` (`FskID`);

--
-- Indizes für die Tabelle `tbl_film_actor`
--
ALTER TABLE `tbl_film_actor`
  ADD PRIMARY KEY (`FilmID`,`ActorID`),
  ADD KEY `ActorID` (`ActorID`);

--
-- Indizes für die Tabelle `tbl_agelimit`
--
ALTER TABLE `tbl_agelimit`
  ADD PRIMARY KEY (`FskID`);

--
-- Indizes für die Tabelle `tbl_actors`
--
ALTER TABLE `tbl_actors`
  ADD PRIMARY KEY (`ActorID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tbl_films`
--
ALTER TABLE `tbl_films`
  MODIFY `FilmID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tbl_agelimit`
--
ALTER TABLE `tbl_agelimit`
  MODIFY `FskID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tbl_actors`
--
ALTER TABLE `tbl_actors`
  MODIFY `ActorID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `tbl_films`
--
ALTER TABLE `tbl_films`
  ADD CONSTRAINT `tbl_filme_ibfk_1` FOREIGN KEY (`FskID`) REFERENCES `tbl_agelimit` (`FskID`);

--
-- Constraints der Tabelle `tbl_film_actor`
--
ALTER TABLE `tbl_film_actor`
  ADD CONSTRAINT `tbl_film_schauspieler_ibfk_1` FOREIGN KEY (`FilmID`) REFERENCES `tbl_films` (`FilmID`),
  ADD CONSTRAINT `tbl_film_schauspieler_ibfk_2` FOREIGN KEY (`ActorID`) REFERENCES `tbl_actors` (`ActorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Einfügen von FSK-Daten
INSERT INTO tbl_agelimit (AgeLimit) VALUES
                                           (0), (6), (12), (16), (18);

-- Einfügen von Filmdaten
INSERT INTO tbl_films (Title, ReleaseYear, FskID) VALUES
                                                           ('Der Pate', 1972, 4),
                                                           ('Pulp Fiction', 1994, 5),
                                                           ('Inception', 2010, 4),
                                                           ('Die Verurteilten', 1994, 3),
                                                           ('Forrest Gump', 1994, 3),
                                                           ('Matrix', 1999, 4),
                                                           ('Der Herr der Ringe: Die Gefährten', 2001, 3),
                                                           ('Das Schweigen der Lämmer', 1991, 4),
                                                           ('Schindlers Liste', 1993, 3),
                                                           ('Zurück in die Zukunft', 1985, 2);

-- Einfügen von Schauspielerdaten
INSERT INTO tbl_actors (FirstName, LastName) VALUES
                                                     ('Marlon', 'Brando'),
                                                     ('Al', 'Pacino'),
                                                     ('John', 'Travolta'),
                                                     ('Uma', 'Thurman'),
                                                     ('Leonardo', 'DiCaprio'),
                                                     ('Joseph', 'Gordon-Levitt'),
                                                     ('Tim', 'Robbins'),
                                                     ('Morgan', 'Freeman'),
                                                     ('Tom', 'Hanks'),
                                                     ('Robin', 'Wright'),
                                                     ('Keanu', 'Reeves'),
                                                     ('Carrie-Anne', 'Moss'),
                                                     ('Elijah', 'Wood'),
                                                     ('Ian', 'McKellen'),
                                                     ('Anthony', 'Hopkins'),
                                                     ('Jodie', 'Foster'),
                                                     ('Liam', 'Neeson'),
                                                     ('Ben', 'Kingsley'),
                                                     ('Michael', 'J. Fox'),
                                                     ('Christopher', 'Lloyd');

-- Einfügen von Film-Schauspieler-Zuordnungen
INSERT INTO tbl_film_actor (FilmID, ActorID) VALUES
                                                 (1, 1), (1, 2),
                                                 (2, 3), (2, 4),
                                                 (3, 5), (3, 6),
                                                 (4, 7), (4, 8),
                                                 (5, 9), (5, 10),
                                                 (6, 11), (6, 12),
                                                 (7, 13), (7, 14),
                                                 (8, 15), (8, 16),
                                                 (9, 17), (9, 18),
                                                 (10, 19), (10, 20);