<?php
require_once 'db_connect.php';
require_once 'actor_functions.php';
require_once 'film_functions.php';

$actorId = $_GET["id"] ?? null;
$actorData = retrieveSingleActor($actorId);
$films = retrieveFilmsByActor($actorId);

handlePostRequest($actorData);
handleUnlinkActor($actorId);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Schauspieler bearbeiten</title>
</head>
<body>
    <h1>Schauspieler bearbeiten</h1>
    <?php if ($actorData) : ?>
    <form method="post">
        <input type="hidden" name="actorId" value="<?= $actorData['ActorID']; ?>">
        <h2>Schauspieler</h2>
        <div>
            <label for="firstName">Vorname</label>
            <input type="text" id="firstName" name="firstName" value="<?= $actorData['FirstName']; ?>" required> *
        </div>
        <br>
        <div>
            <label for="lastName">Nachname</label>
            <input type="text" id="lastName" name="lastName" value="<?= $actorData['LastName']; ?>" required> *
        </div>
        <br>
        <input type="submit" value="Speichern">
    </form>
    <?php else : ?>
    echo "<p>Der Schauspieler konnte nicht gefunden werden.</p>";
    <?php endif; ?>

    <table>
        <caption style="text-align:left"><h2>Filme des Schauspielers</h2></caption>
        <tr>
            <th>Titel</th>
            <th>Erscheinungsjahr</th>
            <th>Altersfreigabe</th>
            <th>Aktionen</th>
        </tr>
        <!-- Anzeige jedes Films in einer Tabellenzeile -->
        <?php foreach ($films as $film): ?>
            <tr>
                <td><?= $film['Title']; ?></td>
                <td><?= $film['ReleaseYear']; ?></td>
                <td><?= "FSK " . $film['AgeLimit']; ?></td>
                <td>
                    <a href="delete_film.php?id=<?= $film['FilmID']; ?>"
                       onclick="return confirm('Sind Sie sicher, dass Sie diesen Film löschen möchten?');">Entfernen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>