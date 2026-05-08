<?php
require "db_connection.php";

$dataInizio = $_POST["data_inizio"];
$dataFine = $_POST["data_fine"];

$query = "SELECT 
            AVG(DATEDIFF(DataChiusura, DataApertura)) AS TempoMedioGiorni
          FROM TICKET
          WHERE Stato = 'CHIUSO'
            AND DataChiusura BETWEEN ? AND ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $dataInizio, $dataFine);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Statistiche</title></head>";
echo "<body>";

echo "<h2>Risultato statistica</h2>";

$row = $result->fetch_assoc();
if ($row["TempoMedioGiorni"] !== null) {
    echo "<p>Tempo medio di chiusura: ".$row["TempoMedioGiorni"]." giorni</p>";
} else {
    echo "<p>Nessun ticket chiuso nel periodo selezionato.</p>";
}

echo "</body></html>";

$stmt->close();
$connection->close();
?>
