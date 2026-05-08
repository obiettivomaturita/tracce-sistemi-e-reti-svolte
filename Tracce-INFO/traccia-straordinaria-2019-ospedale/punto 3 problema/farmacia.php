<?php
session_start();
require "db_connection.php";

if (!isset($_SESSION["Ruolo"]) || $_SESSION["Ruolo"] != "FARMACIA") {
    die("Accesso riservato alla farmacia.");
}

$data = $_POST["data"];

$query = "SELECT 
            R.Nome,
            F.Nome,
            COUNT(*) AS Quantita
          FROM PRESCRIVERE P
          JOIN VISITA V ON P.IdV = V.IdV
          JOIN REPARTO R ON V.IdR = R.IdR
          JOIN FARMACO F ON P.IdF = F.IdF
          WHERE ? BETWEEN P.DataI AND P.DataF
          GROUP BY R.Nome, F.Nome
          ORDER BY R.Nome, F.Nome";

$stmt = $connection->prepare($query);
$stmt->bind_param("s", $data);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Elenco giornaliero</title></head>";
echo "<body>";

echo "<h2>Elenco farmaci da preparare il ".$data."</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Reparto</th><th>Farmaco</th><th>Quantità</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["Reparto"]."</td>";
        echo "<td>".$row["Farmaco"]."</td>";
        echo "<td>".$row["Quantita"]."</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Nessun farmaco previsto nelle prescrizioni per la data selezionata.</p>";
}

echo "<p><a href='farmacia_form.html'>Nuova ricerca</a></p>";
echo "<p><a href='logout.php'>Logout</a></p>";

echo "</body></html>";

$stmt->close();
$connection->close();
?>
