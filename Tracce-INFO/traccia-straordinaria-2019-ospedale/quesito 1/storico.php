<?php
session_start();
require "db_connection.php";

if (!isset($_SESSION["Ruolo"]) || $_SESSION["Ruolo"] != "MEDICO") {
    die("Accesso riservato ai medici.");
}

$idP = $_POST["idP"];

$query = "SELECT 
            DataV,
            OraV,
            PressioneMin,
            PressioneMax,
            Temperatura,
            FrequenzaCardiaca
          FROM VISITA
          WHERE IdP = ?
          ORDER BY DataV, OraV";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $idP);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Storico paziente</title></head>";
echo "<body>";

echo "<h2>Storico dati biometrici - Paziente ".$idP."</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Pressione Min</th>
            <th>Pressione Max</th>
            <th>Temperatura</th>
            <th>Frequenza cardiaca</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["DataV"]."</td>";
        echo "<td>".$row["OraV"]."</td>";
        echo "<td>".$row["PressioneMin"]."</td>";
        echo "<td>".$row["PressioneMax"]."</td>";
        echo "<td>".$row["Temperatura"]."</td>";
        echo "<td>".$row["FrequenzaCardiaca"]."</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Nessuna visita registrata per il paziente selezionato.</p>";
}

echo "<p><a href='storico_form.html'>Nuova ricerca</a></p>";
echo "<p><a href='logout.php'>Logout</a></p>";

echo "</body></html>";

$stmt->close();
$connection->close();
?>
