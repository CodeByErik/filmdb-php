<?php
global $conn;

require_once 'db_connect.php';
require_once 'film_functions.php';
require_once 'actor_functions.php';

/**
 * Überprüft, ob das Formular per POST-Methode gesendet wurde.
 * Wenn ja, werden die Filmdaten und Schauspieler aus dem Formular abgerufen
 * und die Funktion `addFilmWithActors` aufgerufen, um den Film und die Schauspieler
 * zur Datenbank hinzuzufügen.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $releaseYear = $_POST["year"];
    $ageLimit = $_POST["ageLimit"];
    $actors = $_POST["actor"];

    addFilmWithActors($title, $releaseYear, $ageLimit, $actors);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Film-Datenbank</title>
</head>
<body>
<h1>Film-Datenbank</h1>
<h2>Neuen Film hinzufügen</h2>

<!-- Formular zum Hinzufügen eines neuen Films -->
<form method="post" id="filmForm">
    <h3>Film</h3>
    <div>
        <label for="title">Titel</label>
        <input type="text" id="title" name="title" required> *
    </div>
    <br>
    <div>
        <label for="year">Erscheinungsjahr</label>
        <input type="number" id="year" name="year" min="1900" max="2100" required> *
    </div>
    <br>
    <div>
        <label for="ageLimit">Altersfreigabe</label>
        <select id="ageLimit" name="ageLimit" required>
            <option value="">Bitte wählen</option>
            <option value="0">FSK 0</option>
            <option value="6">FSK 6</option>
            <option value="12">FSK 12</option>
            <option value="16">FSK 16</option>
            <option value="18">FSK 18</option>
        </select> *
    </div>
    <h3>Schauspieler</h3>
    <div id="actor-container">
        <div class="actor">
            <label>Vorname
                <input type="text" name="actor[0][firstName]" required> *
            </label>
            <label>Nachname
                <input type="text" name="actor[0][lastName]" required> *
            </label>
        </div>
    </div>
    <br>
    <button type="button" onclick="addActor()">Weitere Schauspieler hinzufügen</button>
    <button type="button" onclick="removeActor()" id="removeActorButton" style="display:none">Schauspieler entfernen</button><br><br>
    <div>
        <input type="submit" value="Film zur Datenbank hinzufügen">
    </div>
    <small>Felder markiert mit * sind Pflichtfelder.</small>
</form><br>

<a href="index.php">Zurück zur Übersicht</a>

<script>
    let actorId = 1;

    /**
     * Fügt ein neues Schauspieler-Eingabefeld zum Formular hinzu.
     * Zeigt den "Schauspieler entfernen" Button an, wenn mehr als ein Schauspieler vorhanden ist.
     */
    function addActor() {
        const container = document.getElementById('actor-container');
        const div = document.createElement('div');
        div.className = 'actor';
        div.innerHTML = `
                <label>Vorname
                <input type="text" name="actor[${actorId}][firstName]" required> *
                </label>
                <label>Nachname
                <input type="text" name="actor[${actorId}][lastName]" required> *
                </label>
                `;
        container.appendChild(div);
        actorId++;

        document.getElementById('removeActorButton')["style"].display = 'inline';
    }

    /**
     * Entfernt das letzte Schauspieler-Eingabefeld aus dem Formular.
     * Versteckt den "Schauspieler entfernen" Button, wenn nur noch ein Schauspieler vorhanden ist.
     */
    function removeActor() {
        const container = document.getElementById('actor-container');
        if (container.children.length > 1) {
            container.removeChild(container.lastChild);
            actorId--;
            if (container.children.length === 1) {
                document.getElementById('removeActorButton')["style"].display = 'none';
            }
        }
    }
</script>
</body>
</html>