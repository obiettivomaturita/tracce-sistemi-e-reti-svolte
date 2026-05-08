<?php
require "db_connection.php";
session_start();

if (!isset($_SESSION["IdU"])) {
    die("Accesso non autorizzato.");
}

$ruolo = $_SESSION["Ruolo"];
if ($ruolo != "Redattore" && $ruolo != "Direttore" && $ruolo != "Giornalista") {
    die("Permesso negato: ruolo non autorizzato.");
}

$idU = $_SESSION["IdU"];
$titolo = $_POST["Titolo"];
$descrizione = $_POST["Descrizione"];

if (empty($titolo) || empty($descrizione)) {
    die("Tutti i campi sono obbligatori.");
}

$query = "
INSERT INTO ARTICOLO (Titolo, DataP, Descrizione, IdU)
VALUES (?, NOW(), ?, ?)
";

$stmt = $connection->prepare($query);
if (!$stmt) {
    die("Errore nella preparazione della query: " . $connection->error);
}

$stmt->bind_param("ssi", $titolo, $descrizione, $idU);

if ($stmt->execute()) {
    echo "<p>Articolo inserito correttamente.</p>";
    echo "<p><a href='area_riservata.php'>Torna all'area riservata</a></p>";
} else {
    echo "<p>Errore nell'inserimento: {$stmt->error}</p>";
}

$stmt->close();
$connection->close();
?>