<?php
// Datenbankverbindungsparameter
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "filmdb";

// Erstellen einer neuen MySQLi-Verbindung
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    // Verbindung fehlgeschlagen, Fehlermeldung ausgeben und Skript beenden
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}