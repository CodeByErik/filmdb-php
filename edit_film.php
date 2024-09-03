<?php
global $conn;
require_once 'db_connect.php';
require_once 'actor_functions.php';
require_once 'film_functions.php';

// Film-ID aus der URL abrufen
$filmId = $_GET["id"] ?? null;
// Filmdaten abrufen
$filmData = retrieveFilm($filmId);

// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filmId = $_POST["filmId"];
    $title = $_POST["title"];
    $releaseYear = $_POST["year"];
    $ageLimit = $_POST["ageLimit"];
    $fskId = getFskId($ageLimit);

    // Film aktualisieren
    if (updateFilm($filmId, $title, $releaseYear, $fskId)) {
        echo "<p>Der Film wurde erfolgreich aktualisiert.</p>";
        $filmData = retrieveFilm($filmId);
    } else {
        echo "<p>Der Film konnte nicht aktualisiert werden.</p>";
    }
}

// Überprüfen, ob ein Schauspieler von einem Film entfernt werden soll
if (isset($_GET['action']) && $_GET['action'] == 'unlink_actor' && isset($_GET['actor_id']) && isset($_GET['film_id'])) {
    $actorId = $_GET['actor_id'];
    $filmId = $_GET['film_id'];

    // Schauspieler von Film entfernen
    if (unlinkActorFilm($filmId, $actorId)) {
        header("Location: edit_film.php?id=$filmId");
        echo "<p>Der Schauspieler wurde erfolgreich entfernt.</p>";
    } else {
        echo "<p>Der Schauspieler konnte nicht entfernt werden.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Film bearbeiten</title>
</head>
<body>
<h1>Film bearbeiten</h1>

<?php if ($filmData) : ?>
    <!-- Formular zur Bearbeitung des Films -->
    <form method="post">
        <input type="hidden" name="filmId" value="<?= $filmData["FilmID"] ?>">

        <div>
            <label for="title">Titel:</label>
            <input type="text" id="title" name="title" value="<?= $filmData["Title"] ?>">
        </div>
        <br>
        <div>
            <label for="year">Erscheinungsjahr:</label>
            <input type="number" id="year" name="year" min="1900" max="2100" value="<?= $filmData["ReleaseYear"] ?>"><br>
        </div>
        <br>
        <div>
            <label for="ageLimit">Altersfreigabe:</label>
            <select id="ageLimit" name="ageLimit">
                <option value="0" <?= $filmData["FskID"] == 1 ? "selected" : "" ?>>FSK 0</option>
                <option value="6" <?= $filmData["FskID"] == 2 ? "selected" : "" ?>>FSK 6</option>
                <option value="12" <?= $filmData["FskID"] == 3 ? "selected" : "" ?>>FSK 12</option>
                <option value="16" <?= $filmData["FskID"] == 4 ? "selected" : "" ?>>FSK 16</option>
                <option value="18" <?= $filmData["FskID"] == 5 ? "selected" : "" ?>>FSK 18</option>
            </select>
        </div>
        <br>

        <div>
            <button type="button" onclick="window.location.href='index.php'">Zurück zur Übersicht</button>
            <input type="submit" value="Film aktualisieren">
        </div>
    </form>

<?php else : ?>
    <p>Der Film konnte nicht gefunden werden.</p>
<?php endif; ?>

<h2>Schauspieler </h2>
<?php if ($filmData) : ?>
    <!-- Tabelle der Schauspieler, die im Film mitspielen -->
    <table>
        <tr>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Aktionen</th>
        </tr>
        <?php
        $actors = retrieveActors($filmData["FilmID"]);
        foreach ($actors as $actor) : ?>
            <tr>
                <td><?= $actor["FirstName"] ?></td>
                <td><?= $actor["LastName"] ?></td>
                <td>
                    <a href="edit_actor.php?id=<?= $actor['ActorID']; ?>">Bearbeiten</a>
                    <a href="?action=unlink_actor&actor_id=<?= $actor['ActorID']; ?>&film_id=<?= $filmId; ?>"
                       onclick="return confirm('Sind Sie sicher, dass Sie diesen Schauspieler löschen möchten?');">Entfernen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<form id="removeActorForm" method="post">
    <input type="hidden" name="action" value="removeActor">
    <input type="hidden" name="actorId" id="actorId">
    <input type="hidden" name="filmId" id="filmId">
</form>
</body>
</html>