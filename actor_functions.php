<?php
require_once 'db_connect.php';

/**
 * Ruft die ID eines Schauspielers basierend auf Vor- und Nachname ab.
 *
 * @param string $firstName Der Vorname des Schauspielers.
 * @param string $lastName Der Nachname des Schauspielers.
 * @return int|null Die ID des Schauspielers oder null, wenn der Schauspieler nicht gefunden wurde.
 */
function getActorId(string $firstName, string $lastName)
{
    global $conn;
    // SQL-Abfrage, um die ID des Schauspielers abzurufen
    $sql = "SELECT ActorID FROM tbl_actors WHERE FirstName = ? AND LastName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $firstName, $lastName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['ActorID'];
    }
    return null;
}
/**
 * Ruft die Schauspieler eines bestimmten Films ab.
 *
 * @param int $filmId Die ID des Films.
 * @return array Ein assoziatives Array mit den Schauspielerdaten.
 */
function retrieveActors(int $filmId): array
{
    global $conn;
    $sql = "SELECT a.ActorID, a.FirstName, a.LastName FROM tbl_actors a
            INNER JOIN tbl_film_actor fa ON a.ActorID = fa.ActorID
            WHERE fa.FilmID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filmId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Ruft die Daten eines bestimmten Schauspielers ab.
 *
 * @param int $actorId Die ID des Schauspielers.
 * @return array Ein assoziatives Array mit den Daten des Schauspielers.
 */
function retrieveSingleActor(int $actorId): array
{
    global $conn;
    $sql = "SELECT ActorID, FirstName, LastName FROM tbl_actors WHERE ActorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $actorId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Fügt einen neuen Schauspieler hinzu.
 *
 * @param string $firstName Der Vorname des Schauspielers.
 * @param string $lastName Der Nachname des Schauspielers.
 * @return int|null Die ID des neu hinzugefügten Schauspielers oder null bei Fehler.
 */
function addActor(string $firstName, string $lastName)
{
    global $conn;
    $sql = "INSERT INTO tbl_actors (FirstName, LastName) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $firstName, $lastName);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return null;
}

/**
 * Löscht einen Schauspieler und seine Verknüpfungen zu Filmen.
 *
 * @param int $actorId Die ID des Schauspielers.
 * @return bool True bei Erfolg, false bei Fehler.
 */
function deleteActor(int $actorId): bool
{
    global $conn;

    $conn->begin_transaction();

    try {
        // Zuerst löschen wir die Verknüpfung in der tbl_film_actor
        $sql = "DELETE FROM tbl_film_actor WHERE ActorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $actorId);
        $stmt->execute();

        // Dann löschen wir den Schauspieler aus der tbl_actors
        $sql = "DELETE FROM tbl_actors WHERE ActorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $actorId);
        $stmt->execute();

        // Wenn alles erfolgreich war, bestätigen wir die Transaktion
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Wenn ein Fehler auftritt, wird die Transaktion rückgängig gemacht
        $conn->rollback();
        return false;
    }
}

/**
 * Entfernt die Verknüpfung eines Schauspielers mit einem Film.
 *
 * @param int $filmId Die ID des Films.
 * @param int $actorId Die ID des Schauspielers.
 * @return bool True bei Erfolg, false bei Fehler oder wenn die Verknüpfung nicht gefunden wurde.
 */
function unlinkActorFilm(int $filmId, int $actorId): bool
{
    global $conn;
    $sql = "DELETE FROM tbl_film_actor WHERE FilmID = ? AND ActorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $filmId, $actorId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            return true; // Erfolgreich gelöscht
        } else {
            return false; // Nicht gefunden
        }
    } else {
        // Fehler beim Löschen
        error_log("Fehler beim Löschen des Schauspielers aus dem Film: " . $stmt->error);
        return false;
    }
}

/**
 * Verknüpft einen Schauspieler mit einem Film.
 *
 * @param int $filmId Die ID des Films.
 * @param int $actorId Die ID des Schauspielers.
 * @return bool True bei Erfolg, false bei Fehler.
 */
function linkActorFilm(int $filmId, int $actorId): bool
{
    global $conn;
    $sql = "INSERT INTO tbl_film_actor (FilmId, ActorId) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $filmId, $actorId);
    return $stmt->execute();
}

/**
 * Ruft die Filme eines bestimmten Schauspielers ab.
 *
 * @param int $actorId Die ID des Schauspielers.
 * @return array Ein assoziatives Array mit den Filmdaten.
 */
function retrieveFilmsByActor(int $actorId): array
{
    global $conn;
    $sql = "SELECT f.FilmID, f.Title, f.ReleaseYear, fs.AgeLimit AS AgeLimit
    FROM tbl_films f
    LEFT JOIN tbl_agelimit fs ON f.FskID = fs.FskID
    LEFT JOIN tbl_film_actor fa ON f.FilmId = fa.FilmId
    LEFT JOIN tbl_actors s ON fa.ActorID = s.ActorID
    WHERE s.ActorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $actorId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Aktualisiert die Daten eines Schauspielers.
 *
 * @param int $actorId Die ID des Schauspielers.
 * @param string $firstName Der Vorname des Schauspielers.
 * @param string $lastName Der Nachname des Schauspielers.
 * @return bool True bei Erfolg, false bei Fehler.
 */
function updateActor(int $actorId, string $firstName, string $lastName): bool
{
    global $conn;
    $sql = "UPDATE tbl_actors SET FirstName = ?, LastName = ? WHERE ActorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $firstName, $lastName, $actorId);
    return $stmt->execute();
}

/**
 * Fügt einen neuen Film hinzu und verknüpft Schauspieler mit dem Film.
 *
 * @param string $title Der Titel des Films.
 * @param int $releaseYear Das Erscheinungsjahr des Films.
 * @param int $ageLimit Die Altersfreigabe des Films.
 * @param array $actors Ein Array von Schauspielern, wobei jeder Schauspieler ein assoziatives Array mit 'firstName' und 'lastName' ist.
 * @return bool True bei Erfolg, false bei Fehler.
 */
function addFilmWithActors(string $title, int $releaseYear, int $ageLimit, array $actors): bool
{
    global $conn;

    $fskId = getFskId($ageLimit);

    if (addFilm($title, $releaseYear, $fskId)) {
        $filmId = $conn->insert_id;

        foreach ($actors as $actor) {
            $firstName = $actor["firstName"];
            $lastName = $actor["lastName"];
            $actorId = getActorId($firstName, $lastName) ?: addActor($firstName, $lastName);

            if ($actorId && !linkActorFilm($filmId, $actorId)) {
                echo "<p>Der Schauspieler $firstName $lastName konnte nicht mit dem Film verknüpft werden.</p>";
                return false;
            } else {
                echo "<p>Der Schauspieler $firstName $lastName wurde erfolgreich mit dem Film verknüpft.</p>";
            }
        }

        echo "<p>Der Film wurde erfolgreich hinzugefügt und Schauspieler wurden verknüpft.</p>";
        return true;
    } else {
        echo "<p>Der Film konnte nicht hinzugefügt werden.</p>";
        return false;
    }
}

/**
 * Behandelt die POST-Anfrage zur Aktualisierung der Informationen eines Schauspielers.
 *
 * @param array $actorData Die Daten des zu aktualisierenden Schauspielers.
 */
function handlePostRequest(array $actorData) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$actorData) {
            echo "<p>Der Schauspieler konnte nicht gefunden werden.</p>";
            exit();
        }
        $actorId = $_POST["actorId"];
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];

        if (updateActor($actorId, $firstName, $lastName)) {
            echo "<p>Der Schauspieler wurde erfolgreich aktualisiert.</p>";
            retrieveSingleActor($actorId);
        } else {
            echo "<p>Der Schauspieler konnte nicht aktualisiert werden.</p>";
        }
    }
}

/**
 * Behandelt das Entfernen der Verknüpfung eines Schauspielers von einem Film.
 *
 * @param int $actorId Die ID des zu entfernenden Schauspielers.
 */
function handleUnlinkActor(int $actorId) {
    if (isset($_GET['action']) && $_GET['action'] == 'unlink_actor' && isset($_GET['id'])) {
        $filmId = $_GET['id'];

        if (unlinkActorFilm($filmId, $actorId)) {
            header("Location: edit_actor.php?id=$actorId");
            echo "<p>Der Schauspieler wurde erfolgreich entfernt.</p>";
        } else {
            echo "<p>Der Schauspieler konnte nicht entfernt werden.</p>";
        }
    }
}