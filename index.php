<?php
require_once 'db_connect.php'; // Stellt die Verbindung zur Datenbank her
require_once 'film_functions.php'; // Enthält Funktionen zur Verwaltung der Filme

$films = retrieveFilms(); // Ruft alle Filme aus der Datenbank ab
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Film-Datenbank</title>
</head>
<body>
    <table class="table-80">
        <caption style="text-align:left"><h1>Film-Datenbank</h1></caption>
        <tr>
            <th>Titel</th>
            <th>Erscheinungsjahr</th>
            <th>Altersfreigabe</th>
            <th>Schauspieler</th>
            <th>Aktionen</th>
        </tr>
        <!-- Anzeige jedes Films in einer Tabellenzeile -->
        <?php foreach ($films as $film): ?>
            <tr>
                <td><?= $film['Title']; ?></td>
                <td><?= $film['ReleaseYear']; ?></td>
                <td><?= "FSK " . $film['AgeLimit']; ?></td>
                <td><?= $film['Actors']; ?></td>
                <td>
                    <a href="edit_film.php?id=<?= $film['FilmID']; ?>">Bearbeiten</a>
                    <a href="delete_film.php?id=<?= $film['FilmID']; ?>"
                       onclick="return confirm('Sind Sie sicher, dass Sie diesen Film löschen möchten?');">Löschen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="add_film.php">Neuen Film hinzufügen</a>
</body>
</html>