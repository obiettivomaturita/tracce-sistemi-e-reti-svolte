<?php
require "db_connection.php";

$idAcc = isset($_POST["idAcc"]) ? intval($_POST["idAcc"]) : 0;
$voto = isset($_POST["voto"]) ? intval($_POST["voto"]) : 0;
$commento = isset($_POST["commento"]) ? $_POST["commento"] : "";

if ($idAcc <= 0) die("IdAcc mancante.");
if ($voto < 1 || $voto > 5) die("Voto non valido (deve essere 1..5).");


$qAcc = "SELECT DataAccesso, OraF, CodP
         FROM ACCESSO
         WHERE IdAcc = ?";
$stmtAcc = $connection->prepare($qAcc);
if (!$stmtAcc) die("Errore prepare: " . $connection->error);

$stmtAcc->bind_param("i", $idAcc);
$stmtAcc->execute();
$resAcc = $stmtAcc->get_result();

if ($resAcc->num_rows == 0) die("Accesso inesistente.");

$acc = $resAcc->fetch_assoc();
$dataAccesso = $acc["DataAccesso"];
$oraF = $acc["OraF"];
$codP = $acc["CodP"];

if ($dataAccesso != date("Y-m-d")) die("Accesso non valido (giorno diverso).");
if (date("H:i:s") > $oraF) die("Sessione scaduta: non è possibile valutare.");

$qCheck = "SELECT IdVal FROM VALUTAZIONE WHERE IdAcc = ?";
$stmtCheck = $connection->prepare($qCheck);
if (!$stmtCheck) die("Errore prepare: " . $connection->error);

$stmtCheck->bind_param("i", $idAcc);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) die("Valutazione già inserita per questo accesso.");


$dataVal = date("Y-m-d");

$qIns = "INSERT INTO VALUTAZIONE (IdAcc, Voto, Commento, DataValutazione)
         VALUES (?, ?, ?, ?)";
$stmtIns = $connection->prepare($qIns);
if (!$stmtIns) die("Errore prepare: " . $connection->error);

$stmtIns->bind_param("iiss", $idAcc, $voto, $commento, $dataVal);

if (!$stmtIns->execute()) die("Errore INSERT: " . $stmtIns->error);


echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Valutazione inserita</title></head>";
echo "<body>";

echo "<h2>Valutazione inserita correttamente</h2>";
echo "<p>POI: ".$codP."</p>";
echo "<p>Voto: ".$voto."</p>";
echo "<p>Commento: ".$commento."</p>";

echo "<p><a href='media_voti.php'>Vai alla media voti</a></p>";

echo "</body></html>";

$stmtAcc->close();
$stmtCheck->close();
$stmtIns->close();
$connection->close();
?>

