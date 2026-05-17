<?php
session_start();
require "db_connection.php";

if (!isset($_SESSION["Ruolo"]) || $_SESSION["Ruolo"] != "MEDICO") {
    die("Accesso riservato ai medici.");
}

$idP = $_POST["idP"];

$query = "SELECT 
            V.Data AS DataVisita,
            V.Ora AS OraVisita,
            V.PressioneMinima,
            V.PressioneMassima,
            V.Temperatura,
            V.FrequenzaCardiaca,
            V.Motivazione,
            V.Annotazioni,
            R.Nome AS Reparto
          FROM VISITA V
          INNER JOIN REPARTO R ON V.IdR = R.IdR
          WHERE V.IdP = ?
          ORDER BY V.Data, V.Ora";

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
    echo "<tr>";
    echo "<th>Data</th>";
    echo "<th>Ora</th>";
    echo "<th>Pressione minima</th>";
    echo "<th>Pressione massima</th>";
    echo "<th>Temperatura</th>";
    echo "<th>Frequenza cardiaca</th>";
    echo "<th>Motivazione</th>";
    echo "<th>Annotazioni</th>";
    echo "<th>Reparto</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["DataVisita"]."</td>";
        echo "<td>".$row["OraVisita"]."</td>";
        echo "<td>".$row["PressioneMinima"]."</td>";
        echo "<td>".$row["PressioneMassima"]."</td>";
        echo "<td>".$row["Temperatura"]."</td>";
        echo "<td>".$row["FrequenzaCardiaca"]."</td>";
        echo "<td>".$row["Motivazione"]."</td>";
        echo "<td>".$row["Annotazioni"]."</td>";
        echo "<td>".$row["Reparto"]."</td>";
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