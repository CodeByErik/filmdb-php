<?php
require_once 'db_connect.php';

/**
 * Fügt einen neuen Film in die Datenbank ein.
 *
 * @param string $title Der Titel des Films.
 * @param int $releaseYear Das Erscheinungsjahr des Films.
 * @param int $fskId Die FSK-ID des Films.
 * @return bool Gibt true zurück, wenn der Film erfolgreich hinzugefügt wurde, andernfalls false.
 */
function addFilm(string $title, int $releaseYear, int $fskId): bool
{
    global $conn;
    $sql = "INSERT INTO tbl_films (Title, ReleaseYear, FskID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $title, $releaseYear, $fskId);
    return $stmt->execute();
}

/**
 * Löscht einen Film aus der Datenbank.
 *
 * @param int $filmId Die ID des zu löschenden Films.
 * @return bool Gibt true zurück, wenn der Film erfolgreich gelöscht wurde, andernfalls false.
 */
function deleteFilm(int $filmId): bool
{
    global $conn;

    // Zuerst löschen wir die Verknüpfung in der tbl_film_actor
    $sql = "DELETE FROM tbl_film_actor WHERE FilmID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filmId);
    $stmt->execute();

    // Dann löschen wir den Film aus der tbl_films
    $sql = "DELETE FROM tbl_films WHERE FilmID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filmId);
    return $stmt->execute();
}

/**
 * Ruft alle Filme aus der Datenbank ab.
 *
 * @return array Ein assoziatives Array mit den Filmdaten.
 */
function retrieveFilms(): array
{
    global $conn;
    $sql = "SELECT f.FilmID, f.Title, f.ReleaseYear, fs.AgeLimit AS AgeLimit,
    GROUP_CONCAT(CONCAT(s.FirstName, ' ', s.LastName) SEPARATOR ', ') AS Actors
    FROM tbl_films f
    LEFT JOIN tbl_agelimit fs ON f.FskID = fs.FskID
    LEFT JOIN tbl_film_actor fa ON f.FilmId = fa.FilmId
    LEFT JOIN tbl_actors s ON fa.ActorID = s.ActorID
    GROUP BY f.FilmId, f.Title, f.ReleaseYear, fs.AgeLimit";
    $result = $conn->query($sql);
    if (!$result) {
        die("Fehler beim Ausführen der Abfrage: " . $conn->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Ruft die Daten eines bestimmten Films aus der Datenbank ab.
 *
 * @param int $filmId Die ID des Films.
 * @return array Ein assoziatives Array mit den Filmdaten.
 */
function retrieveFilm(int $filmId): array
{
    global $conn;
    $sql = "SELECT f.*, fs.AgeLimit AS AgeLimit,
    GROUP_CONCAT(CONCAT(s.FirstName, ' ', s.LastName) SEPARATOR ', ') AS Actors
    FROM tbl_films f
    LEFT JOIN tbl_agelimit fs ON f.FSKID = fs.FSKID
    LEFT JOIN tbl_film_actor fa ON f.FilmId = fa.FilmId
    LEFT JOIN tbl_actors s ON fa.ActorID = s.ActorID
    WHERE f.FilmId = ?
    GROUP BY f.FilmId";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filmId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Aktualisiert die Daten eines Films in der Datenbank.
 *
 * @param int $filmId Die ID des Films.
 * @param string $title Der neue Titel des Films.
 * @param int $releaseYear Das neue Erscheinungsjahr des Films.
 * @param int $fskId Die neue FSK-ID des Films.
 * @return bool Gibt true zurück, wenn der Film erfolgreich aktualisiert wurde, andernfalls false.
 */
function updateFilm(int $filmId, string $title, int $releaseYear, int $fskId): bool
{
    global $conn;
    $sql = "UPDATE tbl_films SET Title = ?, ReleaseYear = ?, FskID = ? WHERE FilmId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $title, $releaseYear, $fskId, $filmId);
    return $stmt->execute();
}

/**
 * Gibt die FSK-ID basierend auf dem Alterslimit zurück.
 *
 * @param string $ageLimit Das Alterslimit.
 * @return int Die entsprechende FSK-ID.
 */
function getFskId(string $ageLimit): int
{
    switch ($ageLimit) {
        case '0': return 1;
        case '6': return 2;
        case '12': return 3;
        case '16': return 4;
        case '18': return 5;
        default: return 0;
    }
}