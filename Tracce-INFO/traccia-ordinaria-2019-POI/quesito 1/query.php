<?php
require "db_connection.php";

$sql = "SELECT p.CodP, p.Indirizzo, AVG(v.Voto) AS MediaVoti
        FROM POI p
        INNER JOIN ACCESSO a ON a.CodP = p.CodP
        INNER JOIN VALUTAZIONE v ON v.IdAcc = a.IdAcc
        GROUP BY p.CodP, p.Indirizzo
        ORDER BY MediaVoti DESC";

$stmt = $connection->prepare($sql);
if (!$stmt) die("Errore prepare: " . $connection->error);

$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Media voti POI</title></head>";
echo "<body>";

echo "<h2>Media voti per POI</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<thead><tr><th>Codice POI</th><th>Indirizzo</th><th>Media voti</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["CodP"]."</td>";
        echo "<td>".$row["Indirizzo"]."</td>";
        echo "<td>".number_format($row["MediaVoti"], 2)."</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>Nessuna valutazione disponibile.</p>";
}

echo "</body></html>";

$stmt->close();
$connection->close();
?>
