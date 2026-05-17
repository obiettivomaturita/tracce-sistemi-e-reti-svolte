<?php
session_start();
require "db_connection.php";

if (!isset($_SESSION["Ruolo"]) || $_SESSION["Ruolo"] != "FARMACIA") {
    die("Accesso riservato alla farmacia.");
}

$data = $_POST["data"];
$idReparto = $_POST["idReparto"];

$query = "SELECT 
            R.Nome AS Reparto,
            R.Piano AS Piano,
            R.Specializzazione AS Specializzazione,
            F.Nome AS Farmaco,
            C.Dosaggio AS Dosaggio,
            C.DataI AS DataInizio,
            C.DataF AS DataFine,
            P.IdPr AS Prescrizione,
            PA.Nome AS NomePaziente,
            PA.Cognome AS CognomePaziente
          FROM PRESCRIZIONE P
          INNER JOIN VISITA V ON P.IdV = V.IdV
          INNER JOIN PAZIENTE PA ON V.IdP = PA.IdP
          INNER JOIN REPARTO R ON V.IdR = R.IdR
          INNER JOIN CONTENERE C ON P.IdPr = C.IdPr
          INNER JOIN FARMACO F ON C.IdF = F.IdF
          WHERE ? BETWEEN C.DataI AND C.DataF
          AND R.IdR = ?
          ORDER BY F.Nome, PA.Cognome, PA.Nome";

$stmt = $connection->prepare($query);
$stmt->bind_param("si", $data, $idReparto);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Elenco farmaci</title></head>";
echo "<body>";

echo "<h2>Elenco farmaci previsti per il giorno ".$data."</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Reparto</th>";
    echo "<th>Piano</th>";
    echo "<th>Specializzazione</th>";
    echo "<th>Farmaco</th>";
    echo "<th>Dosaggio</th>";
    echo "<th>Data inizio terapia</th>";
    echo "<th>Data fine terapia</th>";
    echo "<th>Prescrizione</th>";
    echo "<th>Paziente</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["Reparto"]."</td>";
        echo "<td>".$row["Piano"]."</td>";
        echo "<td>".$row["Specializzazione"]."</td>";
        echo "<td>".$row["Farmaco"]."</td>";
        echo "<td>".$row["Dosaggio"]."</td>";
        echo "<td>".$row["DataInizio"]."</td>";
        echo "<td>".$row["DataFine"]."</td>";
        echo "<td>".$row["Prescrizione"]."</td>";
        echo "<td>".$row["NomePaziente"]." ".$row["CognomePaziente"]."</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Nessun farmaco previsto per la data e il reparto selezionati.</p>";
}

echo "<br>";
echo "<a href='farmacia_form.html'>Nuova ricerca</a>";
echo "<br><br>";
echo "<a href='logout.php'>Logout</a>";

echo "</body>";
echo "</html>";

$stmt->close();
$connection->close();
?>
