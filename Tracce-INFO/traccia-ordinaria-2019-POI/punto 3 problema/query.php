<?php
require "db_connection.php";

$idAcc = isset($_GET["idAcc"]) ? $_GET["idAcc"] : 0;
if ($idAcc <= 0) die("IdAcc mancante.");

$queryAcc = "SELECT a.CodP, a.DataAccesso, a.OraF, b.TipoTariffa
             FROM ACCESSO a
             INNER JOIN BIGLIETTO b ON a.CodB = b.CodB
             WHERE a.IdAcc = ?";

$stmtAcc = $connection->prepare($queryAcc);
if (!$stmtAcc) die("Errore prepare: " . $connection->error);

$stmtAcc->bind_param("i", $idAcc);
$stmtAcc->execute();
$resAcc = $stmtAcc->get_result();

if ($resAcc->num_rows == 0) die("Accesso inesistente.");

$acc = $resAcc->fetch_assoc();
$codP = intval($acc["CodP"]);
$dataAccesso = $acc["DataAccesso"];
$oraF = $acc["OraF"];
$tipoTariffa = $acc["TipoTariffa"];

if ($dataAccesso != date("Y-m-d")) die("Sessione non valida (giorno diverso).");
if (date("H:i:s") > $oraF) die("Sessione scaduta. Scansiona di nuovo il QR.");


$queryPag = "SELECT p.IdPag, poi.Tipo, poi.Indirizzo
             FROM PAGINA_MULTIMEDIALE p
             INNER JOIN POI poi ON poi.CodP = p.CodP
             WHERE p.CodP = ?
               AND p.TipoPagina = 'BASE'";

$stmtPag = $connection->prepare($queryPag);
if (!$stmtPag) die("Errore prepare: " . $connection->error);

$stmtPag->bind_param("i", $codP);
$stmtPag->execute();
$resPag = $stmtPag->get_result();

if ($resPag->num_rows == 0) die("Pagina BASE non trovata per il POI.");

$pag = $resPag->fetch_assoc();
$idPag = intval($pag["IdPag"]);


$queryVid = "SELECT Durata, Lingua, UrlVideo
             FROM VIDEO
             WHERE IdPag = ?";

$stmtVid = $connection->prepare($queryVid);
if (!$stmtVid) die("Errore prepare: " . $connection->error);

$stmtVid->bind_param("i", $idPag);
$stmtVid->execute();
$video = $stmtVid->get_result()->fetch_assoc();


$queryImg = "SELECT Didascalia, Descrizione, UrlImg
             FROM IMMAGINE
             WHERE IdPag = ?
             ORDER BY IDM
             LIMIT 3";

$stmtImg = $connection->prepare($queryImg);
if (!$stmtImg) die("Errore prepare: " . $connection->error);

$stmtImg->bind_param("i", $idPag);
$stmtImg->execute();
$resImg = $stmtImg->get_result();


echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Pagina BASE</title></head>";
echo "<body>";

echo "<h2>Pagina multimediale BASE</h2>";
echo "<p><b>POI:</b> ".$codP." (".$pag["Tipo"].")</p>";
echo "<p><b>Indirizzo:</b> ".$pag["Indirizzo"]."</p>";
echo "<p><b>Tariffa:</b> ".$tipoTariffa." - <b>Valida fino:</b> ".$oraF."</p>";

echo "<hr>";

echo "<h3>Video</h3>";
if ($video && !empty($video["UrlVideo"])) {
    echo "<p>Lingua: ".$video["Lingua"]." - Durata: ".$video["Durata"]."</p>";
    echo "<video controls width='640'>";
    echo "<source src='".$video["UrlVideo"]."' type='video/mp4'>";
    echo "Il browser non supporta il video.";
    echo "</video>";
} else {
    echo "<p>Nessun video disponibile.</p>";
}

echo "<hr>";

echo "<h3>Immagini (max 3)</h3>";
if ($resImg->num_rows > 0) {
    while ($img = $resImg->fetch_assoc()) {
        if (!empty($img["UrlImg"])) {
            echo "<img src='".$img["UrlImg"]."' width='480'><br>";
        }
        echo "<b>".$img["Didascalia"]."</b><br>";
        echo "<p>".$img["Descrizione"]."</p>";
        echo "</div>";
    }
} else {
    echo "<p>Nessuna immagine disponibile.</p>";
}

echo "</body></html>";

$stmtAcc->close();
$stmtPag->close();
$stmtVid->close();
$stmtImg->close();
$connection->close();
?>
