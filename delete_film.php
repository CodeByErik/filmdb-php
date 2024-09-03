<?php
require_once 'db_connect.php'; // Stellt die Verbindung zur Datenbank her
require_once 'film_functions.php'; // Enthält Funktionen zur Verwaltung der Filme

// Überprüft, ob eine Film-ID in der Anfrage vorhanden ist
if (isset($_GET["id"])) {
    $filmId = $_GET["id"]; // ID des zu löschenden Films

    // Versucht, den Film zu löschen und leitet zur Startseite weiter, wenn erfolgreich
    if (deleteFilm($filmId)) {
        header("Location: index.php"); // Weiterleitung zur Startseite nach erfolgreichem Löschen
        exit;
    } else {
        echo "<p>Der Film konnte nicht gelöscht werden.</p>"; // Fehlermeldung bei fehlgeschlagenem Löschvorgang
    }
} else {
    echo "<p>Ungültige Anfrage.</p>"; // Fehlermeldung bei ungültiger Anfrage
}